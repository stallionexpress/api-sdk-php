<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static TaxTypeEnum EORI()
 * @method static TaxTypeEnum IOSS()
 * @method static TaxTypeEnum VAT()
 */
class TaxTypeEnum extends Enum
{
    const EORI = 'eori';
    const IOSS = 'ioss';
    const VAT = 'vat';
}
