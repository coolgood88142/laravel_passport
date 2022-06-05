<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyPermission;
use App\Models\Product;
use App\Models\UserPermission;
use App\Models\UserPermissionLog;
use App\Presenters\UserPermissionPresenter;

class PermissionController extends Controller
{
    protected $product;
    public function __construct()
    {
        $product = [];
        foreach(Product::all() as $data){
            $product[$data->id]['id'] = $data->id;
            $product[$data->id]['name'] = $data->name;
        }

        $this->product = $product;
    }

    public function getDefaultData(){
        $permission = [];
        $companyPermission = [];
        $companyPermissionGroup = [];
        $product = [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $companyId = Auth::user()->company_id;
            $permission = UserPermission::where('user_id', $userId)->orderBy('product_id', 'asc')->get();
            $productData = Product::all();

            foreach($productData as $data){
                $product[$data->id] = $data->name;

                $companyPermissionData = CompanyPermission::where('company_id', $companyId)
                    ->where('product_id', $data->id)->get();

                if($companyPermissionData->count() > 0){
                    $companyPermission[$data->id] = $companyPermissionData;
                    $companyPermissionGroup[$data->id] = $companyPermissionData->first();
                }
            }

        }else{
            return '請先登入後再選擇權益!';
        }

        return view('permission', ['product' => $product, 'user_permission' => $permission,
            'company_permission' => $companyPermission, 'company_permission_group' => $companyPermissionGroup]);
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
            }
        }else if(!Auth::check() && $nowProduct == null){
            $showText = '請選擇權益!';
        }else{
            $showText = '請先登入後再選擇權益!';
        }

        return view('showPermission', ['showText' => $showText]);
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
