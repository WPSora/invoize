<?php

use Invoize\Models\Setting;

function invoizeUpdateOption($key, $value)
{
    return update_option('invoize.' . $key, $value);
}

function invoizeAddOption($key, $value)
{
    return add_option('invoize.' . $key, $value);
}

function invoizeGetOption($key, $defaultValue = false)
{
    $setting = Setting::key($key)->value('option_value') ?: $defaultValue;

    if ($setting == 'false') {
        return false;
    }

    if ($setting == 'true') {
        return true;
    }

    return is_serialized($setting) ? invoize_mb_unserialize($setting) : $setting;
}

function invoizeFormatNumber($currencyName, $num)
{
    $decimal = floor($num) != $num ? 2 : 0;
    if ($currencyName == 'IDR') {
        return number_format((float) $num, $decimal, ',', '.');
    }
    return number_format((float) $num, $decimal, '.', ',');
}

function invoizeFormatCurrency($currencyName, $num)
{
    $decimal = floor($num) != $num ? 2 : 0;
    if ($currencyName == 'IDR') {
        // thousand separator: . ; decimal separator: ,
        $idrCurrencyFormatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $idrCurrencyFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimal);
        return $idrCurrencyFormatter->formatCurrency((float) $num, $currencyName);
    }
    // thousand separator: , ; decimal separator: .
    $usdCurrencyFormatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    $usdCurrencyFormatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $decimal);
    return $usdCurrencyFormatter->formatCurrency((float) $num, $currencyName);
}

function invoizeFormatDate($date, $dateFormat = false)
{
    $parsed = \Carbon\Carbon::parse($date);
    $dateFormat = $dateFormat ?: get_option('date_format');
    return $parsed->format($dateFormat);
}

function invoizeDateMomentFormat($format)
{
    $replacements = [
        'd' => 'DD',
        'D' => 'ddd',
        'j' => 'D',
        'l' => 'dddd',
        'N' => 'E',
        'S' => 'o',
        'w' => 'e',
        'z' => 'DDD',
        'W' => 'W',
        'F' => 'MMMM',
        'm' => 'MM',
        'M' => 'MMM',
        'n' => 'M',
        't' => '', // no equivalent
        'L' => '', // no equivalent
        'o' => 'YYYY',
        'Y' => 'YYYY',
        'y' => 'YY',
        'a' => 'a',
        'A' => 'A',
        'B' => '', // no equivalent
        'g' => 'h',
        'G' => 'H',
        'h' => 'hh',
        'H' => 'HH',
        'i' => 'mm',
        's' => 'ss',
        'u' => 'SSS',
        'e' => 'zz', // deprecated since version 1.6.0 of moment.js
        'I' => '', // no equivalent
        'O' => '', // no equivalent
        'P' => '', // no equivalent
        'T' => '', // no equivalent
        'Z' => '', // no equivalent
        'c' => '', // no equivalent
        'r' => '', // no equivalent
        'U' => 'X',
    ];
    $momentFormat = strtr($format, $replacements);
    return $momentFormat;
}


function invoizeGenerateToken($invoiceNumber, $clientId)
{
    return md5($invoiceNumber . '-' . $clientId . '-' . time());
}

// To unserialize emoji that can't be unserialized normally
function invoize_mb_unserialize($string)
{
    $string2 = preg_replace_callback(
        '!s:(\d+):"(.*?)";!s',
        function ($m) {
            $len = strlen($m[2]);
            $result = "s:$len:\"{$m[2]}\";";
            return $result;
        },
        $string
    );
    return is_string($string2) ? unserialize($string2) : $string2;
}

function invoizeValidateNonce($nonce)
{
    if (!$nonce || !wp_verify_nonce($nonce, 'invoize-preview-nonce')) {
        wp_die('Unauthorized', 'Unauthorized', array('response' => 401));
    }
}
