<?php

namespace App\EventListener\SwaggerUi;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class BackupControllerListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $event->getRequest()->attributes->set('_controller_bck', $event->getRequest()->attributes->get('_controller'));
    }
}
