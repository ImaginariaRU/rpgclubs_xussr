<?php
/**
 * User: Arris
 *
 * Class Clubs
 * Namespace: Units
 *
 * Date: 04.02.2018, time: 18:48
 */


use ReCaptcha\ReCaptcha;
use RPGCAtlas\Classes\DBStatic;
use RPGCAtlas\Classes\StaticConfig;
use RPGCAtlas\Classes\Template;

class Clubs
{




    /* ============ редактирование =============== */
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

        $template->set('html/title', "Редактирование клуба");
        $template->set('href', [
            'profile'           =>  \RPGCAtlas\Units\url('profile_view'),
            'frontpage'         =>  \RPGCAtlas\Units\url('frontpage'),
            'clubs_list'        =>  \RPGCAtlas\Units\url('clubs_list'),
            'ajax_get_city'     =>  \RPGCAtlas\Units\url('ajax_get_city_by_coords'),
            'form_action_submit'=>  \RPGCAtlas\Units\url('club_callback_edit', ['id' => $id]),
            'form_action_delete'=>  \RPGCAtlas\Units\url('club_callback_delete', ['id' => $id]),
            'form_action_toggle'=>  \RPGCAtlas\Units\url('club_callback_toggle', ['id' => $id]),
        ]);
        $template->set('options', [
            'captcha_enabled'   =>  StaticConfig::get('google_recaptcha/enabled'),
            'captcha_sitekey'   =>  StaticConfig::get('google_recaptch/site_key')
        ]);

        return $template->render();
    }

    public function callback_club_edit($id) {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        $query = "
        UPDATE {$table} SET
        `id_owner` = :id_owner,
        `is_public` = :is_public,
        `lat` = :lat,
        `lng` = :lng,
        `title` = :title,
        `desc` = :desc,
        `address` = :address,
        `address_city` = :address_city,
        `banner_horizontal` = :banner_horizontal,
        `banner_vertical` = :banner_vertical,
        `url_site` = :url_site,
        `ipv4_edit` = :ipv4_edit
        WHERE `id` = :id
        ";

        $sth = $dbi->getConnection()->prepare($query);

        $dataset = [
            "id"        =>  \RPGCAtlas\Units\input('club:edit:id'),
            "id_owner"  =>  \RPGCAtlas\Units\input('club:edit:id_owner'),
            "is_public" =>  \RPGCAtlas\Units\input('club:edit:is_public') ? 1 : 0,
            "lat"       =>  \RPGCAtlas\Units\input('club:edit:lat'),
            "lng"       =>  \RPGCAtlas\Units\input('club:edit:lng'),
            "title"     =>  \RPGCAtlas\Units\input('club:edit:title'),
            "desc"      =>  \RPGCAtlas\Units\input('club:edit:desc'),
            "address"   =>  \RPGCAtlas\Units\input('club:edit:address'),
            "banner_horizontal" =>  \RPGCAtlas\Units\input('club:edit:banner_horizontal'),
            "banner_vertical"   =>  \RPGCAtlas\Units\input('club:edit:banner_vertical'),
            "url_site"       =>  \RPGCAtlas\Units\input('club:edit:url_site'),
            "ipv4_edit"     =>  getIp()
        ];
        if (!$dataset['address_city']) {
            $dataset['address_city'] = getCityByCoords($dataset['lat'], $dataset['lng'])['city'];
        }

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        \RPGCAtlas\Units\response()->redirect( \RPGCAtlas\Units\url('clubs_list'));
    }

    public function callback_club_delete($id) {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        $query = "
        DELETE FROM {$table}
        WHERE `id` = :id
        ";

        $sth = $dbi->getConnection()->prepare($query);

        $dataset = [
            "id" =>  $id,
        ];

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        \RPGCAtlas\Units\response()->redirect( \RPGCAtlas\Units\url('clubs_list'));
    }

    public function callback_club_visibility_toggle($id)
    {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        $query = "UPDATE {$table} SET `id_public` = NOT `is_public` WHERE `id` = :id";

        $sth = $dbi->getConnection()->prepare($query);
        $dataset = [
            "id" =>  $id,
        ];

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        return json_encode([TRUE]);
    }

}