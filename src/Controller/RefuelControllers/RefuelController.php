<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 11/01/2019
 * Time: 13:18
 */

namespace App\Controller\RefuelControllers;


use App\Entity\Refuel;
use App\Entity\User;
use App\Interfaces\ApiAuthenticationInterface;
use App\Response\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Flex\Response;

/**
 * TODO: refuel upload picture
 * TODO: refuel upload lat long
 *
 * Class RefuelController
 * @package App\Controller\RefuelControllers
 */
class RefuelController extends AbstractController implements ApiAuthenticationInterface
{

    /**
     * @OA\Get(
     *     operationId="Get all refuels of a user",
     *     path="/refuel/get",
     *     @OA\Response(
     *          response="200",
     *          description="No error"
     *     )
     * )
     */
    public function getAll() {

        /** @var User $user */
        $user = $this->getUser();

        $out = [];

        foreach ($user->getRefuels() as $refuel) {

            $out[] = $refuel;

        }

        $out = array_reverse($out);

        return ApiResponse::okResponse($out);

    }

    public function getRefuel(string $id)
    {

        /** @var User $user */
        $user = $this->getUser();

        $refuel = $this->getDoctrine()->getRepository(Refuel::class)->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if ($refuel === null) {

            return ApiResponse::notFound();

        }

        return ApiResponse::okResponse($refuel);

    }

    public function create(
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        Request $request
    ) {

        $refuel = $this->_validateInput($request, $serializer, $validator);

        if ($refuel instanceof JsonResponse) {

            return $refuel;

        }

        $refuel->setUser($this->getUser());

        $em = $this->getDoctrine()->getManager();

        $em->persist($refuel);

        try {

            $em->flush();

            return ApiResponse::okResponse($refuel);

        } catch (\Exception $e) {

            return ApiResponse::serverError();

        }

    }

    public function update(
        string $id,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {

        /** @var User $user */
        $user = $this->getUser();

        /** @var Refuel $refuel */
        $refuel = $this->getDoctrine()->getRepository(Refuel::class)->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if ($refuel === null) {

            return ApiResponse::notFound();

        }

        $refuelUpdate = $this->_validateInput($request, $serializer, $validator);

        if ($refuelUpdate instanceof JsonResponse) {

            return $refuelUpdate;

        }

        $em = $this->getDoctrine()->getManager();

        $refuel->setKilometers($refuelUpdate->getKilometers());
        $refuel->setLiters($refuelUpdate->getLiters());
        $refuel->setPrice($refuelUpdate->getPrice());

        try {

            $em->flush();

            return ApiResponse::okResponse($refuel);

        } catch (\Exception $e) {

            return ApiResponse::serverError();

        }

    }

    public function delete(string $id) {

        /** @var User $user */
        $user = $this->getUser();

        /** @var Refuel $refuel */
        $refuel = $this->getDoctrine()->getRepository(Refuel::class)->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        if ($refuel === null) {

            return ApiResponse::notFound();

        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($refuel);

        try {

            $em->flush();

            return ApiResponse::okResponse([
                'message' => 'Resource deleted'
            ]);

        } catch (\Exception $e) {

            return ApiResponse::serverError();

        }

    }

    private function _validateInput(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )
    {

        json_decode($request->getContent());

        if (json_last_error() !== JSON_ERROR_NONE) {

            return ApiResponse::badRequest('content', 'no json content');

        }

        try {

            /** @var Refuel $refuel */
            $refuel = $serializer->deserialize(
                $request->getContent(), Refuel::class, 'json'
            );

        } catch (NotNormalizableValueException $exception) {

            return ApiResponse::badRequest('NotNormalizableValueException', 'A value is not right formatted');

        } catch (\Exception $e ) {

            return ApiResponse::badRequest('content', 'Please fill in all the fields');

        }

        $errors = $validator->validate($refuel);

        if (count($errors) > 0) return ApiResponse::badRequest($errors[0]->getPropertyPath(), $errors[0]->getMessage());

        return $refuel;

    }

}