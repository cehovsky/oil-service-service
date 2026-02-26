<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PublicOrderReportDTO
{
    #[OA\Property(example: 'O2600123')]
    private string $orderIdent;

    #[OA\Property(example: '2026-02-25')]
    private string $realizationDate;

    #[OA\Property(example: 'afternoon')]
    private string $realizationTimeSlot;

    #[OA\Property(example: 'Škoda Octavia 2.0 TDI')]
    private string $carModel;

    #[OA\Property(example: '1AB 2345')]
    private string $licensePlate;

    #[OA\Property(example: 'Václavské náměstí 1, Praha 1')]
    private string $serviceAddress;

    #[OA\Property(example: 'TMB******1234', nullable: true)]
    private ?string $vinMasked;

    #[OA\Property(example: 123456, nullable: true)]
    private ?int $mileage;

    /**
     * @var PublicOrderReportServiceItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: PublicOrderReportServiceItemDTO::class)))]
    private array $services;

    /**
     * @var PublicOrderReportPhotoDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: PublicOrderReportPhotoDTO::class)))]
    private array $photos;

    public function __construct(
        string $orderIdent,
        string $realizationDate,
        string $realizationTimeSlot,
        string $carModel,
        string $licensePlate,
        string $serviceAddress,
        ?string $vinMasked,
        ?int $mileage,
        array $services,
        array $photos,
    ) {
        $this->orderIdent = $orderIdent;
        $this->realizationDate = $realizationDate;
        $this->realizationTimeSlot = $realizationTimeSlot;
        $this->carModel = $carModel;
        $this->licensePlate = $licensePlate;
        $this->serviceAddress = $serviceAddress;
        $this->vinMasked = $vinMasked;
        $this->mileage = $mileage;
        $this->services = $services;
        $this->photos = $photos;
    }

    public function getOrderIdent(): string
    {
        return $this->orderIdent;
    }

    public function getRealizationDate(): string
    {
        return $this->realizationDate;
    }

    public function getRealizationTimeSlot(): string
    {
        return $this->realizationTimeSlot;
    }

    public function getCarModel(): string
    {
        return $this->carModel;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function getServiceAddress(): string
    {
        return $this->serviceAddress;
    }

    public function getVinMasked(): ?string
    {
        return $this->vinMasked;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    /**
     * @return PublicOrderReportServiceItemDTO[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return PublicOrderReportPhotoDTO[]
     */
    public function getPhotos(): array
    {
        return $this->photos;
    }
}
