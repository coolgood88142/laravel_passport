<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\UserPermission;
use App\Models\UserPermissionLog;
class PermissionController extends Controller
{
    protected $product;
    public function __construct()
    {
        $this->product =  Product::all();
    }

    public function getDefaultData(){
        $permission = [];
        if(Auth::check()){
            $userId = Auth::user()->id;
            $permissionCount = UserPermission::where('user_id', $userId)->count();

            if($permissionCount > 0){
                $permission = UserPermission::where('user_id', $userId)->get();
            }
        }else if($nowProduct == null){
            $showText = '請選擇權益!';
        }else{
            return '請先登入後再選擇權益!';
        }

        return view('permission', ['product' => $this->product, 'permission' => $permission]);
    }

    public function savePermission(Request $request){
        $permission = [];
        $nowPermission = [];
        $showText = '';
        $nowProduct = $request->product;
        if(Auth::check()){
            $userId = Auth::user()->id;
            $permission = UserPermission::where('user_id', $userId);
            $count = $permission->count();

            $add = [];
            $del = [];
            foreach($this->product as $value){
                if($count > 0){
                    $permissionData = $permission->where('product_id', $value->id)->get();
                    if($permissionData != null && $nowProduct == null){
                        $UserPermission = new UserPermissionLog();
                        $UserPermission->user_id = $userId;
                        $UserPermission->product_id = $value->id;
                        $UserPermission->save();
                        array_push($del, $value->id);
                        $showText = $showText . '移除' . $value->name . ',';
                    }else{
                        if($permissionData != null && !in_array($value->id, $nowProduct)){
                            $UserPermission = new UserPermissionLog();
                            $UserPermission->user_id = $userId;
                            $UserPermission->product_id = $value->id;
                            $UserPermission->save();
                            array_push($del, $value->id);
                            $showText = $showText . '移除' . $value->name . ',';
                        }else if($permissionData == null && in_array($value->id, $nowProduct)){
                            $UserPermission = new UserPermission();
                            $UserPermission->user_id = $userId;
                            $UserPermission->product_id = $value->id;
                            $UserPermission->save();
                            $showText = $showText . '新增' . $value->name . ',';
                        }
                    }
                }else{
                    if(in_array($value->id, $nowProduct)){
                        $UserPermission = new UserPermission();
                        $UserPermission->user_id = $userId;
                        $UserPermission->product_id = $value->id;
                        $UserPermission->save();
                        $showText = $showText . '新增' . $value->name . ',';
                    }
                }
            }

            if($showText != ''){
                $showText = substr($showText, 0, -1);
            }

            if(count($del) > 0){
                UserPermission::whereIn('product_id', $del)->delete();
            }
        }else if(!Auth::check() && $nowProduct == null){
            $showText = '請選擇權益!';
        }else{
            $showText = '請先登入後再選擇權益!';
        }

        return view('showPermission', ['showText' => $showText]);
    }

    public function saveUserPermission($UserPermission, $value){
        $UserPermission = new UserPermission();
        $UserPermission->user_id = $userId;
        $UserPermission->product_id = $value->id;
        $UserPermission->save();
        $showText = $showText . '新增' . $value->name . ',';
    }
}
