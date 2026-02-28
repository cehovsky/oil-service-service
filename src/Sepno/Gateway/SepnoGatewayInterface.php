<?php

declare(strict_types=1);

namespace App\Sepno\Gateway;

use App\OilService\DBAL\Entity\Route;
use App\Sepno\DBAL\Entity\SepnoRecord;

interface SepnoGatewayInterface
{
    public function submitStart(Route $route, ?float $estimatedWasteKg = null): SepnoGatewayResult;

    public function submitClose(SepnoRecord $record, ?float $actualWasteKg = null): SepnoGatewayResult;
}
