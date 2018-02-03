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

    /**
     * Рисует страницу: Список клубов, доступных для изменения текущему пользователю
     *
     * @return string
     */
    public function view_clubs() {
        return "Profile::clubs list";
    }

    /**
     * Рисует форму добавления нового клуба
     *
     * @return string
     */
    public function form_clubs_add() {
        return "Profile::clubs add form";
    }

    /**
     * Коллбэк формы добавления нового клуба
     * @return string
     */
    public function callback_clubs_add() {
        return "Profile::clubs add callback";
    }

    public function form_clubs_edit($id, Request $request) {
        return "Profile::clubs edit form for {$id}" ;
    }

    public function callback_club_edit($id) {
        return "Profile::clubs edit callback for {$id}" ;
    }

    public function callback_club_delete($id) {
        return "Profile::clubs delete {$id} club" ;
    }

    public function callback_club_visibility_toggle($id) {
        return "Profile::clubs AJAX toggle visibility for {$id}" ;
    }

}