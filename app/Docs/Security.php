<?php
/**
* @OA\SecurityScheme(
*     securityScheme="bearerAuth",
*     type="http",
*     description="JWT Authorization header using the Bearer scheme.",
*     scheme="bearer",
*     bearerFormat="JWT"
* ),
* @OA\SecurityScheme(
*     securityScheme="passport",
*     type="oauth2",
*     description="Laravel passport oauth2 security.",
*     in="header",
*     @OA\Flow(
*         flow="authorizationCode",
*         authorizationUrl="http://127.0.0.1:8080/authorize",
*         tokenUrl="http://127.0.0.1:8000/oauth/token",
*         scopes={}
*    )
*)
*/
