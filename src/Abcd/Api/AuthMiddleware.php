<?php

namespace Abcd\Api;

use App\Http\Request;
use App\Http\Response;

class AuthMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param \Application $app
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Application $app): Response
    {
        $token = $this->getApiToken($request);
        if (!$token) {
            throw new \RuntimeException('Con not detect API token', Response::HTTP_BAD_REQUEST);
        }

        // check authentication
        if (!in_array($token, $app['config']['api_tokens'])) {
            throw new \RuntimeException('Your API token is not registered in system', Response::HTTP_UNAUTHORIZED);
        }

        $request->attributes['api_token'] = $token;
        return $response;
    }

    /**
     * @param Request $request
     * @return null|string
     */
    private function getApiToken(Request $request): ?string
    {
        $token = preg_split('/\s+/', $request->headers['Authorization'] ?? '')[1] ?? null;
        return $token ?: null;
    }

}