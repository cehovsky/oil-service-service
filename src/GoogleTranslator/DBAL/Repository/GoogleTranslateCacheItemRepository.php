<?php

declare(strict_types=1);

namespace App\GoogleTranslator\DBAL\Repository;

use App\GoogleTranslator\DBAL\Entity\GoogleTranslateCacheItem;
use App\GoogleTranslator\DBAL\Enum\LanguageCodeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GoogleTranslateCacheItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleTranslateCacheItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleTranslateCacheItem[] findAll()
 * @method GoogleTranslateCacheItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<GoogleTranslateCacheItem>
 */
final class GoogleTranslateCacheItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleTranslateCacheItem::class);
    }

    public function findTranslationText(
        LanguageCodeEnum $sourceLanguageCode,
        LanguageCodeEnum $targetLanguageCode,
        string $sourceText,
    ): ?GoogleTranslateCacheItem {
        return $this->findOneBy([
            'source_language_code' => $sourceLanguageCode,
            'target_language_code' => $targetLanguageCode,
            'source_text' => $sourceText,
        ]);
    }
}
