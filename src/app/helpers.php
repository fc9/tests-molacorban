<?php

if (!function_exists('brl2decimal')) {
    function brl2decimal($brl, $decimals = 2) {
        if(preg_match('/^\d+\.{1}\d+$/', $brl))
            return (float) number_format($brl, $decimals, '.', '');
        
        $brl = preg_replace('/[^\d\.\,]+/', '', $brl);
        $decimal = str_replace('.', '', $brl);
        $decimal = str_replace(',', '.', $decimal);

        return (float) number_format($decimal, $decimals, '.', '');
    }
}
