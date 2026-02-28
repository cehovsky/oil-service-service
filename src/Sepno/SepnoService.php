<?php

declare(strict_types=1);

namespace App\Sepno;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\Exception\ServerErrorHttpException;
use App\Files\FileManager;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Repository\RouteRepository;
use App\Sepno\DBAL\Entity\SepnoRecord;
use App\Sepno\DBAL\Enum\SepnoRecordStatusEnum;
use App\Sepno\DBAL\Repository\SepnoRecordRepository;
use App\Sepno\Gateway\SepnoGatewayInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Uid\Factory\UuidFactory;
use Throwable;

class SepnoService
{
    public function __construct(
        private readonly SepnoRecordRepository $sepnoRecordRepository,
        private readonly RouteRepository $routeRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UuidFactory $uuidFactory,
        private readonly SepnoGatewayInterface $sepnoGateway,
        private readonly FileManager $fileManager,
    ) {
    }

    public function findRouteById(string $routeId): Route
    {
        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException('Route not found.');
        }

        return $route;
    }

    public function findRecordById(string $recordId): SepnoRecord
    {
        $record = $this->sepnoRecordRepository->find($recordId);

        if ($record === null) {
            throw new NotFoundHttpException('SEPNo record not found.');
        }

        return $record;
    }

    public function getCurrentForRoute(Route $route): ?SepnoRecord
    {
        return $this->sepnoRecordRepository->findCurrentForRoute($route);
    }

    public function createAndSend(
        Route $route,
        string $source,
        ?AuthUser $createdByUser = null,
        ?float $estimatedWasteKg = null,
    ): SepnoRecord {
        $current = $this->sepnoRecordRepository->findCurrentForRoute($route);

        if (
            $current !== null
            && $current->getStatus() !== SepnoRecordStatusEnum::CLOSED
            && $current->getStatus() !== SepnoRecordStatusEnum::REJECTED
        ) {
            throw new BadRequestHttpException('An active SEPNo record already exists for this route.');
        }

        $now = new DateTimeImmutable();
        $record = new SepnoRecord(
            $this->uuidFactory->timeBased()->create(),
            $route,
            SepnoRecordStatusEnum::DRAFT,
            $source,
            $now,
            $now,
            $createdByUser,
            $estimatedWasteKg,
        );

        $this->entityManager->persist($record);

        try {
            $gatewayResult = $this->sepnoGateway->submitStart($route, $estimatedWasteKg);

            $file = $this->fileManager->saveFileAndUpload(
                $gatewayResult->getAttachmentFileName(),
                $gatewayResult->getAttachmentBinary(),
                $createdByUser,
            );

            $record
                ->setStatus(SepnoRecordStatusEnum::ACCEPTED)
                ->setOfficialSepnoId($gatewayResult->getOfficialSepnoId())
                ->setRequestXml($gatewayResult->getRequestXml())
                ->setResponseXml($gatewayResult->getResponseXml())
                ->setResponseFile($file)
                ->setSubmittedAt($now)
                ->touch($now);

            $route->setCurrentSepnoRecord($record);

            $this->entityManager->flush();

            return $record;
        } catch (Throwable $e) {
            $record
                ->setStatus(SepnoRecordStatusEnum::REJECTED)
                ->setLastError($e->getMessage())
                ->touch(new DateTimeImmutable());
            $this->entityManager->flush();

            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    public function closeRecord(
        SepnoRecord $record,
        ?AuthUser $actingUser = null,
        ?float $actualWasteKg = null,
    ): SepnoRecord {
        if ($record->getStatus() === SepnoRecordStatusEnum::CLOSED) {
            throw new BadRequestHttpException('SEPNo record is already closed.');
        }

        $now = new DateTimeImmutable();

        try {
            $gatewayResult = $this->sepnoGateway->submitClose($record, $actualWasteKg);

            $file = $this->fileManager->saveFileAndUpload(
                $gatewayResult->getAttachmentFileName(),
                $gatewayResult->getAttachmentBinary(),
                $actingUser,
            );

            $record
                ->setStatus(SepnoRecordStatusEnum::CLOSED)
                ->setOfficialSepnoId($gatewayResult->getOfficialSepnoId())
                ->setRequestXml($gatewayResult->getRequestXml())
                ->setResponseXml($gatewayResult->getResponseXml())
                ->setResponseFile($file)
                ->setActualWasteKg($actualWasteKg ?? $record->getActualWasteKg())
                ->setClosedAt($now)
                ->touch($now);

            $this->entityManager->flush();

            return $record;
        } catch (Throwable $e) {
            $record
                ->setLastError($e->getMessage())
                ->touch(new DateTimeImmutable());
            $this->entityManager->flush();

            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }
}
