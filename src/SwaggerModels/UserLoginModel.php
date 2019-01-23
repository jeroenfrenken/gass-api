<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-23
 * Time: 11:55
 */

namespace App\SwaggerModels;


class UserLoginModel
{

    protected $email;

    protected $password;

    /**
     * @return mixed
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword() : string
    {
        return $this->password;
    }



}