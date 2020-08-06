<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Action\PatchUserCredentialsAction;
use App\Action\CreateUserObjectAction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @UniqueEntity(fields={"email"})
 * @ApiResource(
 *     collectionOperations={
 *          "post"={
 *              "denormalization_context"={"groups"={"user:create"}},
 *              "validation_groups"={"user:create"},
 *              "openapi_context"={
 *                  "description" = "Registers a new user. The user receives an activation request at the e-mail address provided, which must be confirmed. If this request has been fulfilled, the user account is operational.",
 *              },
 *              "security"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *              "controller"=CreateUserObjectAction::class
 *          }
 *     },
 *     itemOperations={
 *         "get"={"security"="is_granted('ROLE_ADMIN') or object == user"},
 *         "delete"={"security"="is_granted('ROLE_ADMIN') or object == user"},
 *         "put"=
 *              {"security_post_denormalize"="is_granted('ROLE_ADMIN') or (object == user)",
 *              "denormalization_context"={"groups"={"user:putpatch"}}
 *          },
 *         "patch"={
 *              "security_post_denormalize"="is_granted('ROLE_ADMIN') or (object == user)",
 *              "denormalization_context"={"groups"={"user:putpatch"}}
 *          },
 *          "patch_credentials"={
 *              "deserialize"=false,
 *              "security"="is_granted('ROLE_USER')",
 *              "method"="PATCH",
 *              "path"="/users/{id}/credentials",
 *              "controller"=PatchUserCredentialsAction::class,
 *              "denormalization_context"={"groups"={"user:credentials"}},
 *              "openapi_context"={
 *                  "summary" = "Updates user credentials",
 *                  "description" = "Updates sensitive data of the user and therefore requires the user's current password in addition to a valid session. If the e-mail address changes, a confirmation from the user is required to verify the validity of the address. A confirmation request is sent to the new e-mail address. If the user confirms the change with the confirmation link, the new e-mail address becomes final can be used for future logins. Please note that from this point on all previously valid sessions become invalid and the user must log in again. A password change will take place immediately if the current password was correct.",
 *                  "parameters"={
 *                      {
 *                          "name" = "id",
 *                          "in" = "path",
 *                          "type" = "string",
 *                          "required" = "true"
 *                      }
 *                  },
 *                  "requestBody" = {
 *                      "required" = "true",
 *                      "content" = {
 *                          "application/json" = {
 *                              "schema" = {
 *                                  "type" = "object",
 *                                  "required" = {"currentPassword"},
 *                                  "properties" = {
 *                                      "newEmail" = {
 *                                          "type" = "string"
 *                                      },
 *                                      "newPassword" = {
 *                                          "type" = "string"
 *                                      },
 *                                      "currentPassword" = {
 *                                          "type" = "string"
 *                                      }
 *                                  }
 *                              }
 *                          }
 *                      }
 *                  }
 *              }
 *          }
 *     },
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     */
    private $id;

    /**
     * @Groups({"user:read", "user:write", "user:create"})
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     * @Assert\NotBlank(groups={"user:create"})
     */
    private $email;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Groups({"user:write", "user:create"})
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"user:create"})
     */
    private $plainPassword;

    /**
     * @Groups({"user:read", "user:write", "user:putpatch", "user:create"})
     * @ORM\Column(type="text")
     * @Assert\NotBlank(groups={"user:create"})
     */
    private $displayName;

    /**
     * @Groups({"user:read"})
     * @ORM\OneToMany(targetEntity="App\Entity\Character", mappedBy="owner", orphanRemoval=true)
     */
    private $characters;

    /**
     * @Groups({"user:read", "user:write", "user:putpatch"})
     * @ORM\ManyToOne(targetEntity="App\Entity\MediaObject")
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MediaObject", mappedBy="owner", orphanRemoval=true)
     */
    private $mediaObjects;

    /**
     * @Groups({"user:read", "admin:write"})
     * @ORM\Column(type="boolean")
     */
    private $isActive = false;

    /**
     * @Groups({"user:read", "admin:write"})
     * @ORM\Column(type="datetime_immutable")
     */
    private $registrationDate;

    /**
     * @Groups({"admin:read", "admin:write"})
     * @ORM\Column(type="guid", nullable=true)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $confirmationSecret;

    /**
     * @var
     * @Groups({"admin:read", "admin:write"})
     * @Assert\Choice({"account", "email"})
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $confirmationType = "account";

    /**
     * @var boolean Testifies if the e-mail address has been confirmed
     * @Groups({"user:read", "admin:write"})
     * @ORM\Column(type="boolean")
     */
    private $confirmedEmail = false;

    /**
     * @var string E-Mail address designated for change
     * @Groups({"user:read"})
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email()
     */
    private $newEmail;

    /**
     * @var string IETF language tag (BCP47)
     * @Groups({"user:read", "user:write", "user:putpatch", "user:create"})
     * @ORM\Column(type="string", length=42)
     * @Assert\Locale(
     *     canonicalize = true,
     *     groups={"user:putpatch", "user:create"}
     * )
     * @Assert\NotBlank(groups={"user:create"})
     */
    private $locale;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->mediaObjects = new ArrayCollection();
        $this->confirmationSecret = $this->gen_uuid();
        $this->registrationDate = new \DateTimeImmutable("now");
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->displayName;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
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

    /**
     * @return Collection|Character[]
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setOwner($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->contains($character)) {
            $this->characters->removeElement($character);
            // set the owning side to null (unless already changed)
            if ($character->getOwner() === $this) {
                $character->setOwner(null);
            }
        }

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

    /**
     * @return Collection|MediaObject[]
     */
    public function getMediaObjects(): Collection
    {
        return $this->mediaObjects;
    }

    public function addMediaObject(MediaObject $mediaObject): self
    {
        if (!$this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects[] = $mediaObject;
            $mediaObject->setOwner($this);
        }

        return $this;
    }

    public function removeMediaObject(MediaObject $mediaObject): self
    {
        if ($this->mediaObjects->contains($mediaObject)) {
            $this->mediaObjects->removeElement($mediaObject);
            // set the owning side to null (unless already changed)
            if ($mediaObject->getOwner() === $this) {
                $mediaObject->setOwner(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeImmutable
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeImmutable $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getConfirmationSecret(): ?string
    {
        return $this->confirmationSecret;
    }

    public function setConfirmationSecret(string $confirmationSecret): self
    {
        $this->confirmationSecret = $confirmationSecret;

        return $this;
    }

    public function getConfirmationType(): ?string
    {
        return $this->confirmationType;
    }

    public function setConfirmationType(?string $confirmationType): self
    {
        $this->confirmationType = $confirmationType;

        return $this;
    }

    public function getConfirmedEmail(): ?bool
    {
        return $this->confirmedEmail;
    }

    public function setConfirmedEmail(bool $confirmedEmail): self
    {
        $this->confirmedEmail = $confirmedEmail;

        return $this;
    }

    public function getNewEmail(): ?string
    {
        return $this->newEmail;
    }

    public function setNewEmail(?string $newEmail): self
    {
        $this->newEmail = $newEmail;

        return $this;
    }

    public function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,

            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function __toString()
    {
        return (string)$this->displayName;
    }

}
