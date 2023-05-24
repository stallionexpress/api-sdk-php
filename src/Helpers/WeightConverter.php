<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Helpers;

use MyParcelCom\ApiSdk\Enums\WeightUnitEnum;
use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;

class WeightConverter
{
    public static function convert(float|int $weight, string $from, string $to): float|int
    {
        $weightInGrams = self::convertToGrams($weight, $from);

        return self::convertFromGrams($weightInGrams, $to);
    }

    private static function convertToGrams(float|int $weight, string $from): float|int
    {
        return match ($from) {
            WeightUnitEnum::MILLIGRAM => $weight / 1000,
            WeightUnitEnum::GRAM => $weight,
            WeightUnitEnum::KILOGRAM => $weight * 1000,
            WeightUnitEnum::OUNCE => $weight * 28.3495,
            WeightUnitEnum::POUND => $weight * 453.592,
            default => throw new MyParcelComException('Invalid weight unit'),
        };
    }

    private static function convertFromGrams(float|int $weightInGrams, string $to): float|int
    {
        return match ($to) {
            WeightUnitEnum::MILLIGRAM => $weightInGrams * 1000,
            WeightUnitEnum::GRAM => $weightInGrams,
            WeightUnitEnum::KILOGRAM => $weightInGrams / 1000,
            WeightUnitEnum::OUNCE => $weightInGrams / 28.3495,
            WeightUnitEnum::POUND => $weightInGrams / 453.592,
            default => throw new MyParcelComException('Invalid weight unit'),
        };
    }
}
