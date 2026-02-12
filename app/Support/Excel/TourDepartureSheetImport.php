<?php

namespace App\Support\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TourDepartureSheetImport implements ToCollection, WithHeadingRow
{
    public function __construct(
        private readonly TourImport $parent,
    ) {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            if ($row->filter()->isEmpty()) {
                continue;
            }

            $this->parent->processDepartureRow($index + 2, $row);
        }
    }
}
