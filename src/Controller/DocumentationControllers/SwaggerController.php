<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-12
 * Time: 21:04
 */

namespace App\Controller\DocumentationControllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SwaggerController extends AbstractController
{

    /**
     * @OA\Server(
     *     url="https://api.gassapp.nl",
     *     description="The production version of gassapp API"
     * )
     *
     * @OA\Info(
     *     title="Gassapp API",
     *     version="0.1"
     * )
     *
     */
    public function getSwagger() {

        $openApi = \OpenApi\scan(__DIR__ . '/../../../src');
        header('Content-Type: application/json');

        return new Response($openApi->toJson());

    }

}