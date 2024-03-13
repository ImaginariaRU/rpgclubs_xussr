<?php
/**
 * User: Arris
 * Date: 03.02.2018, time: 22:20
 */

use Curl\Curl;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;

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
function getVKGroupInfo($group_name, $debug = false) {
    $dataset = [
        'state'     =>  'error'
    ];
    /**
     * @var stdClass $response
     */

    $url = 'https://api.vk.com/method/groups.getById';
    $request_params = [
        'group_ids' =>  $group_name,
        'fields'    =>  'id,name,screen_name,type,city,country,cover,place,description,site,verified',
        'v'         =>  '5.71'
    ];
    $curl = new Curl();

    $curl->get($url, $request_params);
    if ($curl->error) return $dataset;

    $response = $curl->response;
    if (!$response) return $dataset;
    $curl->close();

if ($debug) dd($response);

    if (property_exists($response, 'error')) {
        return $dataset;
    } elseif (property_exists($response, 'response')) {
        $dataset['state'] = 'valid';
    }

    $data = $response->response[0];

    // чистая магия: массив адреса будет содержать только те элементы исходного массива, которые не NULL (соотв. поля $data существуют)
    $address_array = array_filter([
        $data->country->title ?? NULL,
        $data->city->title ?? NULL,
        $data->place->address ?? NULL
    ], function($item){
        return !!($item);
    });

    // теперь надо найти изображение
    // для этого мы перебираем массив $data->cover->images (если $data->cover->enabled == 1)
    // и у каждого элемента проверяем
    $image_url = '';

    if ($data->cover->enabled) {
        $image = array_filter($data->cover->images, function($i){
            return ($i->height > 194 && $i->height < 206);
        });

        $image_url = reset($image)->url ?? NULL;
    }

    $dataset += [
        'name'          =>  $data->name ?? NULL,
        'address'       =>  implode(', ', $address_array),
        'description'   =>  $data->description ?? NULL,
        'city'          =>  $data->city->title ?? NULL,
        'lat'           =>  $data->place->latitude ?? NULL,
        'lon'           =>  $data->place->longitude ?? NULL,
        'site'          =>  $data->site,
        'picture'       =>  $image_url,
        'group_type'    =>  $data->type
    ];

    return $dataset;

}





function doSendMail($destination, $subject, $text)
{
    $mail = new Message;
    $mail->setFrom('Autobot <admin@ролевыеклубы.рф>')
        ->addTo($destination)
        ->setSubject($subject)
        ->setBody($text);

    $mailer = new SendmailMailer;
    $mailer->send($mail);
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

