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
            'permissionAmount' => $this->permissionAmount
        ]);
    }

    public function savePermission(Request $request){
        $permission = [];
        $nowPermission = [];
        $showText = '';
        $nowProduct = $request->product!= null ? $request->product : [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $permission = UserPermission::where('user_id', $userId);
            $count = $permission->count();

            $del = [];
            foreach($this->product as $index => $data){
                $companyPermission = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $data['id'])->get();

                if($companyPermission->count() > 0){
                    foreach($companyPermission as $value){
                        $permissionData = UserPermission::where('user_id', $userId)
                            ->where('product_id', $value->product_id)
                            ->where('start_datetime', $value->start_datetime)
                            ->where('end_datetime', $value->end_datetime)
                            ->first();
                        if($count > 0 || $permissionData == null){
                            if($permissionData != null && $nowProduct == []){
                                $this->deleteUserPermission($permissionData->id);
                                $showText = $this->saveUserPermissionLog($userId, $value, $permissionData, $showText);
                            }else if($permissionData != null && !in_array($value->id, $nowProduct)){
                                $this->deleteUserPermission($permissionData->id);
                                $showText = $this->saveUserPermissionLog($userId, $value, $permissionData, $showText);
                            }else if($permissionData == null && in_array($value->id, $nowProduct)){
                                $showText = $this->saveUserPermission($userId, $value, $showText);
                            }
                        }else if(in_array($value->id, $nowProduct)){
                            $showText = $this->saveUserPermission($userId, $value, $showText);
                        }
                    }
                }
            }

            if($showText != ''){
                $showText = substr($showText, 0, -1);
                $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
            }
        }else if(!Auth::check() && $nowProduct == null){
            $showText = '請選擇權益!';
        }else if(!Auth::check()){
            $showText = '請先登入後再選擇權益!';
        }

        $productData = [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
        }

        // return view('mainPermission', [
        //     'product' => $this->product,
        //     'permission' => $permission,
        //     'showText' => $showText
        // ]);

        foreach(Product::all() as $data){
            $this->product[$data->id]['id'] = $data->id;
            $this->product[$data->id]['name'] = $data->name;
        }

        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $this->permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();

            foreach($this->product as $id => $value){
                $companyPermissionData = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $id)->get();

                if($companyPermissionData->count() > 0){
                    $this->companyPermission[$id] = $companyPermissionData;
                    $this->companyPermissionGroup[$id] = $companyPermissionData->first();
                }
            }
        }

        // dd([
        //     'product' => $this->product,
        //     'user_permission' => $this->permission,
        //     'company_permission' => $this->companyPermission,
        //     'company_permission_group' => $this->companyPermissionGroup,
        //     'show_text' => $showText,
        //     'has_login' => Auth::check()
        // ]);

        return view('permission', [
            'product' => $this->product,
            'user_permission' => $this->permission,
            'company_permission' => $this->companyPermission,
            'company_permission_group' => $this->companyPermissionGroup,
            'show_text' => $showText,
            'has_login' => Auth::check()
        ]);

    }

    public function saveUserPermission($userId, $value, $showText){
        $UserPermission = new UserPermission();
        $UserPermission->user_id = $userId;
        $UserPermission->product_id = $value->product_id;
        $UserPermission->start_datetime = $value->start_datetime;
        $UserPermission->end_datetime = $value->end_datetime;
        $UserPermission->save();
        $showText = $showText . '新增' . $this->product[$value->product_id]['name'] . '(' . $value->start_datetime . '-' . $value->end_datetime . ')' . ',';
        return $showText;
    }

    public function saveUserPermissionLog($userId, $value, $permissionData, $showText){
        $UserPermission = new UserPermissionLog();
        $UserPermission->user_id = $userId;
        $UserPermission->product_id = $value->product_id;
        $UserPermission->start_datetime = $permissionData->start_datetime;
        $UserPermission->end_datetime = $permissionData->end_datetime;
        $UserPermission->save();
        $showText = $showText . '移除' . $this->product[$value->product_id]['name'] . '(' . $permissionData->start_datetime . '-' . $permissionData->end_datetime . ')' . ',';
        return $showText;
    }

    public function deleteUserPermission($id){
        UserPermission::where('id', $id)->delete();
    }

    public function showUserPermissionBlade(){
        $productData = [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
        }else{
            return '請先登入後再選擇權益!';
        }

        // $UserPermissionPresenter = new UserPermissionPresenter();

        // $data = $UserPermissionPresenter->matchProductId($permission, $this->product);


        // dd($UserPermissionPresenter->checkHeader($data, 'A'));

        // @if($presenter->checkHeader($presenter->matchProductId($permission, $product), 'A')

        return view('mainPermission', ['product' => $this->product, 'permission' => $permission]);

    }
}
