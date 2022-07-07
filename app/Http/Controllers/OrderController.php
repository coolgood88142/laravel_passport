<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Ship the given order.
     *
     * @param  Request  $request
     * @return Response
     */
    public function ship(Request $request)
    {
        // dd($request);
        // $companyPermission = $request->companyPermission;

        // $productData = $request->productData;
        // $userPermission = $request->userPermission;
        // $order = Order::findOrFail($orderId);
        $to = [
            'email' => 'coolgood88142@gmail.com'
        ];

        // Ship order...

        Mail::to($to)->send(new OrderShipped());
    }
}
