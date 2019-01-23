<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 09/01/2019
 * Time: 12:43
 */

namespace App\Controller\UserControllers;


use App\Interfaces\ApiAuthenticationInterface;
use App\Response\ApiResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController implements ApiAuthenticationInterface
{

    /**
     * @Security(name="Authorization")
     * @SWG\Get(description="Get user")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the authenticated user",
     *     @SWG\Schema(
     *        ref=@Model(type=App\SwaggerModels\UserModel::class)
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="User not authenticated"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error"
     * )
     */
    public function me() {

        return ApiResponse::okResponse($this->getUser());

    }

}