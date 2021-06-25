<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Removido()
 * @method static static Efetivado()
 */
final class PaymentStatusEnum extends Enum
{
    const REMOVIDO = 0;
    const EFETIVADO = 1;
}
