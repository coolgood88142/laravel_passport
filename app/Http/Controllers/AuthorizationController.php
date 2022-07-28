<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;
use Illuminate\Support\Facades\Http;

class AuthorizationController
{
    /**
     * @OA\Get(
     *     path="/authorizationCode",
     *     tags={"AuthorizationCode"},
     *     @OA\Parameter(
     *          name="client_id",
     *          in="query",
     *          description="client端ID",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="redirect_uri",
     *          in="query",
     *          description="導頁的Uri",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="response_type",
     *          in="query",
     *          description="授權類型",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="scope",
     *          in="query",
     *          description="存取資料範圍",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          name="state",
     *          in="query",
     *          description="授權編碼",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Successful creation"
     *     ),
     *     @OA\Response(
     *        response=401,
     *        description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *        response=400,
     *        description="Bad Request"
     *     ),
     *     @OA\Response(
     *        response=404,
     *        description="not found"
     *     ),
     *     @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *     )
     * )
     */

    use HandlesOAuthErrors;

    /**
     * The authorization server.
     *
     * @var \League\OAuth2\Server\AuthorizationServer
     */
    protected $server;

    /**
     * The response factory implementation.
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * Create a new controller instance.
     *
     * @param  \League\OAuth2\Server\AuthorizationServer  $server
     * @param  \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @return void
     */
    public function __construct(AuthorizationServer $server, ResponseFactory $response)
    {
        $this->server = $server;
        $this->response = $response;
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $psrRequest
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Passport\ClientRepository  $clients
     * @param  \Laravel\Passport\TokenRepository  $tokens
     * @return \Illuminate\Http\Response
     */
    public function authorizationCode(ServerRequestInterface $psrRequest,
                              Request $request,
                              ClientRepository $clients,
                              TokenRepository $tokens)
    {
        return $this->withErrorHandling(function () use ($psrRequest, $request, $clients, $tokens) {
            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);
            // $request->headers->set('Access-Control-Allow-Origin', '*');

            $scopes = $this->parseScopes($authRequest);

            $token = $tokens->findValidToken(
                $user = $request->user(),
                $client = $clients->find($authRequest->getClient()->getIdentifier())
            );

            if (($token && $token->scopes === collect($scopes)->pluck('id')->all()) ||
                $client->skipsAuthorization()) {
                    // $authRequest.header("Access-Control-Allow-Origin", "*");
                    // $authRequest.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
                return $this->approveRequest($authRequest, $user);
            }

            $request->session()->put('authRequest', $authRequest);

            return $this->response->view('authorize', [
                'client' => $client,
                'user' => $user,
                'scopes' => $scopes,
                'request' => $request,
            ]);
        });
    }

    /**
     * Transform the authorization requests's scopes into Scope instances.
     *
     * @param  \League\OAuth2\Server\RequestTypes\AuthorizationRequest  $authRequest
     * @return array
     */
    protected function parseScopes($authRequest)
    {
        return Passport::scopesFor(
            collect($authRequest->getScopes())->map(function ($scope) {
                return $scope->getIdentifier();
            })->unique()->all()
        );
    }

    /**
     * Approve the authorization request.
     *
     * @param  \League\OAuth2\Server\RequestTypes\AuthorizationRequest  $authRequest
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return \Illuminate\Http\Response
     */
    protected function approveRequest($authRequest, $user)
    {
        $authRequest->setUser(new User($user->getKey()));

        $authRequest->setAuthorizationApproved(true);

        return $this->convertResponse(
            $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
        );
    }

    /**
     * Authorize a client to access the user's account.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $psrRequest
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAuthorizationPage(ServerRequestInterface $psrRequest, Request $request){
        //登入狀況
        if(Auth::check()){
            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);
            $scopes = $this->parseScopes($authRequest);
            $token = $tokens->findValidToken(
                $user = $request->user(),
                $client = $clients->find($authRequest->getClient()->getIdentifier())
            );
            //授權狀況
            if (($token && $token->scopes === collect($scopes)->pluck('id')->all()) ||
                $client->skipsAuthorization()) {
                //同意授權後，用authcode跑callback api換AccessToken
                return $this->agreeAuthorization($authRequest, $user);
            }
        }else {
            return route('login');
        }
    }

    /**
     * Approve the authorization request.
     *
     * @param  \League\OAuth2\Server\RequestTypes\AuthorizationRequest  $authRequest
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return \Illuminate\Http\Response
     */
    public function agreeAuthorization($authRequest, $user){
        $authRequest->setUser(new User($user->getKey()));

        $authRequest->setAuthorizationApproved(true);

        //要怎麼改程式碼才可以不跑redirect_uri
        return $this->convertResponse(
            $this->server->completeAuthorizationRequest($authRequest, new Psr7Response)
        );
    }

    public function runCallBack(Request $request){
        $http     = new GuzzleHttp\Client;
        $response = $http->post('http://127.0.0.1:8000/oauth/token', [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'redirect_uri'  => 'http://127.0.0.1:8080/callback',
                'code'          => $request->code,
                'client_id'     => '19',
                'client_secret' => 'YwvWN4dVe0blF7zpwAl2ge61ksTYBrvY8J8NfMT6'
            ]
        ]);

        $queryString = json_decode((string)$response->getBody(), true);

        // return $queryString;

        $userInfo = $http->get('http://127.0.0.1:8000/api/user', [
            'headers' => [
                'Authorization' => 'Bearer '. $queryString['access_token'],
            ],
        ]);

        // return 'Bearer '. $queryString['access_token'];

        $user = json_decode((string)$userInfo->getBody(), true);
        return $user;
    }
}
