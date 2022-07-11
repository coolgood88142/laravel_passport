<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
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
}
