<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Factory;

use App\CarDatabase\DBAL\Entity\Engine;
use App\CarDatabase\DBAL\Entity\EngineFilter;
use App\CarDatabase\DBAL\Entity\Filter;
use App\Domain\DTOValueResolver;
use App\Modules\CarDatabase\DTO\EngineCreateResponseDTO;
use App\Modules\CarDatabase\DTO\EngineDTO;
use App\Modules\CarDatabase\DTO\EngineDeleteResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterCreateResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterDTO;
use App\Modules\CarDatabase\DTO\EngineFilterDeleteResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterInfoResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterListResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterUpdateResponseDTO;
use App\Modules\CarDatabase\DTO\EngineInfoResponseDTO;
use App\Modules\CarDatabase\DTO\EngineListResponseDTO;
use App\Modules\CarDatabase\DTO\EngineSummaryDTO;
use App\Modules\CarDatabase\DTO\EngineUpdateResponseDTO;
use App\Modules\CarDatabase\DTO\FilterCreateResponseDTO;
use App\Modules\CarDatabase\DTO\FilterDTO;
use App\Modules\CarDatabase\DTO\FilterDeleteResponseDTO;
use App\Modules\CarDatabase\DTO\FilterInfoResponseDTO;
use App\Modules\CarDatabase\DTO\FilterListResponseDTO;
use App\Modules\CarDatabase\DTO\FilterSummaryDTO;
use App\Modules\CarDatabase\DTO\FilterUpdateResponseDTO;
use DateTimeInterface;

class DTOFactory
{
    public function createEngineDTO(Engine $engine): EngineDTO
    {
        return new EngineDTO(
            $engine->getId()->__toString(),
            $engine->getManufacturer(),
            $engine->getModel(),
            $engine->getGeneration(),
            $engine->getEngineCode(),
            $engine->getEngineFamily(),
            $engine->getDisplacementCc(),
            $engine->getPowerKw(),
            $engine->getFuel(),
            $engine->getEmissionStandard(),
            $engine->getProductionFromYear(),
            $engine->getProductionToYear(),
            $engine->getOilCapacityL(),
            $engine->getOilCapacityNote(),
            $engine->getOilViscosity(),
            $engine->getOilSpecification(),
            $engine->getOilIntervalKm(),
            $engine->getOilIntervalMonths(),
            $engine->getOilDrainPlugTorqueNm(),
            $engine->getOilFilterTorqueNm(),
            $engine->getSparkPlugTorqueNm(),
            $engine->getSource(),
            $engine->getConfidence(),
            $engine->getNotes(),
            $engine->getCreatedAt()->format(DateTimeInterface::ATOM),
            $engine->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function createEngineSummaryDTO(Engine $engine): EngineSummaryDTO
    {
        return new EngineSummaryDTO(
            $engine->getId()->__toString(),
            $engine->getManufacturer(),
            $engine->getModel(),
            $engine->getEngineCode(),
        );
    }

    /**
     * @param Engine[] $engines
     *
     * @return EngineDTO[]
     */
    public function createEngineDTOs(array $engines): array
    {
        $items = [];

        foreach ($engines as $engine) {
            $items[] = $this->createEngineDTO($engine);
        }

        return $items;
    }

    /**
     * @param Engine[] $engines
     */
    public function createEngineListResponseDTO(array $engines, int $pageCount): EngineListResponseDTO
    {
        return new EngineListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineDTOs($engines),
            $pageCount,
        );
    }

    public function createEngineInfoResponseDTO(Engine $engine): EngineInfoResponseDTO
    {
        return new EngineInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineDTO($engine),
        );
    }

    public function createEngineCreateResponseDTO(Engine $engine): EngineCreateResponseDTO
    {
        return new EngineCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineDTO($engine),
        );
    }

    public function createEngineUpdateResponseDTO(Engine $engine): EngineUpdateResponseDTO
    {
        return new EngineUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineDTO($engine),
        );
    }

    public function createEngineDeleteResponseDTO(): EngineDeleteResponseDTO
    {
        return new EngineDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createFilterDTO(Filter $filter): FilterDTO
    {
        return new FilterDTO(
            $filter->getId()->__toString(),
            $filter->getFilterType()->value,
            $filter->getManufacturer(),
            $filter->getCode(),
            $filter->getOemCode(),
            $filter->getThread(),
            $filter->getHeightMm(),
            $filter->getDiameterMm(),
            $filter->getNotes(),
            $filter->getCreatedAt()->format(DateTimeInterface::ATOM),
            $filter->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function createFilterSummaryDTO(Filter $filter): FilterSummaryDTO
    {
        return new FilterSummaryDTO(
            $filter->getId()->__toString(),
            $filter->getFilterType()->value,
            $filter->getManufacturer(),
            $filter->getCode(),
            $filter->getOemCode(),
        );
    }

    /**
     * @param Filter[] $filters
     *
     * @return FilterDTO[]
     */
    public function createFilterDTOs(array $filters): array
    {
        $items = [];

        foreach ($filters as $filter) {
            $items[] = $this->createFilterDTO($filter);
        }

        return $items;
    }

    /**
     * @param Filter[] $filters
     */
    public function createFilterListResponseDTO(array $filters, int $pageCount): FilterListResponseDTO
    {
        return new FilterListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createFilterDTOs($filters),
            $pageCount,
        );
    }

    public function createFilterInfoResponseDTO(Filter $filter): FilterInfoResponseDTO
    {
        return new FilterInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createFilterDTO($filter),
        );
    }

    public function createFilterCreateResponseDTO(Filter $filter): FilterCreateResponseDTO
    {
        return new FilterCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createFilterDTO($filter),
        );
    }

    public function createFilterUpdateResponseDTO(Filter $filter): FilterUpdateResponseDTO
    {
        return new FilterUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createFilterDTO($filter),
        );
    }

    public function createFilterDeleteResponseDTO(): FilterDeleteResponseDTO
    {
        return new FilterDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createEngineFilterDTO(EngineFilter $engineFilter): EngineFilterDTO
    {
        return new EngineFilterDTO(
            $engineFilter->getId()->__toString(),
            $this->createEngineSummaryDTO($engineFilter->getEngine()),
            $this->createFilterSummaryDTO($engineFilter->getFilter()),
            $engineFilter->isPrimary(),
            $engineFilter->getSource(),
            $engineFilter->getCreatedAt()->format(DateTimeInterface::ATOM),
            $engineFilter->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param EngineFilter[] $engineFilters
     *
     * @return EngineFilterDTO[]
     */
    public function createEngineFilterDTOs(array $engineFilters): array
    {
        $items = [];

        foreach ($engineFilters as $engineFilter) {
            $items[] = $this->createEngineFilterDTO($engineFilter);
        }

        return $items;
    }

    /**
     * @param EngineFilter[] $engineFilters
     */
    public function createEngineFilterListResponseDTO(array $engineFilters, int $pageCount): EngineFilterListResponseDTO
    {
        return new EngineFilterListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineFilterDTOs($engineFilters),
            $pageCount,
        );
    }

    public function createEngineFilterInfoResponseDTO(EngineFilter $engineFilter): EngineFilterInfoResponseDTO
    {
        return new EngineFilterInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineFilterDTO($engineFilter),
        );
    }

    public function createEngineFilterCreateResponseDTO(EngineFilter $engineFilter): EngineFilterCreateResponseDTO
    {
        return new EngineFilterCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineFilterDTO($engineFilter),
        );
    }

    public function createEngineFilterUpdateResponseDTO(EngineFilter $engineFilter): EngineFilterUpdateResponseDTO
    {
        return new EngineFilterUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createEngineFilterDTO($engineFilter),
        );
    }

    public function createEngineFilterDeleteResponseDTO(): EngineFilterDeleteResponseDTO
    {
        return new EngineFilterDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }
}
