<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Enum;

enum StorageContainerTypeEnum: string
{
    public const array VALUES = [
        'collection_tray',
        'oil_canister',
        'barrel',
        'plastic_filter_box',
        'metal_filter_box',
        'spill_pallet',
        'hazard_storage_container',
    ];

    case COLLECTION_TRAY = 'collection_tray';

    case OIL_CANISTER = 'oil_canister';

    case BARREL = 'barrel';

    case PLASTIC_FILTER_BOX = 'plastic_filter_box';

    case METAL_FILTER_BOX = 'metal_filter_box';

    case SPILL_PALLET = 'spill_pallet';

    case HAZARD_STORAGE_CONTAINER = 'hazard_storage_container';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
