<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 09/01/2019
 * Time: 12:37
 */

namespace App\Controller\UserControllers;

use App\Controller\Services\ActionRegisterService;
use App\Controller\Services\TokenService;
use App\Entity\User;
use App\Response\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthenticationController extends AbstractController
{

    public function login(
        SerializerInterface $serializer,
        TokenService $tokenService,
        ActionRegisterService $actionRegisterService,
        Request $request
    ) {

        if (
            !$actionRegisterService->canDoAction($request->getClientIp(), ActionRegisterService::ACTION_LOGIN)
        ) {
            return ApiResponse::rateLimit();
        }

        /** @var User $user */
        $user = $serializer->deserialize(
            $request->getContent(), User::class, 'json'
        );

        if ($user->getEmail() === null) {

            return ApiResponse::badRequest('email', 'Please provide email and password');

        }

        if ($user->getPassword() === null) {

            return ApiResponse::badRequest('password', 'Please provide email and password');

        }

        $doctrine = $this->getDoctrine();

        $account = $doctrine->getRepository(User::class)->findOneBy([
            'email' => strtolower($user->getEmail())
        ]);

        if ($account !== null && password_verify($user->getPassword(), $account->getPassword())) {

            $token = $tokenService->generateToken($account);

            $actionRegisterService->registerAction($request->getClientIp(), ActionRegisterService::ACTION_LOGIN);

            return ApiResponse::okResponse([
                'token' => $token
            ]);

        }

        return ApiResponse::badRequest('login', 'email or password incorrect');

    }

    public function register(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ActionRegisterService $actionRegisterService,
        Request $request
    ) {

        if (
            !$actionRegisterService->canDoAction($request->getClientIp(), ActionRegisterService::ACTION_REGISTER)
        ) {
            return ApiResponse::rateLimit();
        }

        json_decode($request->getContent());

        if (json_last_error() !== JSON_ERROR_NONE) {

            return ApiResponse::badRequest('content', 'no json content');

        }

        /** @var User $user */
        $user = $serializer->deserialize(
            $request->getContent(), User::class, 'json'
        );

        $errors = $validator->validate($user);

        if (count($errors) > 0) return ApiResponse::badRequest($errors[0]->getPropertyPath(), $errors[0]->getMessage());

        $found = $this->getDoctrine()->getRepository(User::class)->findByEmail($user->getEmail());

        if ($found !== null) return ApiResponse::badRequest('email', 'E-mail already taken');

        $user->setPassword(password_hash($user->getPassword(), PASSWORD_BCRYPT));

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);

        try {

            $em->flush();

            $actionRegisterService->registerAction($request->getClientIp(), ActionRegisterService::ACTION_REGISTER);

            return ApiResponse::okResponse($user);

        } catch (\Exception $e) {

            return ApiResponse::serverError();

        }

    }

}