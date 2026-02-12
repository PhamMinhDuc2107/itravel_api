<?php

namespace App\Support\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TourSheetImport implements ToCollection, WithHeadingRow
{
    public function __construct(
        private readonly TourImport $parent,
    ) {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            // Skip completely empty rows
            if ($row->filter()->isEmpty()) {
                continue;
            }

            $this->parent->processTourRow($index + 2, $row);
        }
    }
}
