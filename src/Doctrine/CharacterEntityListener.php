<?php

namespace App\Doctrine;

use App\Entity\Character;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Security\Core\Security;

class CharacterEntityListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function preFlush(Character $character, PreFlushEventArgs $event)
    {
        if ($character->getOwner()) {
            return;
        }

        if ($this->security->getUser()) {
            dump($this->security->getUser());
            $character->setOwner($this->security->getUser());
        }

        $character->setLastModificationDate(new \DateTimeImmutable("now"));
    }

    public function preUpdate(Character $character, PreUpdateEventArgs $event)
    {
        if ($character->getOwner()) {
            return;
        }

        if ($this->security->getUser()) {
            dump($this->security->getUser());
            $character->setOwner($this->security->getUser());
        }

        $character->setLastModificationDate(new \DateTimeImmutable("now"));
    }
}
