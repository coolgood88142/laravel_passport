<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\CompanyPermissionSheet;

class CompanyPermissionExport implements WithMultipleSheets
{
    use Exportable;

    protected $companyPermissionDataArray;

    public function __construct(array $companyPermissionDataArray)
    {
        $this->companyPermissionDataArray = $companyPermissionDataArray;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        foreach($this->companyPermissionDataArray as $key => $companyPermissionData)
        {
            $sheets[] = new CompanyPermissionSheet($companyPermissionData, $key);
        }

        return $sheets;
    }
}
