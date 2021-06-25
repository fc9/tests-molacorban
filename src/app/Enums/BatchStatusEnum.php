<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static Erro()
 * @method static static Carregando()
 * @method static static Ok()
 */
final class BatchStatusEnum extends Enum implements LocalizedEnum
{
    const ERROR = 0;
    const IN_FILE = 1;
    const LOADING = 2;
    const DONE = 3;

    public static function keys2Values(array $arr)
    {
        return array_map(function ($item) {
            return self::fromKey(strtoupper($item))->value;
        }, $arr);;
    }
}
