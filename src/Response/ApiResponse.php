<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 09/01/2019
 * Time: 13:24
 */

namespace App\Response;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{

    static function serverError() {

        return new JsonResponse([
            'ok' => false,
            'data' => [
                'property' => 'SERVER',
                'message' => 'Request could not be executed'
            ]
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    static function rateLimit() {

        return ApiResponse::badRequest('RATE_LIMIT', 'You reached a rate limit try again later');

    }

    static function badRequest(String $property, String $message) {

        return new JsonResponse([
            'ok' => false,
            'data' => [
                'property' => $property,
                'message' => $message
            ]
        ], Response::HTTP_BAD_REQUEST);

    }

    static function okResponse($data = []) {

        return new JsonResponse([
            'ok' => true,
            'data' => $data
        ],Response::HTTP_ACCEPTED);

    }

    static function notAuthorized() {

        return new JsonResponse([
            'ok' => false,
            'message' => 'not authorized'
        ], Response::HTTP_UNAUTHORIZED);

    }

    static function forbidden() {

        return new JsonResponse([
            'ok' => false,
            'message' => 'not allowed'
        ], Response::HTTP_FORBIDDEN);

    }

}