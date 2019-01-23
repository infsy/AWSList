<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AWSObjectRepository")
 */
class AWSObject
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $AWSId;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $AWSType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AWSName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AWSRegion;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $AWSSubscription;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $AWSDeletionTime;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $AWSFirstDetection;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $AWSLastDetection;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAWSId(): ?string
    {
        return $this->AWSId;
    }

    public function setAWSId(string $AWSId): self
    {
        $this->AWSId = $AWSId;

        return $this;
    }

    public function getAWSType(): ?string
    {
        return $this->AWSType;
    }

    public function setAWSType(?string $AWSType): self
    {
        $this->AWSType = $AWSType;

        return $this;
    }

    public function getAWSName(): ?string
    {
        return $this->AWSName;
    }

    public function setAWSName(string $AWSName): self
    {
        $this->AWSName = $AWSName;

        return $this;
    }

    public function getAWSDeletionTime(): ?\DateTimeInterface
    {
        return $this->AWSDeletionTime;
    }

    public function setAWSDeletionTime(?\DateTimeInterface $AWSDeletionTime): self
    {
        $this->AWSDeletionTime = $AWSDeletionTime;

        return $this;
    }

    public function getAWSFirstDetection(): ?\DateTimeInterface
    {
        return $this->AWSFirstDetection;
    }

    public function setAWSFirstDetection(\DateTimeInterface $AWSFirstDetection): self
    {
        $this->AWSFirstDetection = $AWSFirstDetection;

        return $this;
    }

    public function getAWSLastDetection(): ?\DateTimeInterface
    {
        return $this->AWSLastDetection;
    }

    public function setAWSLastDetection(\DateTimeInterface $AWSLastDetection): self
    {
        $this->AWSLastDetection = $AWSLastDetection;

        return $this;
    }

    public function getAWSRegion(): ?string
    {
        return $this->AWSRegion;
    }

    public function setAWSRegion(?string $AWSRegion): self
    {
        $this->AWSRegion = $AWSRegion;

        return $this;
    }

    public function getAWSSubscription(): ?string
    {
        return $this->AWSSubscription;
    }

    public function setAWSSubscription(?string $AWSSubscription): self
    {
        $this->AWSSubscription = $AWSSubscription;

        return $this;
    }
}
