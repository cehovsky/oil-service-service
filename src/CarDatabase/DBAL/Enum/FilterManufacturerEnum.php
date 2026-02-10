<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Enum;

enum FilterManufacturerEnum: string
{
    public const array VALUES = [
        'mann',
        'bosch',
        'mahle',
        'knecht',
        'hengst',
        'fram',
        'purflux',
        'ufi',
        'champion',
        'filtron',
        'wix',
        'sct',
        'denckermann',
        'mapco',
        'valeo',
        'gm',
        'vag',
        'ford',
        'toyota',
        'renault',
        'psa',
        'hyundai_kia',
    ];

    case MANN = 'mann';
    case BOSCH = 'bosch';
    case MAHLE = 'mahle';
    case KNECHT = 'knecht';
    case HENGST = 'hengst';
    case FRAM = 'fram';
    case PURFLUX = 'purflux';
    case UFI = 'ufi';
    case CHAMPION = 'champion';
    case FILTRON = 'filtron';
    case WIX = 'wix';
    case SCT = 'sct';
    case DENCKERMANN = 'denckermann';
    case MAPCO = 'mapco';
    case VALEO = 'valeo';
    case GM = 'gm';
    case VAG = 'vag';
    case FORD = 'ford';
    case TOYOTA = 'toyota';
    case RENAULT = 'renault';
    case PSA = 'psa';
    case HYUNDAI_KIA = 'hyundai_kia';

    public static function values(): array
    {
        return self::VALUES;
    }
}
