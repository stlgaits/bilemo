<?php

namespace App\Security\Voter;

use App\Entity\User;
use Exception;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const USER_VIEW = 'USER_VIEW';
    public const USER_CREATE = 'USER_CREATE';
    public const USER_DELETE = 'USER_DELETE';

    private Security $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [
            self::USER_VIEW,
            self::USER_CREATE,
            self::USER_DELETE
            ])
            && $subject instanceof User;
    }

    /**
     * @throws Exception
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var User $subject */

        switch ($attribute) {
            case self::USER_VIEW:
                if($subject->getAccount() === $user->getAccount()) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                return false;
                break;
            case self::USER_CREATE:
                if($subject->getAccount() === $user->getAccount()) {
                    return true;
                }

                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                return false;
                break;
            case self::USER_DELETE:
                if ($subject === $user) {
                    return true;
                }
                if ($this->security->isGranted('ROLE_ADMIN')) {
                    return true;
                }
                break;
            return false;
        }

        throw new Exception(sprintf('Unhandled attribute "%s"', $attribute));
    }
}
