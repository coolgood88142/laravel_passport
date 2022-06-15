<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @SWG\Swagger(
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Laravel Passport Authorization Code API",
 *         description="Authorization Code API description",
 *         @SWG\Contact(
 *             email="coolgood88142@gmail.com"
 *         ),
 *     ),
 *     @SWG\SecurityScheme(
 *       securityDefinition="Bearer",
 *       type="apiKey",
 *       in="header",
 *       name="Authorization",
 *     ),
 *     @SWG\Post(
 *          path="/oauth/token",
 *          summary="取得Access Token",
 *          tags={"AccessToken"},
 *          produces={"application/json"},
 *          security={"apiKey"},
 *          @SWG\Parameter(
 *               name="grant_type",
 *               in="formData",
 *               description="grantType",
 *               required=true,
 *               type="string",
 *          ),
 *          @SWG\Parameter(
 *               name="redirect_uri",
 *               in="formData",
 *               description="redirectUri",
 *               required=true,
 *               type="string",
 *          ),
  *          @SWG\Parameter(
 *               name="code",
 *               in="formData",
 *               description="code",
 *               required=true,
 *               type="string",
 *          ),
  *          @SWG\Parameter(
 *               name="client_id",
 *               in="formData",
 *               description="client_id",
 *               required=true,
 *               type="string",
 *          ),
  *          @SWG\Parameter(
 *               name="client_secret",
 *               in="formData",
 *               description="clientSecret",
 *               required=true,
 *               type="string",
 *          ),
 *          @SWG\Response(
 *              response=200,
 *              description="Successful operation",
 *          )
 *     )
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
