<?php

namespace App\Support\Excel;

use App\Enum\TourDepartureStatusEnum;
use App\Enum\TourStatusEnum;
use App\Repository\Contract\TourRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TourImport implements WithMultipleSheets
{
    private int $importedCount = 0;
    private int $skippedCount = 0;
    private array $errors = [];

    /** @var array<string, int> tour_code => tour_id mapping */
    private array $tourCodeMap = [];

    private int $itineraryImported = 0;
    private int $itinerarySkipped = 0;
    private int $departureImported = 0;
    private int $departureSkipped = 0;

    public function __construct(
        private readonly TourRepositoryInterface $tourRepository,
    ) {
    }

    public function sheets(): array
    {
        return [
            0 => new TourSheetImport($this),
            1 => new TourItinerarySheetImport($this),
            2 => new TourDepartureSheetImport($this),
        ];
    }

    // ── Tour processing ──────────────────────────────────────

    public function processTourRow(int $rowNumber, Collection $row): void
    {
        $data = $this->mapTourData($row);

        $validator = Validator::make($data, [
            'code' => ['required', 'string', 'max:50', 'unique:tours,code'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tours,slug'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'departure_location_id' => ['required', 'integer', 'exists:locations,id'],
            'destination_location_id' => ['required', 'integer', 'exists:locations,id'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'duration_nights' => ['nullable', 'integer', 'min:0'],
            'price_adult' => ['required', 'numeric', 'min:0'],
            'price_child' => ['nullable', 'numeric', 'min:0'],
            'price_infant' => ['nullable', 'numeric', 'min:0'],
            'excerpt' => ['nullable', 'string'],
            'overview' => ['nullable', 'string'],
            'policy' => ['nullable', 'string'],
            'included' => ['nullable', 'string'],
            'excluded' => ['nullable', 'string'],
            'status' => ['nullable', 'integer', 'in:0,1,2,3'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            $this->skippedCount++;
            $this->errors[] = ['sheet' => 'Tours', 'row' => $rowNumber, 'errors' => $validator->errors()->toArray()];
            return;
        }

        try {
            $tour = $this->tourRepository->create($validator->validated());
            $this->tourCodeMap[$data['code']] = $tour->id;
            $this->importedCount++;
        } catch (\Throwable $e) {
            $this->skippedCount++;
            $this->errors[] = ['sheet' => 'Tours', 'row' => $rowNumber, 'errors' => ['general' => [$e->getMessage()]]];
            Log::error("Tour import failed at row {$rowNumber}", ['error' => $e->getMessage()]);
        }
    }

    // ── Itinerary processing ─────────────────────────────────

    public function processItineraryRow(int $rowNumber, Collection $row): void
    {
        $tourCode = trim((string) ($row['tour_code'] ?? ''));

        if (!isset($this->tourCodeMap[$tourCode])) {
            $this->itinerarySkipped++;
            $this->errors[] = [
                'sheet' => 'Lịch trình',
                'row' => $rowNumber,
                'errors' => ['tour_code' => ["Tour code '{$tourCode}' not found in imported tours"]],
            ];
            return;
        }

        $data = [
            'tour_id' => $this->tourCodeMap[$tourCode],
            'day_number' => (int) ($row['day_number'] ?? 1),
            'position' => (int) ($row['position'] ?? 0),
            'title' => $this->nullableString($row['title'] ?? null),
            'content' => $this->nullableString($row['content'] ?? null),
        ];

        $validator = Validator::make($data, [
            'tour_id' => ['required', 'integer'],
            'day_number' => ['required', 'integer', 'min:1'],
            'position' => ['nullable', 'integer', 'min:0'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            $this->itinerarySkipped++;
            $this->errors[] = ['sheet' => 'Lịch trình', 'row' => $rowNumber, 'errors' => $validator->errors()->toArray()];
            return;
        }

        try {
            DB::table('tour_itineraries')->insert(array_merge($validator->validated(), [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $this->itineraryImported++;
        } catch (\Throwable $e) {
            $this->itinerarySkipped++;
            $this->errors[] = ['sheet' => 'Lịch trình', 'row' => $rowNumber, 'errors' => ['general' => [$e->getMessage()]]];
        }
    }

    // ── Departure processing ────────────────────────────────

    public function processDepartureRow(int $rowNumber, Collection $row): void
    {
        $tourCode = trim((string) ($row['tour_code'] ?? ''));

        if (!isset($this->tourCodeMap[$tourCode])) {
            $this->departureSkipped++;
            $this->errors[] = [
                'sheet' => 'Ngày khởi hành',
                'row' => $rowNumber,
                'errors' => ['tour_code' => ["Tour code '{$tourCode}' not found in imported tours"]],
            ];
            return;
        }

        $data = [
            'tour_id' => $this->tourCodeMap[$tourCode],
            'start_date' => $this->nullableString($row['start_date'] ?? null),
            'price_adult' => (float) ($row['price_adult'] ?? 0),
            'original_price_adult' => (float) ($row['original_price_adult'] ?? 0),
            'price_child' => (float) ($row['price_child'] ?? 0),
            'original_price_child' => (float) ($row['original_price_child'] ?? 0),
            'price_infant' => (float) ($row['price_infant'] ?? 0),
            'original_price_infant' => (float) ($row['original_price_infant'] ?? 0),
            'stock' => (int) ($row['stock'] ?? 0),
            'booked' => (int) ($row['booked'] ?? 0),
            'status' => $this->nullableString($row['status'] ?? null) ?? TourDepartureStatusEnum::Available->value,
        ];

        $validator = Validator::make($data, [
            'tour_id' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'price_adult' => ['required', 'numeric', 'min:0'],
            'original_price_adult' => ['nullable', 'numeric', 'min:0'],
            'price_child' => ['nullable', 'numeric', 'min:0'],
            'original_price_child' => ['nullable', 'numeric', 'min:0'],
            'price_infant' => ['nullable', 'numeric', 'min:0'],
            'original_price_infant' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'booked' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:available,sold_out,closed'],
        ]);

        if ($validator->fails()) {
            $this->departureSkipped++;
            $this->errors[] = ['sheet' => 'Ngày khởi hành', 'row' => $rowNumber, 'errors' => $validator->errors()->toArray()];
            return;
        }

        try {
            DB::table('tour_departures')->insert(array_merge($validator->validated(), [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $this->departureImported++;
        } catch (\Throwable $e) {
            $this->departureSkipped++;
            $this->errors[] = ['sheet' => 'Ngày khởi hành', 'row' => $rowNumber, 'errors' => ['general' => [$e->getMessage()]]];
        }
    }

    // ── Result ───────────────────────────────────────────────

    public function getResult(): array
    {
        return [
            'tours' => ['imported' => $this->importedCount, 'skipped' => $this->skippedCount],
            'itineraries' => ['imported' => $this->itineraryImported, 'skipped' => $this->itinerarySkipped],
            'departures' => ['imported' => $this->departureImported, 'skipped' => $this->departureSkipped],
            'errors' => $this->errors,
        ];
    }

    // ── Helpers ──────────────────────────────────────────────

    private function mapTourData(Collection $row): array
    {
        $name = trim((string) ($row['name'] ?? ''));
        $slug = trim((string) ($row['slug'] ?? ''));

        return [
            'code' => trim((string) ($row['code'] ?? '')),
            'name' => $name,
            'slug' => $slug !== '' ? $slug : Str::slug($name),
            'category_id' => $this->nullableInt($row['category_id'] ?? null),
            'departure_location_id' => $this->nullableInt($row['departure_location_id'] ?? null),
            'destination_location_id' => $this->nullableInt($row['destination_location_id'] ?? null),
            'duration_days' => (int) ($row['duration_days'] ?? 1),
            'duration_nights' => (int) ($row['duration_nights'] ?? 0),
            'price_adult' => (float) ($row['price_adult'] ?? 0),
            'price_child' => (float) ($row['price_child'] ?? 0),
            'price_infant' => (float) ($row['price_infant'] ?? 0),
            'excerpt' => $this->nullableString($row['excerpt'] ?? null),
            'overview' => $this->nullableString($row['overview'] ?? null),
            'policy' => $this->nullableString($row['policy'] ?? null),
            'included' => $this->nullableString($row['included'] ?? null),
            'excluded' => $this->nullableString($row['excluded'] ?? null),
            'status' => (int) ($row['status'] ?? TourStatusEnum::Draft->value),
            'meta_title' => $this->nullableString($row['meta_title'] ?? null),
            'meta_description' => $this->nullableString($row['meta_description'] ?? null),
            'position' => (int) ($row['position'] ?? 0),
        ];
    }

    private function nullableInt(mixed $value): ?int
    {
        return ($value === null || $value === '') ? null : (int) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        return ($value === null || trim((string) $value) === '') ? null : trim((string) $value);
    }
}
