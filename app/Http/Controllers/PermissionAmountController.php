<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
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

        $remainAmount = 0;
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

            $userPermission = UserPermission::where('product_id', $data->product_id)
                ->where('start_datetime', $startDatetime)
                ->where('end_datetime', $endDatetime)
                ->whereIn('user_id', $userArray)
                ->get();

            $userPermissionCount = $userPermission->count();
            $UserPermissionPresenter = new UserPermissionPresenter();
            $matchNowTime = $UserPermissionPresenter->matchNowTime($startDatetime, $endDatetime);
            $remainAmount = $data->amount - $userPermissionCount;

            // //確認今天日期有沒有在目前產品的使用時段內
            if($matchNowTime){
                //查詢有沒有相同產品的使用時段範圍，也在今天日期
                $otherUserPermission = UserPermission::select('start_datetime', 'end_datetime')->where('product_id', $data->product_id)
                    ->whereIn('user_id', $userArray)
                    ->where('start_datetime', '<=', $now)
                    ->where('end_datetime', '>=', $now)
                    ->where('start_datetime', '!=', $startDatetime)
                    ->where('end_datetime', '!=', $endDatetime)
                    ->get();

                //有的話，要計算有多少的沒選到該權益的學員數量
                if($otherUserPermission->count() > 0){
                    foreach($userPermission as $permission){
                        if(in_array($permission->user_id, $userArray)){
                            $key = array_search($permission->user_id, $userArray);
                            unset($userArray[$key]);
                        }
                    }

                    $remainAmount = $data->amount - count($userArray);
                }


                // //查詢有沒有其他產品也在今天日期的使用時段範圍內
                // $otherUserPermission = UserPermission::select('start_datetime', 'end_datetime')->where('product_id', $data->product_id)
                //     ->whereIn('user_id', $userArray)
                //     ->where('start_datetime', '<=', $now)
                //     ->where('end_datetime', '>=', $now)
                //     ->where('start_datetime', '!=', $startDatetime)
                //     ->where('end_datetime', '!=', $endDatetime)
                //     ->get();

                // dd($otherUserPermission);
                // if($otherUserPermission->count() > 0){
                //     $userCount = User::where('company_id', $data->company_id)
                //         ->get()
                //         ->count();

                //     $remainAmount = $data->amount - count($userArray);
                // }
            //     //TODO 要怎麼做才可以在畫面，顯示有包含今天日期的使用時段?
            //     //目前的作法是有重疊的情況，就要把兩種使用時段的資料合併成一筆
            //     if($otherUserPermission->count() > 2){
            //         foreach($otherUserPermission as $orther){
            //             if($orther->start_datetime != $startDatetime || $orther->end_datetime != $endtDatetime){

            //             }
            //         }
            //     }
            }

            $companyPermission = [
                'company_id' =>  $company->id,
                'company_name' =>  $company->name,
                'product_id' =>  $product->id,
                'product_name' =>  $product->name,
                'start_datetime' =>  $startDatetime,
                'end_datetime' =>  $endDatetime,
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
