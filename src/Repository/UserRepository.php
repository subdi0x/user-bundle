<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Repository;

use DevBase\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserLoaderInterface
{
	public function __construct(ManagerRegistry $registry)
	{
		parent::__construct($registry, User::class);
	}

	public function flush(): void
	{
		$this->_em->flush();
	}

	public function persist(User $entity): void
	{
		$this->_em->persist($entity);
	}

	public function add(User $entity, bool $flush = true): void
	{
		$this->_em->persist($entity);
		if ($flush)
		{
			$this->_em->flush();
		}
	}

	public function remove(User $entity, bool $flush = true): void
	{
		$this->_em->remove($entity);
		if ($flush)
		{
			$this->_em->flush();
		}
	}

	public function loadUserByIdentifier(string $identifier): ?UserInterface
	{
		return $this
			->createQueryBuilder('u')
			->where('u.username = :usernameOrEmail OR u.email = :usernameOrEmail')
			->setParameter('usernameOrEmail', $identifier)
			->getQuery()
			->getOneOrNullResult();
	}

	/**
	 * Used to upgrade (rehash) the user's password automatically over time.
	 * @param PasswordAuthenticatedUserInterface $user
	 * @param string $newHashedPassword
	 */
	public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
	{
		if (!$user instanceof User) {
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
		}

		$user->setPassword($newHashedPassword);
		$this->_em->persist($user);
		$this->_em->flush();
	}
}