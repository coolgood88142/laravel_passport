<?php

namespace App\Presenters;

use Carbon\Carbon;

class UserPermissionPresenter
{
    public function matchNowTime($startTime, $endTime){
        $now = Carbon::now('Asia/Taipei');
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);

        return $now->between($start, $end);
    }

    public function matchProductId($permission, $product){
        $productData = [];

        foreach($permission as $data){
            $id = $data->product_id;
            if(!in_array($product[$id]['name'], $productData) && array_key_exists($id, $product)
                && $this->matchNowTime($data->start_datetime, $data->end_datetime)){
                array_push($productData, $product[$id]['name']);
            }
        }

        return $productData;
    }

    public function checkHeader($productData){
        return in_array('google', $productData) || in_array('fb', $productData)  || in_array('twitter', $productData);
    }

    public function checkContentA($productData){
        return (in_array('google', $productData)  || in_array('fb', $productData));
    }

    public function checkContentB($productData){
        return in_array('fb', $productData);
    }

    public function checkContentC($productData){
        return (in_array('fb', $productData) || in_array('twitter', $productData));
    }

    public function checkFooter($productData){
        return in_array('google', $productData) || in_array('fb', $productData)  || in_array('twitter', $productData);
    }
}
