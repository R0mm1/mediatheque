<?php


namespace App\Controller\User;

use Symfony\Component\Security\Core\Security;

class GetCurrent
{
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke()
    {
        return $this->security->getUser();
    }
}