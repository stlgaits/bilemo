<?php

namespace App\Security\Voter;

use App\Entity\Customer;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerVoter extends Voter
{
    public const DELETE_CUSTOMER = 'DELETE_CUSTOMER';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute == self::DELETE_CUSTOMER
            && $subject instanceof Customer;
    }

    /**
     * @throws Exception
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Customer $subject */

        if ($attribute == self::DELETE_CUSTOMER) {
            if ($subject->getAccount() === $user->getAccount()) {
                return true;
            }
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }
            return false;
        }
        throw new Exception(sprintf('Unhandled attribute "%s"', $attribute));
    }
}
