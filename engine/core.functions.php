<?php
/**
 * User: Arris
 * Date: 03.02.2018, time: 22:20
 */

use Curl\Curl;

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
    /**
     * @var stdClass $response
     */

    // координаты "нигде" - это центр карты РФ с зумом чтобы влезло всё. Это, неожиданно, Екатеринбург!
    $coords_not_resolved = [
        'lat'   =>  56.769540,
        'lng'   =>  60.334709,
        'zoom'  =>  4,
        'city'  =>  NULL
    ];

    $url = "http://ipinfo.io/{$ip}/geo";

    $curl = new Curl();
    $curl->get($url, [
        'token'     =>  \RPGCAtlas\Classes\StaticConfig::get('ipinfo/token')
    ]);

    if ($curl->error) return $coords_not_resolved;

    $response = $curl->response;
    $curl->close();

    if (!$response) return $coords_not_resolved;

    $latlng = explode(',', $response->loc, 2);
    return [
        'lat'   =>  $latlng[0] ?? NULL,
        'lng'   =>  $latlng[1] ?? NULL,
        'city'  =>  ($response->region ?? NULL) . ' ' . ($response->city ?? NULL)
    ];
}


/**
 * Возвращает публичную информацию о группе в ВКонтакте по айди или идентификатору
 *
 * @param $group_name
 * @return object
 */
function getVKGroupInfo($group_name) {
    /**
     * @var stdClass $response
     */

    $url = 'https://api.vk.com/method/groups.getById';
    $request_params = [
        'group_ids' =>  $group_name,
        'fields'    =>  'id,name,screen_name,type,city,country,cover,description',
        'v'         =>  '5.71'
    ];
    $curl = new Curl();

    $curl->get($url, $request_params);
    if ($curl->error) return NULL;

    $response = $curl->response;
    if (!$response) return NULL;
    $curl->close();

    return $response->response[0];
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

    $coords = explode(' ', $geo_object->Point->pos);

    return [
        'city'      =>  $geo_object->name ?? NULL,
        'city_lat'  =>  $coords[1] ?? NULL,
        'city_lng'  =>  $coords[0] ?? NULL
    ];
}



if (!function_exists('dd')) {

    /**
     * Dump and die
     * @param $value
     */
    function dd($value) {
        echo '<pre>';
        var_dump($value);
        die;
    }
}

