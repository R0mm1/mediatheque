<?php
/**
 * Created by PhpStorm.
 * User: romain2
 * Date: 15/09/18
 * Time: 14:47
 */

namespace App\Entity\Security;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
}