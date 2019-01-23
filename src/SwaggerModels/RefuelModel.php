<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-23
 * Time: 11:30
 */

namespace App\SwaggerModels;

class RefuelModel
{

    protected $_id;

    protected $date;

    protected $liters;

    protected $price;

    protected $kilometers;

    protected $picturePath;

    protected $user;

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
    public function getDate() : \DateTime
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getLiters() : string
    {
        return $this->liters;
    }

    /**
     * @return mixed
     */
    public function getPrice() : string
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getKilometers() : string
    {
        return $this->kilometers;
    }

    /**
     * @return mixed
     */
    public function getPicturePath() : string
    {
        return $this->picturePath;
    }

    /**
     * @return mixed
     */
    public function getUser() : UserModel
    {
        return $this->user;
    }

}