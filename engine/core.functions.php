<?php
/**
 * User: Arris
 * Date: 03.02.2018, time: 22:20
 */

/**
 *
 * Аналог list($dataset['a'], $dataset['b']) = explode(',', 'AAAAAA,BBBBBB'); только с учетом размерной массивов и дефолтными значениями
 * Example: array_fill_like_list($dataset, ['a', 'b', 'c'], explode(',', 'AAAAAA,BBBBBB'), 'ZZZZZ' );
 *
 * @package KarelWintersky/CoreFunctions
 *
 * @param array $target_array
 * @param array $indexes
 * @param array $source_array
 * @param null $default_value
 */
function array_fill_like_list(array &$target_array, array $indexes, array $source_array, $default_value = NULL)
{
    foreach ($indexes as $i => $index) {
        $target_array[ $index ] = array_key_exists($i, $source_array) ? $source_array[ $i ] : $default_value;
    }
}



/**
 * @package KarelWintersky/CoreFunctions
 *
 * @return null|string
 */
function getIp() {
    if (!isset ($_SERVER['REMOTE_ADDR'])) {
        return NULL;
    }

    if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER)) {
        $http_x_forwared_for = explode(",", $_SERVER["HTTP_X_FORWARDED_FOR"]);
        $client_ip = trim(end($http_x_forwared_for));
        if (filter_var($client_ip, FILTER_VALIDATE_IP)) {
            return $client_ip;
        }
    }

    return filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : NULL;
}

/**
 *
 * https://stackoverflow.com/a/17864552/5127037
 *
 * @package KarelWintersky/NetFunctions
 *
 * @param $ip
 * @return array
 */
function getCoordsByIP($ip) {
    $coords_nowhere = [
        'lat'   =>  NULL,
        'lng'   =>  NULL,
        'city'  =>  NULL
    ];

    $url = "http://ipinfo.io/{$ip}/json";

    $raw = file_get_contents($url);
    if ($raw === FALSE) return $coords_nowhere;

    $json = json_decode($raw, TRUE);
    if (($json === NULL) || ($json === FALSE)) return $coords_nowhere;

    $location = explode(',', $json['loc'], 2);
    return [
        'lat'   =>  $location[0] ?? NULL,
        'lng'   =>  $location[1] ?? NULL,
        'city'  =>  $json->city ?? ''
    ];
}


/**
 *
 * @package KarelWintersky/NetFunctions
 *
 * @param $name
 * @return mixed|null
 */
function getVKGroupInfo($name) {
    // https://vk.com/dev/groups.getById
    // https://vk.com/dev/objects/group - возвращаемые данные


    $url = 'https://api.vk.com/method/groups.getById?';
    $request_params = [
        'group_ids' =>  $name,
        'fields'    =>  'id,name,screen_name,type,city,country,cover,description',
        'v'         =>  '5.71'
    ];

    $raw = file_get_contents($url . http_build_query($request_params));
    if ($raw === FALSE) return NULL;

    $json = json_decode($raw, TRUE);
    if (($json === NULL) || ($json === FALSE)) return NULL;

    return $json;
}

if (!function_exists('dd')) {
    function dd($value) {
        echo '<pre>';
        var_dump($value);
        die;
    }
}


function getCityByCoords($lat, $lng) {
    if (!($lat&&$lng)) return NULL;

    //@todo: monolog - логгировать запросы

    $url = "https://geocode-maps.yandex.ru/1.x/?sco=latlong&kind=locality&format=json&geocode={$lat},{$lng}";
    $raw = file_get_contents($url);

    if ($raw === false) return NULL;

    $json = json_decode( $raw );
    if (($json === NULL) || ($json === FALSE)) return NULL;

    $feature_member = $json->response->GeoObjectCollection->featureMember;

    if (empty($feature_member)) return NULL;

    $geo_object = $feature_member[0]->GeoObject;

    $coords = explode(' ', $geo_object->Point->pos);

    return [
        'city'      =>  $geo_object->metaDataProperty->GeocoderMetaData->Address->formatted ?? NULL,
        'city_lat'  =>  $coords[1] ?? NULL,
        'city_lng'  =>  $coords[0] ?? NULL
    ];
}