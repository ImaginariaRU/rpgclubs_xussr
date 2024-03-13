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
 * Определяет город по координатам, используя АПИ Яндекс-геокодера
 *
 * @dependancy php-curl-class/php-curl-class AS Curl
 * @param $lat
 * @param $lng
 * @return array|null
 */
function getCityByCoords($lat, $lng) {
    if (!($lat&&$lng)) return NULL;

    /**
     * @var stdClass $response
     */

    $url =  "https://geocode-maps.yandex.ru/1.x/";
    $request_params = [
        'sco'       =>  'latlong',
        'kind'      =>  'locality',
        'format'    =>  'json',
        'geocode'   =>  "{$lat},{$lng}"
    ];

    $curl = new Curl();

    $curl->get($url, $request_params);
    if ($curl->error) return NULL;

    $response = $curl->response;
    if (!$response) return NULL;
    $curl->close();

    $feature_member = $response->response->GeoObjectCollection->featureMember;

    if (empty($feature_member)) return NULL;

    $geo_object = $feature_member[0]->GeoObject;

    // note Yandex returns string coords as LNG-LAT
    $coords = explode(' ', $geo_object->Point->pos);

    return [
        'city'      =>  $geo_object->name ?? NULL,
        'city_lat'  =>  $coords[1] ?? NULL,
        'city_lng'  =>  $coords[0] ?? NULL
    ];
}


function getCoordsByAddress($address)
{
    /**
     * @var stdClass $response
     */

    if (!$address) return NULL;

    $url =  "https://geocode-maps.yandex.ru/1.x/";
    $request_params = [
        'format'    =>  'json',
        'geocode'   =>  "$address"
    ];

    $curl = new Curl();

    $curl->get($url, $request_params);
    if ($curl->error) return NULL;

    $response = $curl->response;
    if (!$response) return NULL;
    $curl->close();

    $feature_member = $response->response->GeoObjectCollection->featureMember;

    if (empty($feature_member)) return NULL;

    $geo_object = $feature_member[0]->GeoObject;

    $address_array = array_filter([
        $geo_object->description ?? NULL,
        $geo_object->name ?? NULL
    ], function($item){
        return !!($item);
    });

    // note Yandex returns string coords as LNG-LAT
    $coords = explode(' ', $geo_object->Point->pos);

    return [
        'city'      =>  implode(', ', $address_array),
        'city_lat'  =>  $coords[1] ?? NULL,
        'city_lng'  =>  $coords[0] ?? NULL
    ];
}

function input(string $key, string $default = '')
{
    return $_REQUEST[$key] ?: $default;
}
