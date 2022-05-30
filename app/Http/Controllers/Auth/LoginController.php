<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     *  @SWG\Post(
     *      path="/login",
     *      tags={"Login"},
     *      summary="Login",
     *      operationId="login",
     *      consumes={"application/json"},
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *           name="email",
     *           in="formData",
     *           required=true,
     *           type="string"
     *      ),
     *      @SWG\Parameter(
     *           name="password",
     *           in="formData",
     *           required=true,
     *           type="string"
     *      ),
     *      @SWG\Response(
     *           response=200,
     *           description="Success"
     *      ),
     *      @SWG\Response(
     *         response=401,
     *         description="Unauthenticated"
     *      ),
     *      @SWG\Response(
     *         response=400,
     *         description="Bad Request"
     *      ),
     *      @SWG\Response(
     *         response=404,
     *         description="not found"
     *      ),
     *      @SWG\Response(
     *         response=403,
     *         description="Forbidden"
     *      )
     *  )
     *
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
