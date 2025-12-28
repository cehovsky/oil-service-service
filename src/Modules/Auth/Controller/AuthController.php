<?php

declare(strict_types=1);

namespace App\Modules\Auth\Controller;

use App\Auth\AuthManager;
use App\Auth\Exception\AuthenticationFailedException;
use App\Domain\Exception\InvalidArgumentException;
use App\Modules\Auth\DTO\AuthenticateRequestDTO;
use App\Modules\Auth\DTO\AuthenticateResponseDTO;
use App\Modules\Auth\DTO\TokenInfoResponseDTO;
use App\Modules\Auth\DTO\RefreshTokenResponseDTO;
use App\Modules\Auth\Factory\DTOFactory;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthManager $authManager,
        private readonly DTOFactory $dtoFactory,
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    /**
     * @throws ServerErrorHttpException
     */
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: AuthenticateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Auth',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'New refresh and access token.',
                content: new Model(
                    type: AuthenticateResponseDTO::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(
                    type: ErrorCollection::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/auth/authenticate',
        name: 'auth_authenticate',
        methods: ['POST']
    )]
    public function authenticateWithPassword(Request $request): JsonResponse
    {
        try {
            $authAuthenticateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                AuthenticateRequestDTO::class
            );

            $user = $this->authManager->authenticateUserLocally(
                $authAuthenticateRequestDTO->getEmail(),
                $authAuthenticateRequestDTO->getPassword(),
            );

            $accessToken = $this->authManager->createAccessTokenWithRefreshTokenAndFlush($user);
            $authAuthenticateResponseDTO = $this->dtoFactory->createAuthAuthenticateResponseDTO($accessToken);

            return $this->json($authAuthenticateResponseDTO);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection(
                $e->getErrorCollection()
            );
        } catch (AuthenticationFailedException | UnauthorizedException) {
            throw new UnauthorizedHttpException('Bearer', 'Permission denied');
        } catch (InvalidDataException | InvalidArgumentException) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @throws ServerErrorHttpException
     * @throws UnauthorizedHttpException
     */
    #[OA\Get(
        tags: [
            'Auth',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Refresh user token',
                content: new Model(
                    type: TokenInfoResponseDTO::class
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Permission denied',
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/auth/token/{accessToken}',
        name: 'auth_token_info',
        methods: ['GET']
    )]
    public function getAccessTokenInfo(string $accessToken): JsonResponse
    {
        try {
            $accessTokenEntity = $this->authManager->authenticateAccessTokenFromString($accessToken);
            $authAuthenticateResponseDTO = $this->dtoFactory->createAuthTokenInfoResponseDTO(
                $accessTokenEntity->getRefreshToken()->getUser()
            );

            return $this->json($authAuthenticateResponseDTO);
        } catch (UnauthorizedException) {
            throw new UnauthorizedHttpException('Permission denied');
        } catch (Throwable) {
            throw new ServerErrorHttpException();
        }
    }

    /**
     * @throws ServerErrorHttpException
     * @throws UnauthorizedHttpException
     */
    #[OA\Post(
        tags: [
            'Auth',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'New access token',
                content: new Model(
                    type: RefreshTokenResponseDTO::class
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Permission denied',
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/auth/token/{refreshToken}/refresh',
        name: 'auth_token_refresh',
        methods: ['POST']
    )]
    public function authenticateWithRefreshToken(string $refreshToken): JsonResponse
    {
        try {
            $refreshTokenEntity = $this->authManager->authenticateRefreshTokenFromString($refreshToken);
            $accessToken = $this->authManager->createAccessTokenAndFlush($refreshTokenEntity);
            $authAuthenticateResponseDTO = $this->dtoFactory->createAuthRefreshTokenResponseDTO($accessToken);

            return $this->json($authAuthenticateResponseDTO);
        } catch (UnauthorizedException) {
            throw new UnauthorizedHttpException('Bearer', 'Permission denied');
        } catch (Throwable) {
            throw new ServerErrorHttpException();
        }
    }
}
