<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class companyPermissionImport implements ToCollection
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // public function sheets(): array
    // {
    //     $sheets = [];
    //     foreach(){

    //     }

    //     return $sheets;
    // }
}
