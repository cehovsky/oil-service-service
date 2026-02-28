<?php

declare(strict_types=1);

namespace App\Sepno\DBAL\Entity;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Files\DBAL\Entity\File;
use App\OilService\DBAL\Entity\Route;
use App\Sepno\DBAL\Enum\SepnoRecordStatusEnum;
use App\Sepno\DBAL\Repository\SepnoRecordRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'oil_service_sepno_record')]
#[ORM\Index(name: 'idx_sepno_route', columns: ['route_id'])]
#[ORM\Index(name: 'idx_sepno_status', columns: ['status'])]
#[ORM\Index(name: 'idx_sepno_created', columns: ['created_at'])]
#[ORM\Entity(repositoryClass: SepnoRecordRepository::class)]
class SepnoRecord
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Route::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Route $route;

    #[ORM\Column(type: Types::STRING, enumType: SepnoRecordStatusEnum::class)]
    private SepnoRecordStatusEnum $status;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $officialSepnoId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $requestXml = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $responseXml = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?File $responseFile = null;

    #[ORM\ManyToOne(targetEntity: AuthUser::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?AuthUser $createdByUser = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $estimatedWasteKg = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $actualWasteKg = null;

    #[ORM\Column(length: 32)]
    private string $source;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $lastError = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $submittedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        Route $route,
        SepnoRecordStatusEnum $status,
        string $source,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        ?AuthUser $createdByUser = null,
        ?float $estimatedWasteKg = null,
    ) {
        $this->id = $id;
        $this->route = $route;
        $this->status = $status;
        $this->source = $source;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->createdByUser = $createdByUser;
        $this->estimatedWasteKg = $estimatedWasteKg;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getStatus(): SepnoRecordStatusEnum
    {
        return $this->status;
    }

    public function setStatus(SepnoRecordStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOfficialSepnoId(): ?string
    {
        return $this->officialSepnoId;
    }

    public function setOfficialSepnoId(?string $officialSepnoId): self
    {
        $this->officialSepnoId = $officialSepnoId;

        return $this;
    }

    public function getRequestXml(): ?string
    {
        return $this->requestXml;
    }

    public function setRequestXml(?string $requestXml): self
    {
        $this->requestXml = $requestXml;

        return $this;
    }

    public function getResponseXml(): ?string
    {
        return $this->responseXml;
    }

    public function setResponseXml(?string $responseXml): self
    {
        $this->responseXml = $responseXml;

        return $this;
    }

    public function getResponseFile(): ?File
    {
        return $this->responseFile;
    }

    public function setResponseFile(?File $responseFile): self
    {
        $this->responseFile = $responseFile;

        return $this;
    }

    public function getCreatedByUser(): ?AuthUser
    {
        return $this->createdByUser;
    }

    public function getEstimatedWasteKg(): ?float
    {
        return $this->estimatedWasteKg;
    }

    public function setEstimatedWasteKg(?float $estimatedWasteKg): self
    {
        $this->estimatedWasteKg = $estimatedWasteKg;

        return $this;
    }

    public function getActualWasteKg(): ?float
    {
        return $this->actualWasteKg;
    }

    public function setActualWasteKg(?float $actualWasteKg): self
    {
        $this->actualWasteKg = $actualWasteKg;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function setLastError(?string $lastError): self
    {
        $this->lastError = $lastError;

        return $this;
    }

    public function getSubmittedAt(): ?DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(?DateTimeImmutable $submittedAt): self
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function setClosedAt(?DateTimeImmutable $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
