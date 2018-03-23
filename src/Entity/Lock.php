<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LockRepository")
 */
class Lock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $approved;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     * @ORM\JoinColumn(nullable=true)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account")
     * @ORM\JoinColumn(nullable=true)
     */
    private $destination;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAmount(): ?int
    {
        return $this->amount;
    }


    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }


    public function getCreated(): ?\DateTimeImmutable
    {
        return $this->created;
    }


    public function setCreated(\DateTimeImmutable $created): self
    {
        $this->created = $created;

        return $this;
    }


    public function getApproved(): ?\DateTimeImmutable
    {
        return $this->approved;
    }


    public function setApproved(?\DateTimeImmutable $approved): self
    {
        $this->approved = $approved;

        return $this;
    }


    public function getSource(): ?Account
    {
        return $this->source;
    }


    public function getDestination(): ?Account
    {
        return $this->destination;
    }


    public function setSource(Account $account): self
    {
        $this->source = $account;

        return $this;
    }


    public function setDestination(Account $account): self
    {
        $this->destination = $account;

        return $this;
    }

}
