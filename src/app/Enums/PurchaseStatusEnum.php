<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Estorno()
 * @method static static Cancelada()
 * @method static static Paga()
 */
final class PurchaseStatusEnum extends Enum
{
    const ESTORNO = -1;
    const CANCELADA = 0;
    const PAGA = 1;
}
