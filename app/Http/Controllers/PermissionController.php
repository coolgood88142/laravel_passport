<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    protected $product;
    public function __construct()
    {
        $this->product = ['google','fb', 'Twitter'];
    }

    public function getDefaultData(){
        $permission = [0];

        return view('permission', ['product' => $this->product, 'permission' => $permission]);
    }

    public function savePermission(Request $request){
        $permission = json_decode($request->permission);
        $nowProduct = $request->product;
        $nowPermission = [];
        $showText = '';

        if($nowProduct == null){
            $showText = '請選擇權益!';
        }else{
            foreach($this->product as $key => $value){
                if(!in_array($key, $permission) && !in_array($key, $nowProduct)){
                    continue;
                }

                if(in_array($key, $permission) && !in_array($key, $nowProduct)){
                    $showText = $showText . '移除' . $value;
                }else if(!in_array($key, $permission) && in_array($key, $nowProduct)){
                    $showText = $showText . '新增' . $value;
                }

                if($showText != ''){
                    $showText = $showText . ',';
                }
            }

            $showText = substr($showText, 0, -1);
        }



        // dd(substr($showText, 0, -1));

        // foreach($permission as $key => $value){
        //     $isDelete = false;
        //     foreach($nowProduct as $index => $item){
        //         if((string)$value != $item){
        //             $isDelete = true;
        //         }
        //     }
        //     dd($key);
        //     if($isDelete){
        //         $showText += '移除' . intval($value);
        //     }else{
        //         $showText += '新增' . intval($value);
        //     }


        //     if($key != 1){
        //         $showText += ',';
        //     }
        // }

        return $showText;
    }
}
