<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CharacterRepository")
 */
class Character
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $displayName;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $displayPicture;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $clientVersion;

    /**
     * @ORM\Column(type="json")
     */
    private $data = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastModificationDate;

    /**
     * @ORM\Column(type="text")
     */
    private $checksum;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="characters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getDisplayPicture()
    {
        return $this->displayPicture;
    }

    public function setDisplayPicture($displayPicture): self
    {
        $this->displayPicture = $displayPicture;

        return $this;
    }

    public function getClientVersion(): ?string
    {
        return $this->clientVersion;
    }

    public function setClientVersion(string $clientVersion): self
    {
        $this->clientVersion = $clientVersion;

        return $this;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getLastModificationDate(): ?\DateTimeInterface
    {
        return $this->lastModificationDate;
    }

    public function setLastModificationDate(\DateTimeInterface $lastModificationDate): self
    {
        $this->lastModificationDate = $lastModificationDate;

        return $this;
    }

    public function getChecksum(): ?string
    {
        return $this->checksum;
    }

    public function setChecksum(string $checksum): self
    {
        $this->checksum = $checksum;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
