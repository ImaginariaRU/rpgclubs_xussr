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

use Pecee\Http\Request;
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
        $template = new Template('view_list.html', '$/templates/clubs');

        $dbi = DBStatic::getInstance();

        $table = $dbi::$_table_prefix . 'clubs';

        $query = "SELECT * FROM {$table}";

        $dataset = [];

        foreach ($dbi->getConnection()->query($query)->fetchAll() as $row) {
            $dataset[ $row['id'] ] = [
                'owner'     =>  '*',           //@todo: реальный владелец (для админа показывает логин владельца, для владельца - "Я"
                'id'        =>  $row['id'],
                'title'     =>  $row['title'],
                'address'   =>  $row['address'],
                'url'       =>  $row['url'],
                'coords'    =>  "{$row['lat']} / {$row['lng']}"
            ];
        }

        $template->set('dataset', $dataset);

        $template->set('href', [
            'club_add'      =>  url('club_add_form'),
            'club_edit'     =>  url('club_edit_form'),
            'profile'       =>  url('profile_view'),
            'frontpage'     =>  url('frontpage')
        ]);

        return $template->render();
    }

    /**
     * Рисует форму добавления нового клуба
     *
     * @return string
     */
    public function form_club_add() {
        $template = new Template('form_add.html', '$/templates/clubs');

        $template->set('html/title', "Добавление клуба");

        $template->set('href', [
            'profile'    =>  url('profile_view'),
            'frontpage'  =>  url('frontpage'),
            'form_action'=>  url('club_add_callback')
        ]);

        return $template->render();
    }

    /**
     * Коллбэк формы добавления нового клуба
     * @return string
     */
    public function callback_club_add()
    {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        $query = "
        INSERT INTO {$table}
        (
          `id_owner`,
          `is_public`,
          `lat`,
          `lng`,
          `title`,
          `desc`,
          `address`,
          `picture`,
          `url`
        )
        VALUES
        (
          :id_owner,
          :is_public,
          :lat,
          :lng,
          :title,
          :desc,
          :address,
          :picture,
          :url
        )
        ";

        $sth = $dbi->getConnection()->prepare($query);

        $dataset = [
            "id_owner"  =>  1,
            "is_public" =>  input('clubs:add:is_public') ? 1 : 0,
            "lat"       =>  input('clubs:add:lat'),
            "lng"       =>  input('clubs:add:lng'),
            "title"     =>  input('clubs:add:title'),
            "desc"      =>  input('clubs:add:desc'),
            "address"   =>  input('clubs:add:address'),
            "picture"   =>  input('clubs:add:picture'),
            "url"       =>  input('clubs:add:url')

        ];

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        response()->redirect( url('clubs_list') );
    }

    public function form_club_edit($id) {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';
        $query = "SELECT * FROM {$table} WHERE `id` = :id ORDER BY `id` DESC LIMIT 1";

        $sth = $dbi->getConnection()->prepare($query);
        $sth->execute([
            'id'    =>  $id
        ]);
        $dataset = $sth->fetch();

        //@todo: по $dataset['id_owner'] получаем владельца

        $dataset['title'] = htmlspecialchars($dataset['title'], ENT_QUOTES | ENT_HTML5);

        $template = new Template('form_edit.html', '$/templates/clubs');

        $template->set('dataset', $dataset);

/*        $template->set('dataset', [
            'is_public'     =>  $dataset['is_public'],
            'title'         =>  htmlspecialchars($dataset['title'], ENT_QUOTES | ENT_HTML5),
            'desc'          =>  $dataset['desc'],
            'address'       =>  $dataset['address'],
            'lat'           =>  $dataset['lat'],
            'lng'           =>  $dataset['lng'],
            'picture'       =>  $dataset['picture'],
            'url'           =>  $dataset['url']
        ]);*/

        $template->set('html/title', "Редактирование клуба");
        $template->set('href', [
            'profile'    =>  url('profile_view'),
            'frontpage'  =>  url('frontpage'),
            'form_action'=>  url('club_edit_callback')
        ]);

        return $template->render();
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