<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Entity;

use DevBase\UserBundle\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile implements ProfileInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private $id;

	#[ORM\Column(type: 'datetime', nullable: true)]
	#[Assert\Type(\DateTimeInterface::class)]
	private $updatedAt;

	#[ORM\OneToOne(mappedBy: 'profile', targetEntity: User::class, cascade: ['persist', 'remove'])]
	private $user;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private $firstName;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private $lastName;

	#[ORM\Column(type: 'text', nullable: true)]
	private $about;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	private $url;

	#[ORM\Column(type: 'boolean')]
	private $gender;

	#[ORM\Column(type: 'boolean', nullable: true)]
	private $verified;

	#[ORM\Column(type: 'datetime', nullable: true)]
	#[Assert\NotBlank]
	private $birthday;

	public function __serialize()
	{
		return [$this];
	}

	public function __unserialize($serialized)
	{
		return [$this];
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUsername(): string
	{
		return $this->getUser()->getUsername();
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	public function getUser(): ?UserInterface
	{
		return $this->user;
	}

	public function setUser(UserInterface $user): self
	{
		$this->user = $user;

		// set the owning side of the relation if necessary
		if ($user->getProfile() !== $this) {
			$user->setProfile($this);
		}

		return $this;
	}

	public function getFirstName(): ?string
	{
		return $this->firstName;
	}

	public function setFirstName(?string $firstName): self
	{
		$this->firstName = $firstName;

		return $this;
	}

	public function getLastName(): ?string
	{
		return $this->lastName;
	}

	public function setLastName(?string $lastName): self
	{
		$this->lastName = $lastName;

		return $this;
	}

	public function getAbout(): ?string
	{
		return $this->about;
	}

	public function setAbout(?string $about): self
	{
		$this->about = $about;

		return $this;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function setUrl(?string $url): self
	{
		$this->url = $url;

		return $this;
	}

	public function getGender(): ?bool
	{
		return $this->gender;
	}

	public function setGender(bool $gender): self
	{
		$this->gender = $gender;

		return $this;
	}

	public function getVerified(): ?bool
	{
		return $this->verified;
	}

	public function setVerified(?bool $verified): self
	{
		$this->verified = $verified;

		return $this;
	}

	public function getBirthday(): ?\DateTimeInterface
	{
		return $this->birthday;
	}

	public function setBirthday(?\DateTimeInterface $birthday): self
	{
		$this->birthday = $birthday;

		return $this;
	}
}