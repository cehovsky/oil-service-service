<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ChatUserRequestUpdateRequestDTO
{
    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isResolved;

    #[OA\Property(example: 'Call customer after 3pm', nullable: true)]
    #[Assert\Length(max: 4000)]
    private ?string $note = null;

    public function getIsResolved(): bool
    {
        return $this->isResolved;
    }

    public function setIsResolved(bool $isResolved): self
    {
        $this->isResolved = $isResolved;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
