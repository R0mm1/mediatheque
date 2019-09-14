<?php


namespace App\EventListener;


use App\Entity\Book\BookNotation;
use App\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

/**
 * The purpose of this listener is to change the user with the current user after the data have been retrived by API
 * Platform but before they are persisted in the database. This way, a book notation will always be created for the
 * logged in user.
 */
class BookNotationRequestReceivedListener
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        if(is_object($controllerResult) && $controllerResult instanceof BookNotation){
            $controllerResult->setUser($this->user);
            $event->setControllerResult($controllerResult);
        }
    }
}
