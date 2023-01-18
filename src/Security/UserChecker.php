<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Security;

use DevBase\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @return void
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isEnabled()) {
            $exception = new DisabledException('Your account has been disabled by an administrator');
            $exception->setUser($user);
            throw $exception;
        }
    }

    /**
     * @return void
     */
    public function checkPostAuth(UserInterface $user)
    {
    }
}
