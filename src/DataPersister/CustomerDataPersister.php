<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;

final class CustomerDataPersister implements DataPersisterInterface
{
    private EntityManagerInterface $entityManager;
    private DataPersisterInterface $dataPersister;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, DataPersisterInterface $dataPersister, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->dataPersister = $dataPersister;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof Customer;
    }

    /**
     * @inheritDoc
     * @param Customer $data
     */
    public function persist($data)
    {
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
        $this->dataPersister->remove($data);
    }
}

