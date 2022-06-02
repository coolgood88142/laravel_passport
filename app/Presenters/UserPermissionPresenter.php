<?php

namespace App\Presenters;

use Carbon\Carbon;

class UserPermissionPresenter
{
    public function matchNowTime($startTime, $endTime){
        $now = Carbon::now('Asia/Taipei');
        $match = false;

        // $start = Carbon::new($startTime);
        dd($now->toDateTimeString()->gt($startTime));

        if($now->gt($startTime) && $now->lt($endTime)){
            $match = true;
        }

        return $match;
    }

    public function matchProductId($permission, $product){
        $productData = [];

        foreach($permission as $data){
            $id = $data->product_id;
            if(array_key_exists($id, $product) && $this->matchNowTime($data->start_datetime, $data->end_datetime)){
                array_push($product[$id]['name'], $productData);
            }
        }

        dd($productData);

        return $productData;
    }

    public function checkHeader($productData, $nowData){
        if($nowData == 'A' && ($productData == 'google' || $productData == 'fb' || $productData == 'twitter')){
            return true;
        }else if($nowData == 'B' && ($productData == 'fb' || $productData == 'twitter')){
            return true;
        }else if($nowData == 'C' && ($productData == 'fb')){
            return true;
        }

        return false;
    }

    public function checkContent($productData, $nowData){
        $show = false;
        if($nowData == 'A' && ($productData == 'google' || $productData == 'fb')){
            $show = true;
        }else if($nowData == 'B' && ($productData == 'fb')){
            $show = true;
        }else if($nowData == 'C' && ($productData == 'fb' || $productData == 'twitter')){
            $show = true;
        }

        return $show;
    }

    public function checkFooter($productData){
        $show = false;
        if($productData == 'google' || $productData == 'fb' || $productData == 'twitter'){
            $show = true;
        }
        return $show;
    }
}
