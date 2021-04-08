<?php

namespace ICS\SsiBundle\EventListener;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use ICS\SsiBundle\Annotation\Log as AnnotationLog;
use ICS\SsiBundle\Entity\Log;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DatabaseActivitySubscriber implements EventSubscriberInterface
{
    private $container;
    private $annotationReader;

    public function __construct(ContainerInterface $container, Reader $annotationReader)
    {
        $this->container = $container;
        $this->annotationReader = $annotationReader;
    }

    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::postUpdate,
            Events::preRemove,
        ];
    }

    // callback methods must be called exactly like the events they listen to;
    // they receive an argument of type LifecycleEventArgs, which gives you access
    // to both the entity object of the event and the entity manager itself
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($this->isLoggable($entity, 'add')) {
            $this->container->get('monolog.logger.db')->info('Add entity ' . $this->getProperty($entity) . ' of type ' . get_class($entity) . ' to database');
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($this->isLoggable($entity, 'delete')) {
            $this->container->get('monolog.logger.db')->info('Remove entity ' . $this->getProperty($entity) . ', of type ' . get_class($entity) . ' from database');
        }
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($this->isLoggable($entity, 'update')) {
            $this->container->get('monolog.logger.db')->info('Update ' . $this->getProperty($entity) . ', of type ' . get_class($entity) . ' to database');
        }
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
    }

    public function getProperty($entity)
    {
        $annotations = $this->annotationReader->getClassAnnotations(new ReflectionClass($entity));
        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        foreach ($annotations as $annotation) {
            if (is_a($annotation, AnnotationLog::class)) {

                $property = $annotation->getProperty();

                return $propertyAccessor->getValue($entity, $property);
            }
        }

        return "";
    }

    public function isLoggable($entity, $action): bool
    {
        $annotations = $this->annotationReader->getClassAnnotations(new ReflectionClass($entity));

        foreach ($annotations as $annotation) {
            if (is_a($annotation, AnnotationLog::class)) {
                $logAnnotation = $annotation;
                if ($logAnnotation != null && (in_array($action, $logAnnotation->getActions()) || in_array('all', $logAnnotation->getActions()))) {
                    return true;
                }
            }
        }

        return false;
    }
}
