<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CompanyPermissionExport;

class ExcelController extends Controller
{
    public function exportExcxel($companyPermissionDataArray, $filename)
    {
        Excel::store(new CompanyPermissionExport($companyPermissionDataArray), $filename);
    }
}
