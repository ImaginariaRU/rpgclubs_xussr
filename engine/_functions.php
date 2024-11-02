<?php

use Arris\App;
use Arris\Core\Curl;

/**
 * @param string|array $key
 * @param $value [optional]
 * @return string|array|bool|mixed|null
 */
function config($key = '', $value = null) {
    $app = App::factory();

    if (!is_null($value) && !empty($key)) {
        $app->setConfig($key, $value);
        return true;
    }

    if (is_array($key)) {
        foreach ($key as $k => $v) {
            $app->setConfig($k, $v);
        }
        return true;
    }

    if (empty($key)) {
        return $app->getConfig();
    }

    return $app->getConfig($key);
}

/**
 * @param $key
 * @param $value
 *
 * @return array|bool|mixed|null
 */
function app($key = null, $value = null) {
    $app = App::factory();

    if (!is_null($value)) {
        $app->set($key, $value);
        return true;
    }

    if (is_array($key)) {
        foreach ($key as $k => $v) {
            $app->set($k, $v);
        }
        return true;
    }

    if (empty($key)) {
        return $app->get();
    }

    return $app->get($key);
}

/**
 * Генерирует UUID, используя системный генератор случайных чисел
 *
 * @return string
 */
function getSystemUUID()
{
    return trim(file_get_contents('/proc/sys/kernel/random/uuid'));
}

/**
 *
 * @param int $size
 * @param int $decimals
 * @param string $decimal_separator
 * @param string $thousands_separator
 * @return string
 */
function size_format(int $size, int $decimals = 0, string $decimal_separator = '.', string $thousands_separator = ','): string {
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    $index = min(floor((strlen(strval($size)) - 1) / 3), count($units) - 1);
    $number = number_format($size / pow(1000, $index), $decimals, $decimal_separator, $thousands_separator);
    return sprintf('%s %s', $number, $units[$index]);
}

/**
 * @param $key
 * @return int
 */
function get_ini_value($key)
{
    $val = trim(ini_get($key));
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return (int)$val;
}

function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

/**
 * @param string $key
 * @param string $default
 * @return mixed
 */
function input(string $key = '', string $default = ''):mixed
{
    if (empty($key)) {
        return $_REQUEST;
    }

    return $_REQUEST[$key] ?: $default;
}
