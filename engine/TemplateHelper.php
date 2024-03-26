<?php

namespace RPGCAtlas;

class TemplateHelper
{
    public static $inner_buttons = [];

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