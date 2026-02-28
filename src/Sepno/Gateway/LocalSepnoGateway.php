<?php

declare(strict_types=1);

namespace App\Sepno\Gateway;

use App\OilService\DBAL\Entity\Route;
use App\Sepno\DBAL\Entity\SepnoRecord;
use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class LocalSepnoGateway implements SepnoGatewayInterface
{
    public function submitStart(Route $route, ?float $estimatedWasteKg = null): SepnoGatewayResult
    {
        $sentAt = new DateTimeImmutable();
        $officialId = 'SEP-' . strtoupper(substr($route->getId()->toRfc4122(), 0, 8)) . '-' . $sentAt->format('YmdHis');

        $requestXml = $this->buildStartXml($route, $officialId, $sentAt, $estimatedWasteKg);
        $responseXml = $this->buildResponseXml('accepted', $officialId, $sentAt);

        return new SepnoGatewayResult(
            $officialId,
            $requestXml,
            $responseXml,
            sprintf('sepno-%s-start.xml', $route->getId()->toRfc4122()),
            $requestXml,
        );
    }

    public function submitClose(SepnoRecord $record, ?float $actualWasteKg = null): SepnoGatewayResult
    {
        $sentAt = new DateTimeImmutable();
        $officialId = $record->getOfficialSepnoId() ?? 'SEP-' . strtoupper(substr($record->getId()->toRfc4122(), 0, 8));

        $requestXml = $this->buildCloseXml($record, $officialId, $sentAt, $actualWasteKg);
        $responseXml = $this->buildResponseXml('closed', $officialId, $sentAt);

        return new SepnoGatewayResult(
            $officialId,
            $requestXml,
            $responseXml,
            sprintf('sepno-%s-close.xml', $record->getId()->toRfc4122()),
            $requestXml,
        );
    }

    private function buildStartXml(Route $route, string $officialId, DateTimeImmutable $sentAt, ?float $estimatedWasteKg): string
    {
        $messageId = Uuid::v4()->toRfc4122();
        $estimated = $estimatedWasteKg !== null ? number_format($estimatedWasteKg, 2, '.', '') : '0.00';

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OhlašeniPrepravy>
  <Hlavicka>
    <IDZpravy>{$messageId}</IDZpravy>
    <DatumCasOdeslani>{$sentAt->format(DATE_ATOM)}</DatumCasOdeslani>
    <TypPodani>ZAHÁJENÍ</TypPodani>
  </Hlavicka>
  <Preprava>
    <InterniID>{$route->getId()->toRfc4122()}</InterniID>
    <SEPNoID>{$officialId}</SEPNoID>
    <DatumCasZahajeni>{$sentAt->format(DATE_ATOM)}</DatumCasZahajeni>
    <Odpad>
      <KodOdpadu>130205*</KodOdpadu>
      <MnozstviKg>{$estimated}</MnozstviKg>
    </Odpad>
  </Preprava>
</OhlašeniPrepravy>
XML;
    }

    private function buildCloseXml(
        SepnoRecord $record,
        string $officialId,
        DateTimeImmutable $sentAt,
        ?float $actualWasteKg,
    ): string {
        $messageId = Uuid::v4()->toRfc4122();
        $actual = $actualWasteKg !== null ? number_format($actualWasteKg, 2, '.', '') : number_format($record->getEstimatedWasteKg() ?? 0, 2, '.', '');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<OhlašeniPrepravyUpdate>
  <Hlavicka>
    <IDZpravy>{$messageId}</IDZpravy>
    <DatumCasOdeslani>{$sentAt->format(DATE_ATOM)}</DatumCasOdeslani>
    <TypPodani>UKONČENÍ</TypPodani>
  </Hlavicka>
  <Preprava>
    <SEPNoID>{$officialId}</SEPNoID>
    <DatumCasUkonceni>{$sentAt->format(DATE_ATOM)}</DatumCasUkonceni>
    <Odpad>
      <MnozstviKg>{$actual}</MnozstviKg>
    </Odpad>
  </Preprava>
</OhlašeniPrepravyUpdate>
XML;
    }

    private function buildResponseXml(string $status, string $officialId, DateTimeImmutable $sentAt): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SepnoResponse>
  <status>{$status}</status>
  <officialSepnoId>{$officialId}</officialSepnoId>
  <processedAt>{$sentAt->format(DATE_ATOM)}</processedAt>
</SepnoResponse>
XML;
    }
}
