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


    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }


    /**
     * @param Account $account
     *
     * @return Operation
     */
    public function setAccount(Account $account): self
    {
        $this->account = $account;

        return $this;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }


    /**
     * @param int $amount
     *
     * @return Operation
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }


    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }


    /**
     * @param \DateTime $created
     *
     * @return Operation
     */
    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s | %s%d', $this->getCreated()->format('d.m.Y H:i:s'),
            $this->getAmount() > 0 ? '+' : '', $this->getAmount());
    }

}
