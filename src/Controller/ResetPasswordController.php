<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Controller;

use DevBase\UserBundle\Entity\User;
use DevBase\UserBundle\Form\ChangePasswordFormType;
use DevBase\UserBundle\Form\ResetPasswordRequestFormType;
use DevBase\UserBundle\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractUserController
{
	use ResetPasswordControllerTrait;

	private $resetPasswordHelper;

	public function __construct(ResetPasswordHelperInterface $resetPasswordHelper)
	{
		$this->resetPasswordHelper = $resetPasswordHelper;
	}

	/**
	 * Display & process form to request a password reset.
	 */
	#[Route(path: '', name: 'app_forgot_password_request', methods: ['GET', 'POST'])]
	public function request(Request $request, MailerInterface $mailer): Response
	{
		$form = $this->createForm(ResetPasswordRequestFormType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			return $this->processSendingPasswordResetEmail(
				$form->get('email')->getData(),
				$mailer
			);
		}

		return $this->render('@DevBaseUser/reset_password/request.html.twig', [
			'requestForm' => $form->createView(),
		]);
	}

	/**
	 * Confirmation page after a user has requested a password reset.
	 */
	#[Route(path: '/verify-email', name: 'app_check_email', methods: ['GET', 'POST'])]
	public function checkEmail(): Response
	{
		// Generate a fake token if the user does not exist or someone hit this page directly.
		// This prevents exposing whether or not a user was found with the given email address or not
		if (null === ($resetToken = $this->getTokenObjectFromSession())) {
			$resetToken = $this->resetPasswordHelper->generateFakeResetToken();
		}

		return $this->render('@DevBaseUser/reset_password/check_email.html.twig', [
			'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
		]);
	}

	/**
	 * Validates and process the reset URL that the user clicked in their email.
	 */
	#[Route(path: '/reset/{token}', name: 'app_reset_password', methods: ['GET', 'POST'])]
	public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
	{
		if ($token) {
			// We store the token in session and remove it from the URL, to avoid the URL being
			// loaded in a browser and potentially leaking the token to 3rd party JavaScript.
			$this->storeTokenInSession($token);

			return $this->redirectToRoute('app_reset_password');
		}

		$token = $this->getTokenFromSession();
		if (null === $token) {
			throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
		}

		try {
			$user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
		} catch (ResetPasswordExceptionInterface $e) {
			$this->addFlash('reset_password_error', sprintf(
				'There was a problem validating your reset request - %s',
				$e->getReason()
			));

			return $this->redirectToRoute('app_forgot_password_request');
		}

		// The token is valid; allow the user to change their password.
		$form = $this->createForm(ChangePasswordFormType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// A password reset token should be used only once, remove it.
			$this->resetPasswordHelper->removeResetRequest($token);

			// Hash the plain password, and set it.
			/** @var User $user */
			$encodedPassword = $passwordHasher->hashPassword(
				$user,
				$form->get('plainPassword')->getData()
			);

			$user->setPassword($encodedPassword);
			$this->getEntityManager()->flush();

			// The session is cleaned up after the password has been changed.
			$this->cleanSessionAfterReset();

			$this->addFlash('success', 'Your password was successfully updated');

			return $this->redirectToRoute('app_login');
		}

		return $this->render('@DevBaseUser/reset_password/reset.html.twig', [
			'resetForm' => $form->createView(),
		]);
	}

	private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
	{
		$user = $this->em->getRepository(User::class)->findOneBy([
			'email' => $emailFormData,
		]);

		// Do not reveal whether a user account was found or not.
		if (!$user) {
			return $this->redirectToRoute('app_check_email');
		}

		try {
			$resetToken = $this->resetPasswordHelper->generateResetToken($user);
		} catch (ResetPasswordExceptionInterface $e) {
			// If you want to tell the user why a reset email was not sent, uncomment
			// the lines below and change the redirect to 'app_forgot_password_request'.
			// Caution: This may reveal if a user is registered or not.

			$this->addFlash('reset_password_error', sprintf(
				'There was a problem handling your password reset request - %s',
				$e->getReason()
			));

			return $this->redirectToRoute('app_check_email');
		}

		$email = (new TemplatedEmail())
			->from(new Address('no-reply@local.dev', 'Site Name'))
			->to($user->getEmail())
			->subject('Reset Password')
			->htmlTemplate('@DevBaseUser/reset_password/email.html.twig')
			->context([
				'resetToken' => $resetToken,
				'userName' => $user->getUsername()
			]);

		$mailer->send($email);

		return $this->redirectToRoute('app_check_email');
	}
}
