<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Entity\Mediatheque\File;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Vich\UploaderBundle\Storage\StorageInterface;

class ResolveFileUrlSubscriber// implements EventSubscriberInterface
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE]
        ];
    }

    public function onPreSerialize(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response || !$request->attributes->getBoolean('_api_respond', true)) {
            return;
        }

        $attributes = RequestAttributesExtractor::extractAttributes($request);
        if (!$attributes || !is_a($attributes['resource_class'], File::class, true)) {
            return;
        }

        $fileObjects = $controllerResult;

        if (!is_iterable($fileObjects)) {
            $fileObjects = [$fileObjects];
        }

        foreach ($fileObjects as $fileObject) {
            if (!$fileObject instanceof File) {
                continue;
            }

            $url = $this->storage->resolveUri($fileObject, 'file');
            $fileObject->setUrl($url);
        }
    }
}