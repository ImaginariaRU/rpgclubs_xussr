<?php
/**
 * User: Arris
 *
 * Class Clubs
 * Namespace: Units
 *
 * Date: 04.02.2018, time: 18:48
 */

namespace RPGCAtlas\Units;

use Pecee\Http\Request as Request;
use RPGCAtlas\Classes\StaticConfig;
use RPGCAtlas\Classes\Template;
use RPGCAtlas\Classes\DBStatic;

class Clubs
{
    /**
     * Рисует страницу: Список клубов, доступных для изменения текущему пользователю
     *
     * @return string
     */
    public function view_clubs() {
        $template = new Template('list.html', '$/templates/clubs');

        $dbi = DBStatic::getInstance();

        $table = $dbi::$_table_prefix . 'clubs';

        $query = "SELECT * FROM {$table}";

        $dataset = [];

        foreach ($dbi->getConnection()->query($query)->fetchAll() as $row) {
            $dataset[ $row['id'] ] = [
                'id'        =>  $row['id'],
                'title'     =>  $row['title'],
                'address'   =>  $row['address'],
                'url'       =>  $row['url'],
                'coords'    =>  "{$row['lat']} / {$row['lng']}"
            ];
        }

        $template->set('dataset', $dataset);

        $template->set('href', [
            'profile'    =>  url('profile_view'),
            'frontpage'  =>  url('frontpage')
        ]);

        return $template->render();
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