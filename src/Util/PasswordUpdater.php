<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Util;

use DevBase\UserBundle\Model\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Class updating the hashed password in the user when there is a new password.
 */
class PasswordUpdater implements PasswordUpdaterInterface
{
    private $hasherFactory;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory)
    {
        $this->hasherFactory = $hasherFactory;
    }

    public function hashPassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();

        if ('' === $plainPassword || null === $plainPassword) {
            return;
        }

        $hasher = $this->hasherFactory->getPasswordHasher($user);
        $hashedPassword = $hasher->hash($plainPassword);
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }
}
