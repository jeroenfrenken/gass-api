<?php

namespace App\Entity;

use App\Controller\Services\UploadService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Refuel implements \JsonSerializable
{

    use UploadService;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Blank()
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(
     *     message="{{ value }} can not be blank"
     * )
     * @Assert\Type(
     *     type = "float",
     *     message = "The value {{ value }} is not a valid {{ type }}"
     * )
     */
    private $liters;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(
     *     message="{{ value }} can not be blank"
     * )
     * @Assert\Type(
     *     type = "float",
     *     message = "The value {{ value }} is not a valid {{ type }}"
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(
     *     message="{{ value }} can not be blank"
     * )
     * @Assert\Type(
     *     type = "float",
     *     message = "The value {{ value }} is not a valid {{ type }}"
     * )
     */
    private $kilometers;

    //Holds the uploaded picture
    private $picture;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Blank()
     */
    private $picturePath;

    //Holds the uploading latitude
    private $lat;

    //Holds the uploading longitude
    private $long;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Blank()
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="refuels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLiters(): ?float
    {
        return $this->liters;
    }

    public function setLiters(float $liters): self
    {
        $this->liters = $liters;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getKilometers(): ?float
    {
        return $this->kilometers;
    }

    public function setKilometers(float $kilometers): self
    {
        $this->kilometers = $kilometers;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        if ($this->picturePath !== null) {

            return "https://static.gassapp.nl/" . $this->getPublicUploadDir() . $this->picturePath;

        } else {

            return "";

        }
    }

    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param mixed $lat
     */
    public function setLat($lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return mixed
     */
    public function getLong()
    {
        return $this->long;
    }

    /**
     * @param mixed $long
     */
    public function setLong($long): void
    {
        $this->long = $long;
    }


    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->setDate(new \DateTime('now'));

        if ($this->getPicture() !== null) {

            $filePath = $this->uploadFile($this->getPicture());

            $this->setPicturePath($filePath);

        }

    }

    public function jsonSerialize()
    {
        return [
            '_id' => $this->getId(),
            'date' => $this->getDate(),
            'liters' => number_format($this->getLiters(),2, '.', ''),
            'price' => number_format($this->getPrice(), 2, '.', ''),
            'kilometers' => number_format($this->getKilometers(), 2, '.', ''),
            'picturePath' => $this->getPicturePath(),
            'user' => $this->getUser()
        ];
    }

}
