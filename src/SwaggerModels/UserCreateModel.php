<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-23
 * Time: 12:03
 */

namespace App\SwaggerModels;


class UserCreateModel
{

    protected $email;

    protected $firstname;

    protected $lastname;

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
    public function getFirstname() : string
    {
        return $this->firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname() : string
    {
        return $this->lastname;
    }

    /**
     * @return mixed
     */
    public function getPassword() : string
    {
        return $this->password;
    }

}