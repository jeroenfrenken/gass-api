<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-23
 * Time: 11:40
 */

namespace App\SwaggerModels;


class RefuelCreateModel
{

    protected $liters;

    protected $price;

    protected $kilometers;

    protected $picture;

    /**
     * @return mixed
     */
    public function getLiters() : float
    {
        return $this->liters;
    }

    /**
     * @return mixed
     */
    public function getPrice() : float
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getKilometers() : float
    {
        return $this->kilometers;
    }

    /**
     * @return mixed
     */
    public function getPicture() : string
    {
        return $this->picture;
    }

}