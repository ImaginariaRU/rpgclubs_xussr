<?php

namespace RPGCAtlas;

use Arris\App;
use Arris\Database\DBWrapper;
use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;

class Common
{
    public static function logSiteUsage(LoggerInterface $logger, $is_print = false)
    {
        $metrics = [
            'time.total'        =>  number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 6, '.', ''),
            'memory.usage'      =>  memory_get_usage(true),
            'memory.peak'       =>  memory_get_peak_usage(true),
            'site.url'          =>  $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
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

    public static function return_bytes($val)
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
     * ->
     *
     * @param $key
     * @return int
     */
    public static function get_ini_value($key)
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

    /**
     * @param $datetime
     * @return string
     *
     */
    public static function convertDateTime($datetime):string
    {
        if ($datetime === "0000-00-00 00:00:00" || $datetime === "0000-00-00" || empty($datetime)) {
            return "-";
        }

        if (intval($datetime)) {
            $datetime = date('Y-m-d H:i:s', $datetime);
        }

        $year_suffix = self::yearSuffux;
        list($y, $m, $d, $h, $i, $s) = sscanf($datetime, "%d-%d-%d %d:%d:%d");

        return sprintf("%s %s %s %02d:%02d", $d, self::ruMonths[$m], $y ? "{$y} {$year_suffix}" : "", $h, $i);
    }



}