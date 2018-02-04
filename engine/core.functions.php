<?php
/**
 * User: Arris
 * Date: 03.02.2018, time: 22:20
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
 * @param $ip
 * @return array
 */
function getCoordsByIP($ip) {
    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"), true);

    // на самом деле нам надо получить город, для города получить координаты центра и вернуть их

    $location = explode(',', $details['loc'], 2);

    return [
        'lat'   =>  $location[0],
        'lng'   =>  $location[1]
    ];
}

function dd($value) {
    echo '<pre>';
    var_dump($value);
    die;
}