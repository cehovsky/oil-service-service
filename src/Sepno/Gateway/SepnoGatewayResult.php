<?php

declare(strict_types=1);

namespace App\Sepno\Gateway;

class SepnoGatewayResult
{
    public function __construct(
        private readonly string $officialSepnoId,
        private readonly string $requestXml,
        private readonly string $responseXml,
        private readonly string $attachmentFileName,
        private readonly string $attachmentBinary,
    ) {
    }

    public function getOfficialSepnoId(): string
    {
        return $this->officialSepnoId;
    }

    public function getRequestXml(): string
    {
        return $this->requestXml;
    }

    public function getResponseXml(): string
    {
        return $this->responseXml;
    }

    public function getAttachmentFileName(): string
    {
        return $this->attachmentFileName;
    }

    public function getAttachmentBinary(): string
    {
        return $this->attachmentBinary;
    }
}
