<?php

declare(strict_types=1);

namespace App\Domain\Doctrine;

use App\Domain\Exception\InvalidUuidException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

class UuidStringType extends Type
{
    public function getName(): string
    {
        return 'uuid';
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 36;
        $column['fixed'] = true;

        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @throws InvalidUuidException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_string($value) && !$value instanceof AbstractUid) {
            throw new InvalidUuidException($value);
        }

        return (string) $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?Uuid
    {
        if (is_string($value)) {
            return Uuid::fromString($value);
        }

        return null;
    }
}
