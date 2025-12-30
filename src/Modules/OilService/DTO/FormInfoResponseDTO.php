<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class FormInfoResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: FormDTO::class))]
    private FormDTO $form;

    public function __construct(
        string $result,
        int $timestamp,
        FormDTO $form
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->form = $form;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getForm(): FormDTO
    {
        return $this->form;
    }
}
