<?php

namespace BWC\Share\Symfony\Security\User;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * Encodes account password before saving
 * Must be tagged in service definition with
 *    - { name: doctrine.event_subscriber }
 */
class AccountListener implements EventSubscriber
{
    /** @var AccountPasswordEncoderInterface  */
    private $passwordEncoder;


    public function __construct(AccountPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->handleEvent($args);
    }



    protected function handleEvent(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof AdvancedUserAccountInterface) {
            $this->fixEntity($entity);
            $this->passwordEncoder->encodePassword($entity);
            if ($args instanceof PreUpdateEventArgs) {
                // We are doing a update, so we must force Doctrine to update the
                // changeset in case we changed something above
                $em   = $args->getEntityManager();
                $uow  = $em->getUnitOfWork();
                $meta = $em->getClassMetadata(get_class($entity));
                $uow->recomputeSingleEntityChangeSet($meta, $entity);
            }
        }
    }


    protected function fixEntity($entity)
    {

    }

} 