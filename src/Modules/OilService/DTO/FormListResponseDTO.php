<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class FormListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var FormDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: FormDTO::class)))]
    private array $forms;

    /**
     * @param FormDTO[] $forms
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $forms,
        int $pageCount
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->forms = $forms;
        $this->pageCount = $pageCount;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return FormDTO[]
     */
    public function getForms(): array
    {
        return $this->forms;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
