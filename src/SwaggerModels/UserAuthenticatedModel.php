<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-23
 * Time: 12:00
 */

namespace App\SwaggerModels;


class UserAuthenticatedModel extends UserModel
{

    protected $token;

    /**
     * @return mixed
     */
    public function getToken() : string
    {
        return $this->token;
    }

}