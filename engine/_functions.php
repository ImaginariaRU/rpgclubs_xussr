<?php

use Arris\App;

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
