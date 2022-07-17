<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Service\Sheets;
use App\Http\Services\GoogleSheetsServices;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Log;

class GoogleSheetsController extends Controller
{
    public function sendSpreadsheetsCompanyData(Request $request)
    {
        $queryCompany = $request->queryCompany == null ? '' : $request->queryCompany;
        $companyController = new CompanyController();
        $companyPermissionData = $companyController->createCompanyData($queryCompany);
        $values = [];
        //設定title
        $companyPermission = [
            '公司ID',
            '公司名稱',
            '產品ID',
            '產品名稱',
            '數量',
            '使用時段',
            '學員ID',
            '建立時間',
            '更新時間',
        ];

        $userPermission = [
            '學員ID',
            '學員姓名',
            '產品ID',
            '產品名稱',
            '使用時段',
            '建立時間',
            '更新時間',
        ];

        $userPermissionLog = [
            '學員ID',
            '學員姓名',
            '產品ID',
            '產品名稱',
            '使用時段',
            '建立時間',
            '更新時間',
        ];

        array_push($values, ['公司權益']);
        array_push($values, $companyPermission);

        foreach($companyPermissionData['companyPermission'] as $company){
            $array = [
                $company['company_id'],
                $company['company_name'],
                $company['product_id'],
                $company['product_name'],
                $company['amount'],
                $company['date_time'],
                $company['users_id'],
                $company['created_at'],
                $company['updated_at'],
            ];
            array_push($values, $array);
        }

        array_push($values, []);
        array_push($values, ['學員權益']);
        array_push($values, $userPermission);

        foreach($companyPermissionData['userPermission'] as $user){
            $array = [
                $user['user_id'],
                $user['user_name'],
                $user['product_id'],
                $user['product_name'],
                $user['date_time'],
                $user['created_at'],
                $user['updated_at'],
            ];
            array_push($values, $array);
        }

        array_push($values, []);
        array_push($values, ['學員權益變更記錄']);
        array_push($values, $userPermission);

        foreach($companyPermissionData['userPermissionLog'] as $user){
            $array = [
                $user['user_id'],
                $user['user_name'],
                $user['product_id'],
                $user['product_name'],
                $user['date_time'],
                $user['created_at'],
                $user['updated_at'],
            ];
            array_push($values, $array);
        }


        Log::info($values);

        $email = $request->queryEmail == null ? '' : $request->queryEmail;
        $id = $this->createSpreadsheets($email);

        $this->writeSheetOperation($id, $values);

        printf('已發送成功' . '<br/>');
        printf("google試算表連結: %s\n", 'https://docs.google.com/spreadsheets/d/' . $id . '<br/>');
        printf("Email: %s\n", $email);
        // return $data;
    }

    public function writeSheetOperation($id, $values)
    {
        (new GoogleSheetsServices())->writeSheet($id, $values);
        // $data = (new GoogleSheetsServices())->readSheet($id);

        // return $data;
    }

    public function createSpreadsheets($email)
    {
        $email = $email == null ? '' : $email;

        if($email != ''){
            return (new GoogleSheetsServices())->create($email);
        }else{
            return null;
        }
    }
}
