<?php


namespace App\Service;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User as UserEntity;

class User
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var UserEntity
     */
    protected $user;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $entityManager->getRepository(UserEntity::class);
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param array $criteria
     * @return $this
     * @throws \Exception
     */
    public function loadUserFromCriteria(array $criteria)
    {
        $this->user = $this->userRepository->findOneBy($criteria);
        $this->checkUser();
        return $this;
    }

    /**
     * @param UserEntity $user
     * @return $this
     * @throws \Exception
     */
    public function loadUserFromEntity(UserEntity $user)
    {
        $this->user = $user;
        $this->checkUser();
        return $this;
    }

    /**
     * @return UserEntity
     */
    public function getLoadedUser()
    {
        return $this->user;
    }

    /**
     * @param string $plainNewPassword
     * @param bool $save
     * @return $this
     * @throws \Exception
     */
    public function setPassword(string $plainNewPassword, $save = true)
    {
        $this->checkUser();

        $this->user->setPassword(
            $this->passwordEncoder->encodePassword($this->user, $plainNewPassword)
        );

        if ($save) {
            $this->entityManager->persist($this->user);
            $this->entityManager->flush();
        }

        return $this;
    }

    /**
     * @param string $username
     * @param string $plainPassword
     * @return $this
     * @throws \Exception
     */
    public function createUser(string $username, string $plainPassword)
    {
        $this->user = new UserEntity();
        $this->user->setUsername($username);
        $this->setPassword($plainPassword, false);

        $this->entityManager->persist($this->user);
        $this->entityManager->flush();

        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function checkUser()
    {
        if (!is_object($this->user) || !$this->user instanceof UserEntity)
            throw new \Exception('No user loaded');
    }
}
