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

class AuthorizationController
{
    /**
     * @SWG\Get(
     *     path="/authorizationCode",
     *     summary="取得authorizationCode",
     *     tags={"AuthorizationCode"},
     *     produces={"application/json"},
     *     security={
     *          {"Bearer":{}}
     *     },
     *     @SWG\Parameter(
     *          name="client_id",
     *          in="query",
     *          description="client端ID",
     *          required=true,
     *          type="string",
     *     ),
     *     @SWG\Parameter(
     *          name="redirect_uri",
     *          in="query",
     *          description="導頁的Uri",
     *          required=true,
     *          type="string",
     *     ),
     *     @SWG\Parameter(
     *          name="response_type",
     *          in="query",
     *          description="授權類型",
     *          required=true,
     *          type="string",
     *     ),
     *     @SWG\Parameter(
     *          name="scope",
     *          in="query",
     *          description="存取資料範圍",
     *          required=false,
     *          type="string",
     *     ),
     *     @SWG\Parameter(
     *          name="state",
     *          in="query",
     *          description="授權編碼",
     *          required=true,
     *          type="string",
     *     ),
     *     @SWG\Response(
     *          response="200",
     *          description="Successful creation"
     *     ),
     *     @SWG\Response(
     *        response=401,
     *        description="Unauthenticated"
     *     ),
     *     @SWG\Response(
     *        response=400,
     *        description="Bad Request"
     *     ),
     *     @SWG\Response(
     *        response=404,
     *        description="not found"
     *     ),
     *     @SWG\Response(
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

            $scopes = $this->parseScopes($authRequest);

            $token = $tokens->findValidToken(
                $user = $request->user(),
                $client = $clients->find($authRequest->getClient()->getIdentifier())
            );

            if (($token && $token->scopes === collect($scopes)->pluck('id')->all()) ||
                $client->skipsAuthorization()) {
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
}
