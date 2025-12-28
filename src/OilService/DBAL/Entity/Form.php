<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Repository\FormRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'oil_service_form')]
#[ORM\Entity(repositoryClass: FormRepository::class)]
class Form
{
}
