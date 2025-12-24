<?php

declare(strict_types=1);

namespace App\Auth\Security;

use App\Auth\AuthManager;
use App\Domain\Exception\UnauthorizedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Throwable;

class Authenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly LoggerInterface $logger
    ) {
    }

    private const TOKEN_PATTERN = '/^Bearer (.{1,1024})$/';

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');

        if ($authorizationHeader === null) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        if (preg_match(self::TOKEN_PATTERN, $authorizationHeader, $matches)) {
            $token = $matches[1];
        } else {
            throw new CustomUserMessageAuthenticationException('Authentication token not valid.');
        }

        return new SelfValidatingPassport(new UserBadge($token, function (string $userIdentifier) {
            try {
                $accessTokenEntity = $this->authManager->authenticateAccessTokenFromString($userIdentifier);

                return $accessTokenEntity->getRefreshToken()->getUser();
            } catch (UnauthorizedException $e) {
                throw new CustomUserMessageAuthenticationException($e->getMessage());
            } catch (Throwable $e) {
                $this->logger->critical(
                    $e->getMessage(),
                    ['exception' => $e]
                );

                throw new CustomUserMessageAuthenticationException('Cannot find User.');
            }
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
