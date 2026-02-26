<?php

declare(strict_types=1);

namespace App\OilService\Report;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

class OrderReportLinkService
{
    private const string DEFAULT_WEB_BASE_URL = 'http://localhost:8088';
    private const string ENV_REPORT_WEB_BASE_URL = 'ORDER_REPORT_WEB_BASE_URL';

    public function buildPublicReportUrl(string $secretKey): string
    {
        return sprintf('%s/report/%s', $this->resolveWebBaseUrl(), $secretKey);
    }

    public function buildQrCodeDataUri(string $url): string
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(260)
            ->margin(8)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return 'data:image/svg+xml;base64,' . base64_encode($result->getString());
    }

    private function resolveWebBaseUrl(): string
    {
        $value = $_ENV[self::ENV_REPORT_WEB_BASE_URL]
            ?? $_SERVER[self::ENV_REPORT_WEB_BASE_URL]
            ?? self::DEFAULT_WEB_BASE_URL;

        $normalized = trim((string) $value);

        if ($normalized === '') {
            return self::DEFAULT_WEB_BASE_URL;
        }

        return rtrim($normalized, '/');
    }
}
