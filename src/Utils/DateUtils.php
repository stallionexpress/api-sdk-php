<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Utils;

use DateTime;

class DateUtils
{
    public static function toTimestamp(DateTime|int|string $dateTime): int
    {
        if (is_int($dateTime)) {
            return $dateTime;
        }

        if (is_string($dateTime)) {
            return (new DateTime($dateTime))->getTimestamp();
        }

        return $dateTime->getTimestamp();
    }
}
