<?php

declare(strict_types=1);

namespace App\Blob;

use AzureOss\FlysystemAzureBlobStorage\AzureBlobStorageAdapter;
use AzureOss\Storage\Blob\BlobServiceClient;
use League\Flysystem\FilesystemAdapter;

class AzureBlobAdapterFactory
{
    public static function create(
        string $azureConnectionString,
        string $container,
        string $prefix,
    ): FilesystemAdapter {
        $containerClient = BlobServiceClient::fromConnectionString($azureConnectionString)
            ->getContainerClient($container);

        return new AzureBlobStorageAdapter(
            $containerClient,
            $prefix,
        );
    }
}
