<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Utils;

use MyParcelCom\ApiSdk\Exceptions\ConversionException;

class DistanceUtils
{
    const UNIT_KILOMETER = 'kilometers';
    const UNIT_METER = 'meters';
    const UNIT_MILE = 'miles';
    const UNIT_FOOT = 'feet';

    private static array $unitConversion = [
        self::UNIT_METER     => 1,
        self::UNIT_KILOMETER => 1000,
        self::UNIT_MILE      => 1609.344,
        self::UNIT_FOOT      => 0.3048,
    ];

    /**
     * Converts given distance from $sourceUnit to $destinationUnit.
     *
     * @example DistanceUtils::convertDistance(100, DistanceUtils::UNIT_KILOMETER, DistanceUtils::UNIT_MILE)
     *          will return `62,137119224`.
     */
    public static function convertDistance(float|int $distance, string $sourceUnit, string $destinationUnit): float|int
    {
        if (!isset(self::$unitConversion[$sourceUnit])) {
            throw new ConversionException('Cannot convert distance with unit: ' . $sourceUnit);
        }
        if (!isset(self::$unitConversion[$destinationUnit])) {
            throw new ConversionException('Cannot convert to distance with unit: ' . $destinationUnit);
        }

        if ($sourceUnit === $destinationUnit) {
            return $distance;
        }

        return $distance * self::$unitConversion[$sourceUnit] / self::$unitConversion[$destinationUnit];
    }
}
