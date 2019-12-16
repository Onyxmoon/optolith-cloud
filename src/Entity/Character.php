<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Validator\IsValidOwner;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     collectionOperations={
 *          "post"={"security"="is_granted('ROLE_USER')"}
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN') or object.getOwner() == user"},
 *         "delete"={"security"="is_granted('ROLE_ADMIN') or object.getOwner() == user"},
 *         "put"={"security_post_denormalize"="is_granted('ROLE_ADMIN') or (object.getOwner() == user and previous_object.getOwner() == user)"},
 *         "patch"={"security_post_denormalize"="is_granted('ROLE_ADMIN') or (object.getOwner() == user and previous_object.getOwner() == user)"}
 *     },
 *     normalizationContext={"groups"={"character:read"}},
 *     denormalizationContext={"groups"={"character:write"}},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CharacterRepository")
 * @ORM\EntityListeners({"App\Doctrine\CharacterEntityListener"})
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
     * @Groups({"character:read", "character:write"})
     * @ORM\Column(type="string", length=255)
     */
    private $displayName;

    /**
     * @Groups({"character:read", "character:write"})
     * @ORM\Column(type="string", length=255)
     */
    private $clientVersion;

    /**
     * @Groups({"character:read", "character:write"})
     * @ORM\Column(type="json")
     */
    private $data = [];

    // Maybe autofill from server?
    /**
     * @Groups({"character:read", "character:write"})
     * @ORM\Column(type="datetime")
     */
    private $lastModificationDate;

    /**
     * @Groups({"character:read", "character:write"})
     * @ORM\Column(type="text")
     */
    private $checksum;

    /**
     * @Groups({"character:read", "admin:write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="characters")
     * @ORM\JoinColumn(nullable=false)
     * @IsValidOwner()
     */
    private $owner;

    /**
     * @Groups({"character:read", "character:write"})
     * @ORM\ManyToOne(targetEntity="App\Entity\MediaObject")
     */
    private $avatar;

    public function getId(): ?string
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

    public function getAvatar(): ?MediaObject
    {
        return $this->avatar;
    }

    public function setAvatar(?MediaObject $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }


}
