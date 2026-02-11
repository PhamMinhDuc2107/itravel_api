<?php

namespace App\Support\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

abstract class BaseExcelExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Define column headings
     */
    abstract public function headings(): array;

    /**
     * Map data to columns
     *
     * @param mixed $row
     */
    abstract public function map($row): array;

    /**
     * Define sheet title
     */
    abstract public function title(): string;

    /**
     * Apply default header styles (can be overridden)
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $this->getHeaderColor()],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Header background color (override if needed)
     */
    protected function getHeaderColor(): string
    {
        return 'E2EFDA'; // Default green
    }

    /**
     * Format currency
     */
    protected function formatCurrency(float|int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }

    /**
     * Format date
     */
    protected function formatDate(?\DateTimeInterface $date, string $format = 'd/m/Y'): string
    {
        return $date ? $date->format($format) : 'N/A';
    }

    /**
     * Format datetime
     */
    protected function formatDateTime(?\DateTimeInterface $date): string
    {
        return $this->formatDate($date, 'd/m/Y H:i');
    }
}
