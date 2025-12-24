<?php

namespace App\Domain\ApiGrid;

use App\Domain\ApiGrid\OrderEnum;
use App\Domain\ApiGrid\OrderEnumInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class ApiGridPropertyHelper
{
    public const ORDER_KEY = 'order';
    public const SORT_KEY = 'sort';
    public const PAGE_KEY = 'page';
    public const MAX_RESULTS_KEY = 'perPage';
    public const MAX_RESULTS_DEFAULT_VALUE = 100;

    public function createSortEnum(
        Request $request,
        string $sortEnumClass,
        OrderEnumInterface $defaultValue,
        string $requestParamKey = self::SORT_KEY,
    ): OrderEnumInterface {
        $value = $request->query->get($requestParamKey);

        try {
            assert(is_string($value));

            return $sortEnumClass::from($value);
        } catch (Throwable) {
            return $defaultValue;
        }
    }

    public function createOrderEnum(
        Request $request,
        OrderEnum $defaultValue = OrderEnum::ASC,
        string $requestParamKey = self::ORDER_KEY,
    ): OrderEnum {
        $value = $request->query->get($requestParamKey);

        try {
            assert(is_string($value));

            return OrderEnum::from($value);
        } catch (Throwable) {
            return $defaultValue;
        }
    }

    public function createfirstResult(
        Request $request,
        int $maxResults,
        int $defaultValue = 0,
        string $requestParamKey = self::PAGE_KEY
    ): int {
        $value = $request->query->get($requestParamKey);

        try {
            assert(is_numeric($value));

            return (int)(($value - 1) * $maxResults);
        } catch (Throwable) {
            return $defaultValue;
        }
    }

    public function createMaxResults(
        Request $request,
        int $defaultValue = self::MAX_RESULTS_DEFAULT_VALUE,
        string $requestParamKey = self::MAX_RESULTS_KEY
    ): int {
        $value = $request->query->get($requestParamKey);

        try {
            assert(is_numeric($value));

            return (int)$value;
        } catch (Throwable) {
            return $defaultValue;
        }
    }

    public function createPageCount(
        int $itemCount,
        int $maxResults
    ): int {
        return (int)ceil($itemCount / $maxResults);
    }
}
