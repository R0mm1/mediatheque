<?php

namespace App\EventListener\SwaggerUi;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class RestoreControllerListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->getRequest()->attributes->get('_route') !== 'api_doc') {
            $event->getRequest()->attributes->set('_controller', $event->getRequest()->attributes->get('_controller_bck'));
        }
    }
}
