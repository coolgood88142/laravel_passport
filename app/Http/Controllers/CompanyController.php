<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\CompanyPermission;
use App\Models\Product;
use App\User;
use App\Models\UserPermission;

class CompanyController extends Controller
{
    protected $product;
    protected $permission;
    protected $company;
    protected $companyPermission;
    protected $companyPermissionGroup;
    protected $permissionAmount;

    public function __construct()
    {
        $this->queryCompanyId = '';
        $this->queryCompanyName = '';
        $this->companyPermission = [];
        $this->companyPermissionGroup = [];
        $this->companyId = '';
        $this->productId = '';
        $this->amount = '';
        $this->company = [];
        $this->permissionAmount = [];
    }

    public function editCompany(Request $request){
        $queryCompany = $request->queryCompany == null ? '' : $request->queryCompany;
        $queryCompanyPermission = $request->queryCompanyPermission == null ? '' : $request->queryCompanyPermission;
        $addCompanyPermission = $request->addCompanyPermission == null ? '' : $request->addCompanyPermission;
        $errorMsg = '';

        if($queryCompany != '' && $queryCompanyPermission == 'Y'){
            $this->companyId = $queryCompany;
            $company = Company::where('id', $queryCompany)->first();
            $this->queryCompanyName = $company->name;
        }else if($queryCompany != '' && $addCompanyPermission == 'Y'){
            $this->companyId = $request->companyId == null ? '' : $request->companyId;
            $this->productId = $request->productId == null ? '' : $request->productId;
            $this->amount = $request->amount == null ? '' : $request->amount;
            $startDateTime = $request->startDateTime == null ? '' : strtotime($request->startDateTime);
            $endDateTime = $request->endDateTime == null ? '' : strtotime($request->endDateTime);

            if($this->amount > 0){
                $companyPermission = CompanyPermission::where('company_id', $this->companyId)
                                ->where('product_id', $this->productId)
                                ->first();
                if($companyPermission->amount > $this->amount){
                    $productName = Product::where('id', $this->productId)->first();
                    $errorMsg = $productName->name . '數量不能小於' . $companyPermission->amount . ',';
                }else if($startDateTime > $endDateTime){
                    $errorMsg = $errorMsg . '使用時段的起始時間不能大於截止時間' . ',';
                }else if($startDateTime == $endDateTime){
                    $errorMsg = $errorMsg . '使用時段的起始時間不能等於截止時間' . ',';
                }

                if($errorMsg == ''){
                    $amount = $this->amount;
                    $companyId = $this->companyId;
                    $productId = $this->productId;

                    $data = new CompanyPermission();
                    $data->company_id = $companyId;
                    $data->product_id = $productId;
                    $data->amount = $amount;
                    $data->start_datetime = $request->startDateTime;
                    $data->end_datetime = $request->endDateTime;
                    $data->save();

                    return redirect(route('queryPermissionAmount'));
                }else{
                    $errorMsg = substr($errorMsg, 0, -1);
                }
            }
        }



        return view('editCompany', [
            'company' => Company::all(),
            'product' => Product::all(),
            'errorMsg' => $errorMsg,
            'companyId' => $this->companyId,
            'productId' => $this->productId,
            'amount' => $this->amount,
            'startDateTime' => $request->startDateTime,
            'endDateTime' => $request->endDateTime,
        ]);
    }

    public function queryCompany(Request $request){
        return view('queryCompany', [
            'company' => Company::all(),
        ]);
    }

    public function sendCompanyData(Request $request){
        $queryCompany = $request->queryCompany == null ? '' : $request->queryCompany;
        $companyPermission = [];
        $userPermission = [];
        $productData = [];

        if($queryCompany != ''){
            $companyPermission = CompanyPermission::where('company_id', $queryCompany)->get();

            foreach($companyPermission as $data){
                $company = Company::where('id', $data->company_id)->first();
                $product = Product::where('id', $data->product_id)->first();
                $userData = User::select('id')->where('company_id', $data->company_id)->get();
                $startDatetime = $data->start_datetime;
                $endDatetime = $data->end_datetime;
                $userArray = [];
                foreach($userData as $user){
                    array_push($userArray, $user->id);
                }

                $permission = UserPermission::where('product_id', $data->product_id)
                    ->where('start_datetime', $startDatetime)
                    ->where('end_datetime', $endDatetime)
                    ->whereIn('user_id', $userArray)
                    ->get();

                if($permission->count() > 0){
                    foreach($permission as $data){
                        array_push($userPermission, $data);
                    }
                }
            }

            foreach(Product::all() as $data){
                array_push($productData, $data);
            }
        }

        return view('emails.company', [
            'companyPermission' => $companyPermission,
            'productData' => $productData,
            'userPermission' => $userPermission,
        ]);
    }
}
