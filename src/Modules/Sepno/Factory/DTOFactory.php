<?php

declare(strict_types=1);

namespace App\Modules\Sepno\Factory;

use App\Domain\DTOValueResolver;
use App\Files\DBAL\Entity\File;
use App\Modules\Files\DTO\FileDTO;
use App\Modules\Sepno\DTO\SepnoRecordCurrentResponseDTO;
use App\Modules\Sepno\DTO\SepnoRecordDTO;
use App\Modules\Sepno\DTO\SepnoRecordInfoResponseDTO;
use App\Modules\Sepno\DTO\SepnoRecordListResponseDTO;
use App\Modules\Warehouse\DTO\RouteSummaryDTO;
use App\Sepno\DBAL\Entity\SepnoRecord;
use DateTimeInterface;

class DTOFactory
{
    public function createSepnoRecordDTO(SepnoRecord $record, bool $includeXml = false): SepnoRecordDTO
    {
        return new SepnoRecordDTO(
            $record->getId()->toRfc4122(),
            new RouteSummaryDTO(
                $record->getRoute()->getId()->toRfc4122(),
                $record->getRoute()->getDate()->format('Y-m-d'),
                $record->getRoute()->getIsActive(),
            ),
            $record->getStatus()->value,
            $record->getOfficialSepnoId(),
            $record->getSource(),
            $record->getEstimatedWasteKg(),
            $record->getActualWasteKg(),
            $this->createFileDTO($record->getResponseFile()),
            $includeXml ? $record->getRequestXml() : null,
            $includeXml ? $record->getResponseXml() : null,
            $record->getLastError(),
            $record->getCreatedAt()->format(DateTimeInterface::ATOM),
            $record->getSubmittedAt()?->format(DateTimeInterface::ATOM),
            $record->getClosedAt()?->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param SepnoRecord[] $records
     */
    public function createSepnoRecordListResponseDTO(array $records, int $pageCount): SepnoRecordListResponseDTO
    {
        $items = [];

        foreach ($records as $record) {
            $items[] = $this->createSepnoRecordDTO($record);
        }

        return new SepnoRecordListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $items,
            $pageCount,
        );
    }

    public function createSepnoRecordInfoResponseDTO(SepnoRecord $record): SepnoRecordInfoResponseDTO
    {
        return new SepnoRecordInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createSepnoRecordDTO($record, true),
        );
    }

    public function createSepnoRecordCurrentResponseDTO(?SepnoRecord $record): SepnoRecordCurrentResponseDTO
    {
        return new SepnoRecordCurrentResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $record ? $this->createSepnoRecordDTO($record) : null,
        );
    }

    private function createFileDTO(?File $file): ?FileDTO
    {
        if ($file === null) {
            return null;
        }

        return new FileDTO(
            $file->getId()->toRfc4122(),
            $file->getFileName(),
            $file->getSize(),
            $file->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }
}
