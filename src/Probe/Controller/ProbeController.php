<?php

declare(strict_types=1);

namespace App\Probe\Controller;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class ProbeController extends AbstractController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
    ) {
    }

    #[OA\Get(
        tags: [
            'Probe',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'It\'s alive',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            type: 'string',
                            example: 'OK'
                        ),
                    ]
                )
            ),
        ],
    )]
    #[Route(
        '/health',
        name: 'probe_health',
        methods: ['GET']
    )]
    public function health(): JsonResponse
    {
        return $this->json(['result' => 'ok']);
    }

    /**
     * @throws Exception
     */
    #[OA\Get(
        tags: [
            'Probe',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'It\'s ready',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            type: 'string',
                            example: 'ok'
                        ),
                    ]
                )
            ),
        ],
    )]
    #[Route(
        '/ready',
        name: 'probe_ready',
        methods: ['GET']
    )]
    public function ready(): JsonResponse
    {
        $this->entityManager->getConnection()->executeQuery('SELECT 1');

        return $this->json(['result' => 'ok']);
    }
}
