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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RefuelController
 * @package App\Controller\RefuelControllers
 */
class RefuelController extends AbstractController implements ApiAuthenticationInterface
{

    /**
     * @Security(name="Authorization")
     * @SWG\Get(description="Get all the users refuels")
     * @SWG\Response(
     *     response=200,
     *     description="Returns all the refuels of a user",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=App\SwaggerModels\RefuelModel::class))
     *     )
     * )
     * @SWG\Response(
     *     response=401,
     *     description="User not authenticated"
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

    /**
     * @Security(name="Authorization")
     * @SWG\Get(description="Get a single refuel")
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single refuel based on id",
     *     @SWG\Schema(
     *         ref=@Model(type=App\SwaggerModels\RefuelModel::class)
     *     )
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Resource not found"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="User not authenticated"
     * )
     */
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

    /**
     * @Security(name="Authorization")
     * @SWG\Post(description="Create a refuel")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(type="json", ref=@Model(type=App\SwaggerModels\RefuelCreateModel::class))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the created refuel",
     *     @SWG\Schema(
     *        ref=@Model(type=App\SwaggerModels\RefuelModel::class)
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request"
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

    /**
     * @Security(name="Authorization")
     * @SWG\Put(description="Updates a refuel")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     @SWG\Schema(type="json", ref=@Model(type=App\SwaggerModels\RefuelCreateModel::class))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the updated refuel",
     *     @SWG\Schema(
     *        ref=@Model(type=App\SwaggerModels\RefuelModel::class)
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Bad request"
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

    /**
     * @Security(name="Authorization")
     * @SWG\Delete(description="Deletes a refuel")
     * @SWG\Response(
     *     response=200,
     *     description="Resource deleted"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Resource not found"
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