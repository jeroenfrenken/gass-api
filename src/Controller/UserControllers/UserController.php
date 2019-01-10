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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController implements ApiAuthenticationInterface
{

    public function me() {

        return ApiResponse::okResponse($this->getUser());

    }

}