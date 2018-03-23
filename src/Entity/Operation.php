<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OperationRepository")
 */
class Operation
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Account", inversedBy="operations", cascade={"all"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;


    public function getAccount(): Account
    {
        return $this->account;
    }


    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAmount(): int
    {
        return $this->amount;
    }


    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }


    public function getCreated()
    {
        return $this->created;
    }


    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }

}
