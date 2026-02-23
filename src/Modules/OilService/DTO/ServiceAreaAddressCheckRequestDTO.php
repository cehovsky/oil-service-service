<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceAreaAddressCheckRequestDTO
{
    #[OA\Property(example: 'Václavské náměstí 1, Praha 1, 110 00')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    private string $address;

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}
