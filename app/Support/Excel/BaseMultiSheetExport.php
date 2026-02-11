<?php

namespace App\Support\Excel;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

abstract class BaseMultiSheetExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Define sheets for export
     */
    abstract public function sheets(): array;
}

