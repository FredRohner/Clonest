<?php

namespace App\Event;

use App\Entity\Post;
use Cocur\Slugify\Slugify;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class PostSubscriber implements EventSubscriber
{
    private $slugger;

    private $security;

    public function __construct(Slugify $slugger, Security $security)
    {
        $this->slugger = $slugger;
        $this->security = $security;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Post) {
            $entity->setUser(
                $entity->getUser() ?? $this->security->getUser()
            );

            $entity->setCreatedAt(
                $entity->getCreatedAt() ?? new \DateTime()
            );

            $entity->setSlug(
                $this->slugger->slugify($entity->getTitle())
            );
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Post) {
            $entity->setSlug(
                $this->slugger->slugify($entity->getTitle())
            );
        }
    }
}
