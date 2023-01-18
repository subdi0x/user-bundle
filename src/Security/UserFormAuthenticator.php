<?php

declare(strict_types=1);

namespace DevBase\UserBundle\Security;

use DevBase\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    /**
     * @var string
     */
    private const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
        private UserAuthenticatorInterface $userAuthenticator
    ) {
    }

    /**
     * Manual login
     */
    public function login(User $user, Request $request): ?Response
    {
        return $this->userAuthenticator->authenticateUser(
            $user,
            $this,
            $request
        );
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($request->isXmlHttpRequest()) {
            $result = ['success' => true];

            return new JsonResponse($result);
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_event_list'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->isXmlHttpRequest()) {
            $result = [
                'success' => false,
                'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security'),
            ];

            return new JsonResponse($result);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
