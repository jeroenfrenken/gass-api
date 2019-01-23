<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-23
 * Time: 11:34
 */

namespace App\SwaggerModels;


class UserModel
{

    protected $_id;

    protected $email;

    protected $firstname;

    protected $lastname;

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->_id;
    }

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


}