<?php

namespace App\Jobs;

use App\Support\Excel\TourImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ImportToursJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 300;

    public function __construct(
        private readonly string $filePath,
    ) {
    }

    public function handle(TourImport $tourImport): void
    {
        Log::channel('daily')->info('[TourImport] Job started', [
            'file' => $this->filePath,
            'job_id' => $this->job?->getJobId(),
            'attempt' => $this->attempts(),
        ]);

        try {
            $fullPath = Storage::disk('local')->path($this->filePath);

            if (!file_exists($fullPath)) {
                Log::channel('daily')->error('[TourImport] File not found', [
                    'full_path' => $fullPath,
                    'file' => $this->filePath,
                ]);
                return;
            }

            Log::channel('daily')->info('[TourImport] Starting Excel import', [
                'full_path' => $fullPath,
                'file_size' => filesize($fullPath),
            ]);

            Excel::import($tourImport, $fullPath);

            $result = $tourImport->getResult();

            // Log summary
            Log::channel('daily')->info('[TourImport] Import completed', [
                'tours_imported' => $result['tours']['imported'],
                'tours_skipped' => $result['tours']['skipped'],
                'itineraries_imported' => $result['itineraries']['imported'],
                'itineraries_skipped' => $result['itineraries']['skipped'],
                'departures_imported' => $result['departures']['imported'],
                'departures_skipped' => $result['departures']['skipped'],
                'total_errors' => count($result['errors']),
            ]);

            // Log individual errors for debugging
            if (!empty($result['errors'])) {
                Log::channel('daily')->warning('[TourImport] Rows with errors', [
                    'errors' => $result['errors'],
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('daily')->error('[TourImport] Job failed with exception', [
                'file' => $this->filePath,
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'line' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        } finally {
            // Clean up the temporary import file
            Storage::disk('local')->delete($this->filePath);
            Log::channel('daily')->info('[TourImport] Temp file cleaned up', ['file' => $this->filePath]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?\Throwable $exception): void
    {
        Log::channel('daily')->error('[TourImport] Job permanently failed', [
            'file' => $this->filePath,
            'error' => $exception?->getMessage(),
            'class' => $exception ? get_class($exception) : null,
            'trace' => $exception?->getTraceAsString(),
        ]);

        // Ensure file cleanup even on failure
        Storage::disk('local')->delete($this->filePath);
    }
}
