<?php

declare(strict_types=1);

namespace App\Files;

use App\Auth\DBAL\Entity\User;
use App\Files\DBAL\Entity\File;
use App\Files\DBAL\Repository\FileRepository;
use App\Modules\Files\Controller\FilesController;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Factory\UuidFactory;
use Throwable;

class FileManager
{
    private const string STORAGE_FILES_FOLDER = 'uploaded-files';

    public function __construct(
        private readonly FilesystemAdapter $blobFileSystem,
        private readonly UuidFactory $uuidFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly Connection $dbalConnection,
        private readonly FileRepository $fileRepository,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @throws FileUploadFailedException
     */
    public function saveFileAndUpload(
        string $fileName,
        string $contentsBinary,
        ?User $createdUser = null,
    ): File {
        $file = new File(
            $this->uuidFactory->randomBased()->create(),
            self::STORAGE_FILES_FOLDER,
            $fileName,
            strlen($contentsBinary),
            $createdUser,
        );

        try {
            $this->dbalConnection->beginTransaction();

            $this->entityManager->persist($file);
            $this->entityManager->flush();

            $this->blobFileSystem->write($file->getFullPath(), $contentsBinary, new Config());

            $this->entityManager->commit();
        } catch (Throwable $e) {
            try {
                $this->dbalConnection->rollBack();
            } catch (Throwable $e) {
                throw new FileUploadFailedException('Failed to rollback! ' . $e->getMessage(), $e->getCode(), $e);
            }

            throw new FileUploadFailedException($e->getMessage(), $e->getCode(), $e);
        }

        return $file;
    }

    public function getFileEntity(string $id): ?File
    {
        try {
            return $this->fileRepository->find($id);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @throws FileDownloadFailedException
     */
    public function downloadFileContents(File $file): string
    {
        try {
            return $this->blobFileSystem->read($file->getFullPath());
        } catch (FilesystemException $e) {
            throw new FileDownloadFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getFileLink(?File $file): ?string
    {
        if ($file === null) {
            return null;
        }

        return ($this->requestStack->getCurrentRequest()?->getBaseUrl() ?? '') . FilesController::FILES_DOWNLOAD_URL . $file->getId();
    }
}
