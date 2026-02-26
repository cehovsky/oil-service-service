<?php

declare(strict_types=1);

namespace App\Modules\OilService\Factory;

use App\Domain\DTOValueResolver;
use App\Files\DBAL\Entity\File;
use App\Modules\Files\Controller\FilesController;
use App\Modules\OilService\DTO\PublicOrderReportDTO;
use App\Modules\OilService\DTO\PublicOrderReportPhotoDTO;
use App\Modules\OilService\DTO\PublicOrderReportResponseDTO;
use App\Modules\OilService\DTO\PublicOrderReportServiceItemDTO;
use App\OilService\DBAL\Entity\Order;

class PublicOrderReportDTOFactory
{
    public function createResponseDTO(Order $order): PublicOrderReportResponseDTO
    {
        return new PublicOrderReportResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            new PublicOrderReportDTO(
                $order->getFormattedIdent(),
                $order->getRealizationDate()->format('Y-m-d'),
                $order->getRealizationTimeSlot()->value,
                $order->getCarModel(),
                $order->getLicensePlate(),
                $order->getAddress(),
                $this->maskVin($order->getVin()),
                $order->getMileage(),
                $this->createServiceItems($order),
                $this->createPhotoItems($order),
            ),
        );
    }

    /**
     * @return PublicOrderReportServiceItemDTO[]
     */
    private function createServiceItems(Order $order): array
    {
        $items = [];

        foreach ($order->getPriceListItems() as $priceListItem) {
            $items[] = new PublicOrderReportServiceItemDTO(
                'service',
                $priceListItem->getLabel(),
                1,
            );
        }

        foreach ($order->getOrderInventoryItems() as $inventoryItem) {
            $items[] = new PublicOrderReportServiceItemDTO(
                'material',
                $inventoryItem->getInventoryItem()->getLabel(),
                $inventoryItem->getQuantity(),
            );
        }

        return $items;
    }

    /**
     * @return PublicOrderReportPhotoDTO[]
     */
    private function createPhotoItems(Order $order): array
    {
        $photos = [];

        $this->appendPhoto($photos, $order->getOilChangeVehiclePhoto(), 'Vozidlo před servisem');
        $this->appendPhoto($photos, $order->getVinPhoto(), 'VIN štítek');
        $this->appendPhoto($photos, $order->getOldOilFilterPhoto(), 'Použitý olejový filtr');
        $this->appendPhoto($photos, $order->getOldOilPhoto(), 'Původní olej');
        $this->appendPhoto($photos, $order->getOdometerPhoto(), 'Stav tachometru');

        foreach ($order->getOtherPhotos() as $index => $otherPhoto) {
            $this->appendPhoto(
                $photos,
                $otherPhoto,
                sprintf('Další fotografie #%d', $index + 1),
            );
        }

        return $photos;
    }

    /**
     * @param PublicOrderReportPhotoDTO[] $photos
     */
    private function appendPhoto(array &$photos, ?File $file, string $label): void
    {
        if ($file === null) {
            return;
        }

        $photos[] = new PublicOrderReportPhotoDTO(
            $label,
            $file->getFileName(),
            FilesController::FILES_DOWNLOAD_URL . $file->getId()->__toString(),
        );
    }

    private function maskVin(?string $vin): ?string
    {
        if ($vin === null || $vin === '') {
            return null;
        }

        $len = strlen($vin);
        if ($len <= 7) {
            return str_repeat('*', $len);
        }

        return substr($vin, 0, 3) . str_repeat('*', $len - 7) . substr($vin, -4);
    }
}
