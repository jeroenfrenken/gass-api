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
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthenticationController extends AbstractController
{

    /**
     * @SWG\Post(description="Authenticate a user")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(type="json", ref=@Model(type=App\SwaggerModels\UserLoginModel::class))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user",
     *     @SWG\Schema(
     *        ref=@Model(type=App\SwaggerModels\UserAuthenticatedModel::class)
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error"
     * )
     */
    public function login(
        SerializerInterface $serializer,
        TokenService $tokenService,
        ActionRegisterService $actionRegisterService,
        Request $request
    ) {

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

            $array = $account->jsonSerialize();

            $array['token'] = $token;

            return ApiResponse::okResponse($array);

        }

        return ApiResponse::badRequest('login', 'email or password incorrect');

    }

    /**
     * @SWG\Post(description="Register a user")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(type="json", ref=@Model(type=App\SwaggerModels\UserCreateModel::class))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the user",
     *     @SWG\Schema(
     *        ref=@Model(type=App\SwaggerModels\UserModel::class)
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request"
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error"
     * )
     */
    public function register(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        ActionRegisterService $actionRegisterService,
        Request $request
    ) {

        json_decode($request->getContent());

        if (json_last_error() !== JSON_ERROR_NONE) {

            return ApiResponse::badRequest('content', 'Please fill in al the fields');

        }

        try {

            /** @var User $user */
            $user = $serializer->deserialize(
                $request->getContent(), User::class, 'json'
            );


        }  catch (\Exception $e) {

            return ApiResponse::badRequest('content', 'Please fill in al the fields');

        }

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