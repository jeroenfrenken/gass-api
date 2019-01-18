<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-18
 * Time: 22:23
 */

namespace App\Controller\RefuelControllers;


use App\Controller\Services\UploadService;
use App\Entity\Refuel;
use App\Entity\UserToken;
use App\Response\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ImageController extends AbstractController
{

    use UploadService;

    public function image($id, Request $request) {

        if ($request->get('token') === null) return ApiResponse::notAuthorized();

        $user = $this->getDoctrine()->getRepository(UserToken::class)->findOneBy([
            'token' => $request->get('token')
        ]);

        if ($user === null) return ApiResponse::notAuthorized();

        $found = $this->getDoctrine()->getRepository(Refuel::class)->findOneBy([
            'user' => $user->getUser(),
            'picturePath' => $id
        ]);

        if ($found === null) {

            return ApiResponse::notAuthorized();

        }

        return $this->file($this->getFullUploadDir() . $id, $id, ResponseHeaderBag::DISPOSITION_INLINE);

    }

}