<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Service\Attribute\Required;

class SecurityController extends AbstractController
{
	/**
	 * @var AuthenticationUtils
	 */
	protected $authenticationUtils;

	#[Required]
	public function setAuthenticationUtils(AuthenticationUtils $authenticationUtils): self
	{
		$this->authenticationUtils = $authenticationUtils;

		return $this;
	}

    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@DevBaseUser/security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @return LogicException
     */
    #[Route(path: '/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
