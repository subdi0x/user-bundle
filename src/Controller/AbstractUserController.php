<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Controller;

use DevBase\UserBundle\Entity\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractUserController extends BaseController
{
	/**
	 * @var EntityManagerInterface
	 */
	protected $em;

	#[Required]
	public function setEntityManager(EntityManagerInterface $entityManager): self
	{
		$this->em = $entityManager;

		return $this;
	}

	//public function __construct(private EntityManagerInterface $entityManager) {}

	protected function getEntityManager(): EntityManagerInterface
	{
		return $this->em;
	}

	public function getAppUser(): UserInterface
	{
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new RuntimeException('Not an app user');
		}

		return $user;
	}
}