<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $busyByPid;


    /**
     * @return int
     */
    public function getBusyByPid(): int
    {
        return $this->busyByPid;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }


    /**
     * @param string $username
     *
     * @return Account
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUsername();
    }

}
