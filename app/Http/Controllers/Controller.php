<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *  @OA\Post(
 *     path="/oauth/token",
 *     tags={"AccessToken"},
 *     @OA\RequestBody(
 *        @OA\MediaType(
 *           mediaType="multipart/form-data",
 *           @OA\Schema(
 *              @OA\Property(
 *                  property="grant_type",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="redirect_uri",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="code",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="client_id",
 *                  type="Integer"
 *              ),
 *              @OA\Property(
 *                  property="client_secret",
 *                  type="string"
 *              ),
 *              example={
 *                  "grant_type": "authorization_code",
 *                  "redirect_uri": "http://127.0.0.1:8080/getAuthorizationCode",
 *                  "code": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
 *                  "client_id": 18,
 *                  "client_secret": "har2ePG2kSTW2BPNaqVWdiyhg5U3SarVxNpLxZgD"
 *              }
 *           )
 *        )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *     )
 *  )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
