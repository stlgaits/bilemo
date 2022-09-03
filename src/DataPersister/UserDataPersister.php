<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

final class UserDataPersister implements DataPersisterInterface
{
    private DataPersisterInterface $dataPersister;
    private Security $security;

    public function  __construct(DataPersisterInterface $dataPersister, Security $security) {

        $this->dataPersister = $dataPersister;
        $this->security = $security;

    }

    /**
     * @inheritDoc
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    /**
     * @inheritDoc
     */
    public function persist($data, array $context = [])
    {
        $user = $this->security->getUser();
        $data->setAccount($user->getAccount());
        $this->dataPersister->persist($data);
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        $this->dataPersister->remove($data);
    }
}