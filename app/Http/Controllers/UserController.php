<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
     * @SWG\Get(
     *     path="/api/user",
     *     summary="取得使用者資訊",
     *     tags={"UserInfo"},
     *     produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
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
}
