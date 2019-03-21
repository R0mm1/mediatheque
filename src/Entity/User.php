<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="PaperBook", mappedBy="owner", cascade={"persist", "remove", "merge"})
     */
    protected $books;

    public function asArray(array $aFields = null)
    {
        $aUser = [];
        foreach (['Id', 'Username'] as $field) {
            if (is_null($aFields) || in_array($field, $aFields)) {
                $aUser[lcfirst($field)] = $this->{"get$field"}();
            }
        }
        return $aUser;
    }
}
