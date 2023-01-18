<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Entity;

interface UserInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int;

    /**
     * Set id.
     *
     * @param int $id The unique identifier
     *
     * @return AbstractEntity
     */
    public function setId(?int $id);

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored in a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array<Role|string> The user roles
     */
    public function getRoles(): array;

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return null|string The encoded password if any
     */
    public function getPassword(): ?string;

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return null|string The salt
     */
    public function getSalt(): ?string;

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): ?string;

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void;

	/**
	 * 设置详细资料.
	 *
	 * @param ProfileInterface $profile
	 *
	 * @return UserInterface
	 */
	public function setProfile(ProfileInterface $profile);

	/**
	 * 获取详细资料.
	 *
	 * @return ProfileInterface
	 */
	public function getProfile();
}
