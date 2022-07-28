<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     **
     * @OA\Get(
     *     path="/api/user",
     *     tags={"UserInfo"},
     *     @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *     ),
     *     @OA\Response(
     *          response="401",
     *          description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *          response="403",
     *          description="Forbidden",
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request",
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="not found",
     *     ),
     *     security={
     *          {"passport":{}}
     *     },
     * )
     *
     */
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
