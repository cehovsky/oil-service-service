<?php

declare(strict_types=1);

namespace App\Files\DBAL\Entity;

use App\Auth\DBAL\Entity\User;
use App\Files\DBAL\Repository\FileRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: FileRepository::class)]
class File
{
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    /**
     * @param string $folder Folder in Azure (or any other) storage where the file is saved. No trailing slash necessary
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid')]
        private Uuid $id,
        #[ORM\Column]
        private string $folder,
        #[ORM\Column]
        private string $fileName,
        #[ORM\Column]
        private int $size,
        #[ORM\JoinColumn]
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'files')]
        private ?User $createdUser,
    ) {
        $this->folder = trim($this->folder, " \n\r\t\v\0/");
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getFullPath(): string
    {
        return $this->folder . '/' . $this->fileName;
    }

    public function getCreatedUser(): ?User
    {
        return $this->createdUser;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
