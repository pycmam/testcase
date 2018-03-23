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


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }


    /**
     * @param int $amount
     *
     * @return Lock
     */
    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }


    /**
     * @return \DateTime|null
     */
    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }


    /**
     * @param \DateTime $created
     *
     * @return Lock
     */
    public function setCreated(\DateTime $created): self
    {
        $this->created = $created;

        return $this;
    }


    /**
     * @return \DateTime|null
     */
    public function getApproved(): ?\DateTime
    {
        return $this->approved;
    }


    /**
     * @param \DateTime|null $approved
     *
     * @return Lock
     */
    public function setApproved(?\DateTime $approved): self
    {
        $this->approved = $approved;

        return $this;
    }


    /**
     * @return Account|null
     */
    public function getSource(): ?Account
    {
        return $this->source;
    }


    /**
     * @return Account|null
     */
    public function getDestination(): ?Account
    {
        return $this->destination;
    }


    /**
     * @param Account|null $account
     *
     * @return Lock
     */
    public function setSource(?Account $account): self
    {
        $this->source = $account;

        return $this;
    }


    /**
     * @param Account|null $account
     *
     * @return Lock
     */
    public function setDestination(?Account $account): self
    {
        $this->destination = $account;

        return $this;
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
       return sprintf('%s | Id %d, From: %s, To: %s: Amount: %d, Approved: %s',
           $this->getCreated()->format('d.m.Y H:i:s'), $this->getId(),
           $this->getSource() ? $this->getSource()->getUsername() : '---',
           $this->getDestination() ? $this->getDestination()->getUsername() : '---', $this->getAmount(),
           $this->getApproved() ? $this->getApproved()->format('d.m.Y H:i:s') : '---');
    }

}
