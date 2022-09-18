<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class UserDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports($data, $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @inheritDoc
     * @param User $data
     */
    public function persist($data, $context = [])
    {
        // this only applies in the context of API requests, when persisting an Entity manually,
        // the traditional method prevails (setting $password to a hashed pwd manually)
        if ($data->getPlainPassword()) {
            $data->setPassword($this->userPasswordHasher->hashPassword($data, $data->getPlainPassword()));
            // the plain password isn't saved into the database but this is used as a cautious, preventive measure
            // to avoid the plain password being serialised to the session via Security
            $data->eraseCredentials();
        }
        $user = $this->security->getUser();
        $data->setAccount($user->getAccount());
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
    }
}
