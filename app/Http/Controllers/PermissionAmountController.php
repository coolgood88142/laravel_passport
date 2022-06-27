<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\CompanyPermission;
use App\Models\Product;
use App\Models\UserPermission;
use App\Models\UserPermissionLog;
use App\Presenters\UserPermissionPresenter;
use Carbon\Carbon;

class PermissionAmountController extends Controller
{
    protected $product;
    protected $permission;
    protected $company;
    protected $companyPermission;
    protected $companyPermissionGroup;
    protected $permissionAmount;

    public function __construct()
    {
        $this->product = [];
        $this->permission = [];
        $this->companyPermission = [];
        $this->companyPermissionGroup = [];
        $this->showText = '';
        $this->company = [];
        $this->permissionAmount = [];
    }

    public function queryPermissionAmountData(Request $request){
        $queryCompanyId = $request->queryCompanyId == null ? '' : $request->queryCompanyId;
        $queryCompanyName = $request->queryCompanyName == null ? '' : $request->queryCompanyName;
        $queryProduct = $request->queryProduct == null ? '' : $request->queryProduct;
        $queryOrdData = $request->queryOrdData == null ? '' : $request->queryOrdData;

        $where = [];

        if($queryCompanyId != ''){
            array_push($where, ['company_id', '=',  $queryCompanyId ]);
        }

        if($queryCompanyName != ''){
            $companyData = Company::where('name', 'like', '%' . $queryCompanyName . '%')->get();

            foreach($companyData as $data){
                array_push($where, ['company_id', '=',  $data->id ]);
            }
        }

        if($queryProduct != ''){
            array_push($where, ['product_id', '=', $queryProduct ]);
        }

        foreach(Product::all() as $data){
            $this->product[$data->id]['id'] = $data->id;
            $this->product[$data->id]['name'] = $data->name;
        }

        foreach(Company::all() as $data){
            $this->company[$data->id]['id'] = $data->id;
            $this->company[$data->id]['name'] = $data->name;
        }

        $now = Carbon::now('Asia/Taipei')->toDateTimeString();
        if($queryOrdData != 'Y'){
            array_push($where, ['start_datetime', '<=', $now ]);
            array_push($where, ['end_datetime', '>=', $now ]);
        }

        $companyPermission = null;
        if(count($where) > 0){
            $companyPermission = CompanyPermission::where($where)->get();
        }else{
            $companyPermission = CompanyPermission::all();
        }

        foreach($companyPermission as $data){
            $company = Company::where('id', $data->company_id)->first();
            $product = Product::where('id', $data->product_id)->first();
            $userPermission = UserPermission::where('product_id', $data->product_id)
                            ->where('start_datetime', $data->start_datetime)
                            ->where('end_datetime', $data->end_datetime)
                            ->get();

            $userPermissionCount = $userPermission->count();
            $remainAmount = $data->amount - $userPermissionCount;

            $companyPermission = [
                'company_id' =>  $company->id,
                'company_name' =>  $company->name,
                'product_id' =>  $product->id,
                'product_name' =>  $product->name,
                'start_datetime' =>  $data->start_datetime,
                'end_datetime' =>  $data->end_datetime,
                'use_amount' => $userPermissionCount,
                'remain_amount' => $remainAmount,
            ];

            array_push($this->permissionAmount, $companyPermission);
        }

        return view('queryPermissionAmount', [
            'product' => $this->product,
            'queryCompanyId' => $queryCompanyId,
            'queryCompanyName' => $queryCompanyName,
            'queryProduct' => $queryProduct,
            'queryOrdData' => $queryOrdData,
            'permissionAmount' => $this->permissionAmount
        ]);
    }
}
