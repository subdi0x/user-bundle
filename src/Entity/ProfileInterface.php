<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Entity;

interface ProfileInterface
{
	public function getId(): ?int;

    /**
     * setUser.
     *
     * @param UserInterface $user
     *
     * @return ProfileInterface
     */
    public function setUser(UserInterface $user);

    /**
     * getUser.
     *
     * @return UserInterface
     */
    public function getUser();
}
