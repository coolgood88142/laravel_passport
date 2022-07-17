<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Mail\Attach;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\ExcelController;

class MailController extends Controller
{
    /**
     * Ship the given order.
     *
     * @param  Request  $request
     * @return Response
     */
    public function sendEmailCompanyData(Request $request)
    {
        $queryCompany = $request->queryCompany == null ? '' : $request->queryCompany;
        $companyController = new CompanyController();
        $companyPermissionData = $companyController->createCompanyData($queryCompany);

        $email = $request->queryEmail == null ? '' : $request->queryEmail;
        $to = [
            'email' => $email
        ];

        Mail::to($to)->send(new OrderShipped($companyPermissionData));
        Log::info($companyPermissionData);

        if(count(Mail::failures())){
            printf('發送失敗，請重新輸入');
        }else{
            printf('發送成功' . '<br/>');
            printf("Email: %s\n", $email);
        }
    }

    public function sendEmailWithAttach(Request $request)
    {
        $queryCompany = $request->queryCompany == null ? '' : $request->queryCompany;
        $companyController = new CompanyController();
        $companyPermissionData = $companyController->createCompanyData($queryCompany);

        $excelController = new ExcelController();
        $filename = 'export.xlsx';

        $companyPermission = [];
        array_push($companyPermission, ['公司權益']);
        array_push($companyPermission, ['公司ID', '公司名稱', '產品ID', '產品名稱', '數量', '使用時段', '學員ID', '建立時間', '更新時間']);
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
            array_push($companyPermission, $array);
        }

        $companyPermissionLog = [];
        array_push($companyPermissionLog, ['公司權益']);
        array_push($companyPermissionLog, ['公司ID', '公司名稱', '產品ID', '產品名稱', '數量', '使用時段', '學員ID', '建立時間', '更新時間']);
        foreach($companyPermissionData['companyPermissionLog'] as $companyLog){
            $array = [
                $companyLog['company_id'],
                $companyLog['company_name'],
                $companyLog['product_id'],
                $companyLog['product_name'],
                $companyLog['amount'],
                $companyLog['date_time'],
                $companyLog['users_id'],
                $companyLog['created_at'],
                $companyLog['updated_at'],
            ];
            array_push($companyPermissionLog, $array);
        }

        $userPermission = [];
        array_push($userPermission, ['學員權益']);
        array_push($userPermission, ['學員ID', '學員姓名', '產品ID', '產品名稱', '使用時段', '建立時間', '更新時間']);
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
            array_push($userPermission, $array);
        }

        $userPermissionLog = [];
        array_push($userPermissionLog, ['學員權益變更記錄']);
        array_push($userPermissionLog, ['學員ID', '學員姓名', '產品ID', '產品名稱', '使用時段', '建立時間', '更新時間']);
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
            array_push($userPermissionLog, $array);
        }


        $companyPermissionDataArray = [
            'companyPermission' => $companyPermission,
            'companyPermissionLog' => $companyPermissionLog,
            'userPermission' => $userPermission,
            'userPermissionLog' => $userPermissionLog
        ];

        $excelController->exportExcxel($companyPermissionDataArray, $filename);

        $email = $request->queryEmail == null ? '' : $request->queryEmail;
        $to = [
            'email' => $email
        ];

        Mail::to($to)->send(new Attach($filename));

        if(count(Mail::failures())){
            printf('發送失敗，請重新輸入');
        }else{
            printf('發送成功' . '<br/>');
            printf("Email: %s\n", $email);
        }
    }
}
