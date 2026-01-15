<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Domain\Exception\ServerErrorHttpException;
use App\Modules\OilService\DTO\PriceListItemPublicListResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class PriceListItemPublicController extends AbstractController
{
    public function __construct(
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly DTOFactory $dtoFactory,
    ) {
    }

    #[OA\Get(
        tags: [
            'PriceListItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active public price list items ordered by label',
                content: new Model(
                    type: PriceListItemPublicListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/price-list-items/public',
        name: 'oil_service_price_list_items_public_list',
        methods: ['GET']
    )]
    public function list(): JsonResponse
    {
        try {
            $priceListItems = $this->priceListItemRepository->findActivePublicItemsOrderedByLabel();

            $responseDTO = $this->dtoFactory->createPriceListItemPublicListResponseDTO($priceListItems);

            return $this->json($responseDTO);
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }
}
