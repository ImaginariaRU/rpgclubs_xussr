<?php
/**
 * User: Arris
 *
 * Class StaticConfig
 * Namespace: RPGCAtlas\Classes
 *
 * Date: 03.02.2018, time: 20:45
 */

namespace RPGCAtlas\Classes;

class StaticConfig
{
    const GLUE = '/';
    private static $config;

    public static function set_config(INIConfig $config) {
        self::$config = $config;
    }

    public static function get_config():INIConfig {
        return self::$config;
    }


    public function get($parents, $default_value = NULL)
    {
        if ($parents === '') {
            return $default_value;
        }

        if (!is_array($parents)) {
            $parents = explode($this::GLUE, $parents);
        }

        $ref = &self::$config;

        foreach ((array) $parents as $parent) {
            if (is_array($ref) && array_key_exists($parent, $ref)) {
                $ref = &$ref[$parent];
            } else {
                return $default_value;
            }
        }
        return $ref;
    }


    public function set($parents, $value)
    {
        if (!is_array($parents)) {
            $parents = explode($this::GLUE, (string) $parents);
        }

        if (empty($parents)) return false;

        $ref = &self::$config;

        foreach ($parents as $parent) {
            if (isset($ref) && !is_array($ref)) {
                $ref = array();
            }

            $ref = &$ref[$parent];
        }

        $ref = $value;
        return true;
    }

    /**
     * @param array $array
     * @param array|string $parents
     */
    private function array_unset_value(&$array, $parents)
    {
        if (!is_array($parents)) {
            $parents = explode($this::GLUE, $parents);
        }

        $key = array_shift($parents);

        if (empty($parents)) {
            unset($array[$key]);
        } else {
            $this->array_unset_value($array[$key], $parents);
        }
    }

}