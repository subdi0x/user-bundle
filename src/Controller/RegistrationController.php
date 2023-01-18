<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Controller;

use DevBase\UserBundle\Entity\Profile;
use DevBase\UserBundle\Entity\User;
use DevBase\UserBundle\Form\RegistrationFormType;
use DevBase\UserBundle\Security\EmailVerifier;
use DevBase\UserBundle\Security\UserFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

//class RegistrationController extends AbstractController
class RegistrationController extends AbstractUserController
{
	public function __construct(private EmailVerifier $emailVerifier) {}

	#[Route(path: '/register', name: 'app_register', methods: ['GET', 'POST'])]
	public function index(Request $request, UserPasswordHasherInterface $passwordHasher, UserFormAuthenticator $authenticator): Response
	{
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user
				->setPassword(
					$passwordHasher->hashPassword(
						$user,
						$form->get('plainPassword')->getData()
					)
				);

			$profile = new Profile();
			$profile->setUser($user);
			$profile->setGender(true);
			$profile->setVerified(false);
			$user->setProfile($profile);

			$entityManager = $this->getEntityManager();
			$entityManager->persist($user);
			$entityManager->flush();

			// generate a signed url and email it to the user
			$this->emailVerifier->sendEmailConfirmation($user);
			// do anything else you need here, like send an email

			$response = $authenticator->login(
				$user,
				$request,
			);

			return $response ?? $this->redirectToRoute('app_index');
		}

		return $this->render('@DevBaseUser/registration/register.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route(path: '/verify-email', name: 'app_verify_email', methods: ['GET', 'POST'])]
	public function verifyUserEmail(Request $request): Response
	{
		try {
			/** @var User $user */
			$user = $this->getAppUser();
			$this->emailVerifier->handleEmailConfirmation($request, $user);
		} catch (VerifyEmailExceptionInterface $verifyEmailException) {
			$this->addFlash('error', $verifyEmailException->getReason());

			return $this->redirectToRoute('app_register');
		}

		$this->addFlash('success', 'Your email a bien été vérifié.');

		return $this->redirectToRoute('app_event_list');
	}
}
