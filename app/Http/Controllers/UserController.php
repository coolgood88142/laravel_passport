<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
     * @SWG\Get(
     *     path="/api/user",
     *     summary="取得使用者資訊",
     *     tags={"UserInfo"},
     *     produces={"application/json"},
     *     security={
     *          {"Bearer":{}}
     *     },
     *     @SWG\Response(
     *          response=200,
     *          description="Successful operation",
     *     ),
     *     @SWG\Response(
     *          response="401",
     *          description="Unauthenticated",
     *     ),
     *     @SWG\Response(
     *          response="403",
     *          description="Forbidden",
     *     ),
     *     @SWG\Response(
     *          response="400",
     *          description="Bad Request",
     *     ),
     *     @SWG\Response(
     *          response="404",
     *          description="not found",
     *     ),
     * )
     */
class UserController extends Controller
{
    public function getData(Request $request) {
        return $request->user()->toArray();
    }

    public function getLoginSesstion(Request $request){
        if(Auth::check()){
            $key = env('APP_KEY', null);
            $iv = random_bytes(16);
            $data = openssl_encrypt($request->cookie('laravel_session'), 'AES-256-CBC', $key, 0, $iv);
            dd($request->cookie());
            dd(session_decode($request->cookie('laravel_session')));
            return session('key');
        }else{
            return 'error';
        }
    }
}
