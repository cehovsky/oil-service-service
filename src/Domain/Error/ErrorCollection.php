<?php

declare(strict_types=1);

namespace App\Domain\Error;

use App\Domain\ArrayCollection;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @method ErrorItem      remove(string|int $key)
 * @method ErrorItem|null get(string|int $key)
 * @method ErrorItem|null first()
 * @method ErrorItem|null last()
 * @method ErrorItem[]    toArray()
 */
#[OA\Schema(
    properties: [
        new OA\Property(
            property: 'result',
            example: 'Bad request',
        ),

        new OA\Property(
            property: 'errors',
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: ErrorItem::class,
                ),
            ),
        ),
        new OA\Property(
            property: 'itemClass',
            writeOnly: true,
        ),
        new OA\Property(
            property: 'empty',
            writeOnly: true,
        ),
        new OA\Property(
            property: 'iterator',
            type: 'string',
            writeOnly: true,
        ),
    ]
)]
class ErrorCollection extends ArrayCollection implements ErrorCollectionInterface
{
    public function getItemClass(): string
    {
        return ErrorItem::class;
    }

    /**
     * @return array<string, null|string|array<int, mixed>>
     */
    public function toResponseArray(): array
    {
        $output = [
            'result' => 'error',
            'errors' => [],
        ];

        foreach ($this->toArray() as $errorItem) {
            $output['errors'][] = [
                'path' => $errorItem->getPath(),
                'code' => $errorItem->getCode(),
                'message' => $errorItem->getMessage(),
            ];
        }

        return $output;
    }
}
