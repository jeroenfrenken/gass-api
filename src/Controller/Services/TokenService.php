<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 10/01/2019
 * Time: 13:22
 */

namespace App\Controller\Services;


use App\Entity\User;
use App\Entity\UserToken;
use Doctrine\ORM\EntityManagerInterface;

class TokenService
{

    private $_doctrine;

    public function __construct(EntityManagerInterface $doctrine) {

        $this->_doctrine = $doctrine;

    }

    public function generateToken(User $user) {

        $token = new UserToken();

        $token->setUser($user);
        $token->setToken(uniqid());

        $em = $this->_doctrine;

        $em->persist($token);

        $em->flush();

        return $token->getToken();

    }

}