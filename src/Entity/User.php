<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Entity;

use DevBase\UserBundle\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DevBase\UserBundle\Model\AbstractUser as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('username', message: 'Username not available')]
#[UniqueEntity('email', message: 'Email not available"')]
class User extends BaseUser implements UserInterface
{
	#[ORM\OneToOne(inversedBy: 'user', targetEntity: Profile::class, cascade: ['persist', 'remove'])]
	#[ORM\JoinColumn(nullable: false)]
	private $profile;

	public function getProfile(): ?ProfileInterface
	{
		return $this->profile;
	}

	public function setProfile(ProfileInterface $profile): self
	{
		$this->profile = $profile;

		return $this;
	}
}