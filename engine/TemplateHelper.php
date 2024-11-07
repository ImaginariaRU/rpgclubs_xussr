<?php

namespace RPGCAtlas;

use Arris\AppRouter;

class TemplateHelper
{
    public static $inner_buttons = [];

    public static function init()
    {
        self::$inner_buttons = [
            [
                'text'      =>  'На карту',
                'url'       =>  AppRouter::getRouter('view.main.page'),
                'class'     =>  '',
                'disabled'  =>  false
            ],
            [], // empty means separator
            [
                'text'      =>  'POI List',
                'url'       =>  AppRouter::getRouter('view.poi.list'),
                'class'     =>  '',
                'disabled'  =>  false
            ],
            [
                'text'      =>  'POI Types',
                'url'       =>  AppRouter::getRouter('view.poi_types.list'),
                'class'     =>  '',
                'disabled'  =>  true
            ],
            [
                'text'      =>  'Tickets',
                'url'       =>  AppRouter::getRouter('view.ticket.list'),
                'class'     =>  '',
                'disabled'  =>  false
            ]
        ];
    }

    /**
     * @return void
     */
    public static function assignInnerButtons()
    {
        App::$template->assign("inner_buttons", self::$inner_buttons);
    }


    /**
     * Добавляет внутреннюю кнопку в список
     *
     * @param $button_definition `[ url => '', text => '', 'disabled' => true|false, 'class' => 'custom class' ]`
     * @return void
     */
    public static function addInnerButton($button_definition, $priority = null)
    {
        if (!is_null($priority)) {
            self::$inner_buttons[ $priority ] = $button_definition;
        } else {
            self::$inner_buttons[] = $button_definition;
        }
    }

}