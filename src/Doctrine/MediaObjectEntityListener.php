<?php

namespace App\Doctrine;

use App\Entity\Character;
use App\Entity\MediaObject;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Security\Core\Security;

class MediaObjectEntityListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function preFlush(MediaObject $mediaObject, PreFlushEventArgs $event)
    {
        if ($mediaObject->getOwner()) {
            return;
        }

        if ($this->security->getUser()) {
            $mediaObject->setOwner($this->security->getUser());
        }
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
    }
}
