<?php

namespace RPGCAtlas;

use Arris\App;
use Arris\Core\Curl;
use Arris\Database\DBWrapper;
use Arris\Entity\Result;
use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;

class Common
{
    public static function logSiteUsage(LoggerInterface $logger, $is_print = false): void
    {
        $metrics = [
            'time.total'        =>  number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 6, '.', ''),
            'memory.usage'      =>  memory_get_usage(true),
            'site.url'          =>  idn_to_utf8($_SERVER['HTTP_HOST']) . $_SERVER['REQUEST_URI'],
            'isMobile'          =>  config('features.is_mobile'),
        ];

        /**
         * @var DBWrapper $pdo
         */
        $pdo = (App::factory())->getService('pdo');

        if (!is_null($pdo)) {
            $stats = $pdo->getStats();
            $metrics['mysql.queries'] = $stats['total_queries'];
            $metrics['mysql.time'] = $stats['total_time'];
        }

        $metrics['ipv4'] = Server::getIP();

        /*if ($is_print) {
            $site_usage_stats = sprintf(
                '<!-- Consumed memory: %u bytes, SQL query count: %u, SQL time %g sec, Total time: %g sec. -->',
                $metrics['memory.usage'],
                $metrics['MySQL']['Queries'],
                $metrics['MySQL']['Time'],
                $metrics['time.total']
            );
            echo $site_usage_stats . PHP_EOL;
        }*/

        $logger->notice('', $metrics);
    }

    /**
     * Конвертирует в русскую дату
     * (не используется)
     *
     * @param $datetime
     * @return string
     */
    public static function convertDateTimeRU(string $datetime):string
    {
        $year_suffix = 'г. ';
        $ruMonths = [
            1 => 'января', 2 => 'февраля',
            3 => 'марта', 4 => 'апреля', 5 => 'мая',
            6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября',
            12 => 'декабря'
        ];


        if ($datetime === "0000-00-00 00:00:00" || $datetime === "0000-00-00" || empty($datetime)) {
            return "-";
        }

        if (intval($datetime)) {
            $datetime = date('Y-m-d H:i:s', $datetime);
        }

        list($y, $m, $d, $h, $i, $s) = sscanf($datetime, "%d-%d-%d %d:%d:%d");

        return sprintf("%s %s %s %02d:%02d", $d, $ruMonths[$m], $y ? "{$y} {$year_suffix}" : "", $h, $i);
    }

    /**
     *
     * @param string $ip
     * @return array[string: lat, string: lng, string: city]
     */
    public static function getCoordsByIP(string $ip): array
    {
        $coords_not_resolved = [
            'lat'   =>  56.769540,
            'lng'   =>  60.334709,
            'zoom'  =>  4,
            'city'  =>  NULL
        ];
        if ($ip == '127.0.0.1') {
            return $coords_not_resolved;
        }

        $url = "http://ipinfo.io/{$ip}/geo";

        $curl = new Curl();
        $curl->get($url, [
            'token'     =>  _env('IPINFO.TOKEN', '')
        ]);
        if ($curl->error) {
            return $coords_not_resolved;
        }

        $response = $curl->response;
        $curl->close();

        if (!$response) {
            return $coords_not_resolved;
        }

        $response = json_decode($response, true);

        if (!array_key_exists('loc', $response)) {
            return $coords_not_resolved;
        }

        $latlng = explode(',', $response['loc'], 2);
        return [
            'lat'   =>  $latlng[0] ?? null,
            'lng'   =>  $latlng[1] ?? null,
            'zoom'  =>  4,
            'city'  =>  $response->city ?? null
        ];
    }

    /**
     * Нужен ключ авторизации
     *
     * Некоторые ссылки:
     * https://dev.vk.com/ru/api/access-token/implicit-flow-user?ref=old_portal
     * https://dev.vk.com/ru/method/groups.getById
     * https://dev.vk.com/ru/api/access-token/getting-started#%D0%A1%D0%B5%D1%80%D0%B2%D0%B8%D1%81%D0%BD%D1%8B%D0%B9%20%D0%BA%D0%BB%D1%8E%D1%87%20%D0%B4%D0%BE%D1%81%D1%82%D1%83%D0%BF%D0%B0
     *
     * https://dev.vk.com/ru/admin/apps-list
     *
     * @param $group_name
     * @param $debug
     * @return Result
     */
    public static function getVKGroupInfo($group_name, $debug = false)
    {
        $r = new Result();

        $url = 'https://api.vk.com/method/groups.getById';
        $request_params = [
            'access_token' => _env('VK.SERVICE_KEY', ''),
            'group_id' =>  $group_name,
            'fields'    =>  'id,name,screen_name,type,city,country,cover,place,description,site,verified',
            'v'         =>  '5.199'
        ];
        $curl = new Curl();

        $curl->get($url, $request_params);
        if ($curl->error) {
            $r->error($curl->error_message);
            return $r;
        }

        $response = $curl->response;
        if (!$response) {
            $r->error("Empty response");
            return $r;
        }
        $curl->close();

        if (property_exists($response, 'error')) {
            $r->error("Error field found in response answer");
            return $r;
        } elseif (property_exists($response, 'response')) {
            $r->success("Response is valid");
        }

        $response = json_decode($response);

        /**
         * @var \stdClass $group
         */
        $group = $response->response->groups[0];

        $image_url = '';
        if ($group->cover->enabled) {
            $image = array_filter($group->cover->images, function($i){
                // return ($i->height > 194 && $i->height < 206);
                return $i->width == 400;
            });

            $image_url = reset($image)->url ?? null;
        }

        $r->setData([
            'id'            =>  $group->id,
            'city'          =>  $group->city->title,
            'description'   =>  $group->description,
            'site'          =>  $group->site,
            'name'          =>  $group->name,
            'screen_name'   =>  $group->screen_name,
            'group_type'    =>  $group->type,
            'picture'       =>  $image_url,

        ]);

        /*
        // чистая магия: массив адреса будет содержать только те элементы исходного массива, которые не NULL (соотв. поля $data существуют)
        $data = $response->response[0];
        $address_array = array_filter([
            $data->country->title ?? null,
            $data->city->title ?? null,
            $data->place->address ?? null
        ], static function($item) {
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

            $image_url = reset($image)->url ?? null;
        }

        $r->setData([
            'name'          =>  $data->name ?? null,
            'address'       =>  implode(', ', $address_array),
            'description'   =>  $data->description ?? null,
            'city'          =>  $data->city->title ?? null,
            'lat'           =>  $data->place->latitude ?? null,
            'lon'           =>  $data->place->longitude ?? null,
            'site'          =>  $data->site,
            'picture'       =>  $image_url,
            'group_type'    =>  $data->type
        ]);*/

        return $r;
    }



}