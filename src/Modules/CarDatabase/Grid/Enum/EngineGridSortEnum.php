<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Grid\Enum;

use App\CarDatabase\DBAL\Repository\EngineRepository;
use App\Domain\ApiGrid\OrderEnumInterface;

enum EngineGridSortEnum: string implements OrderEnumInterface
{
    case MANUFACTURER = 'manufacturer';
    case MODEL = 'model';
    case ENGINE_CODE = 'engineCode';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return EngineRepository::ALIAS . '.' . $this->value;
    }
}
