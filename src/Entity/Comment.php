<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $images = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isInterested;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasVisited;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PointOfInterest", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pointOfInterest;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

        return $this;
    }

    public function getIsInterested(): ?bool
    {
        return $this->isInterested;
    }

    public function setIsInterested(bool $isInterested): self
    {
        $this->isInterested = $isInterested;

        return $this;
    }

    public function getHasVisited(): ?bool
    {
        return $this->hasVisited;
    }

    public function setHasVisited(bool $hasVisited): self
    {
        $this->hasVisited = $hasVisited;

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

    public function getPointOfInterest(): ?PointOfInterest
    {
        return $this->pointOfInterest;
    }

    public function setPointOfInterest(?PointOfInterest $pointOfInterest): self
    {
        $this->pointOfInterest = $pointOfInterest;

        return $this;
    }
}
