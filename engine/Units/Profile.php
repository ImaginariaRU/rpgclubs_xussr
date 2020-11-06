<?php
/**
 * User: Arris
 *
 * Class Profile
 * Namespace: RPGCAtlas\Units
 *
 * Date: 03.02.2018, time: 18:51
 */

namespace RPGCAtlas\Units;

use Pecee\Http\Request as Request;
use RPGCAtlas\Classes\Template;
use RPGCAtlas\Classes\DBStatic;

class Profile
{
    /**
     * Страница информации о профайле текущего пользователя
     *
     * @return string
     */
    public function view() {
        return "Profile";
    }

    /**
     * Рисует форму редактирования профиля текущего пользователя
     *
     * @return string
     */
    public function form_edit(){
        return "Profile::edit";
    }


}