<?php
/**
 * Created by PhpStorm.
 * User: jeroenfrenken
 * Date: 2019-01-18
 * Time: 21:28
 */

namespace App\Controller\Services;

use Symfony\Component\Filesystem\Filesystem;

trait UploadService
{

    private $rootDir = __DIR__ . "/../../../public/";

    private $publicDir = "images/";

    protected function getFullUploadDir() {

        return $this->rootDir . $this->publicDir;

    }


    protected function getPublicUploadDir() {

        return $this->publicDir;

    }

    /*
     * TODO: Exception creation enz filename ?? make the imageupload process private
     */

    protected function uploadFile(String $file) : String {

        $data = explode(";", $file);

        $extension = str_replace("data:image/", "", $data[0]);

        if (
            $extension !== "png" ||
            $extension !== "jpg" ||
            $extension !== "jpeg"
        ) {

            //THROW EXCEPTION

        }

        $fileName = uniqid() . "." . $extension;



        $image = base64_decode(str_replace("base64,", "", $data[1]));

        $fs = new Filesystem();

        $fullFileName = $this->getFullUploadDir() . $fileName;

        $fs->touch($fullFileName);

        $fs->appendToFile($fullFileName, $image);

        return $fileName;

    }

}