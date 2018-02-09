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

use ReCaptcha\ReCaptcha;
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
        $template = new Template('view_list.html', '$/templates/clubs');

        $dbi = DBStatic::getInstance();

        $table = $dbi::$_table_prefix . 'clubs';

        $query = "SELECT * FROM {$table} ORDER BY `id` DESC";

        $dataset = [];
        foreach ($dbi->getConnection()->query($query)->fetchAll() as $row) {
            $data = $row;
            $data['coords'] = "{$row['lat']} / {$row['lng']}";

            // еще нужно определить реального владельца по айди (id_owner)
            //@todo: реальный владелец (для админа показывает логин владельца, для владельца - "Я"

            $dataset[ $row['id'] ] = $data;
        }

        $template->set('dataset', $dataset);

        $template->set('summary', [
            // клубов всего
            'clubs_total'   =>  count($dataset),

            // кол-во клубов, у которых is_public = 1
            'clubs_visible' =>  count(array_filter($dataset, function($data){ return !!$data['is_public']; }) )
        ]);

        $template->set('href', [
            'club_add'      =>  url('club_form_add'),
            'club_edit'     =>  url('club_form_edit'),
            'profile'       =>  url('profile_view'),
            'frontpage'     =>  url('frontpage')
        ]);

        return preg_replace('/^\h*\v+/m', '', $template->render());
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
            'profile'           =>  url('profile_view'),
            'frontpage'         =>  url('frontpage'),
            'clubs_list'        =>  url('clubs_list'),
            'form_action_submit'=>  url('club_callback_add'),
            'ajax_get_city'     =>  url('ajax_get_city_by_coords')
        ]);
        $template->set('options', [
            'captcha_enabled'   =>  StaticConfig::get('google_recaptcha/enabled'),
            'captcha_sitekey'   =>  StaticConfig::get('google_recaptch/site_key')
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
          `address_city`,
          `banner_horizontal`,
          `banner_vertical`,
          `url_site`,
          `ipv4_add`
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
          :address_city,
          :banner_horizontal,
          :banner_vertical,
          :url_site,
          INET_ATON(:ipv4_add)
        )
        ";

        $sth = $dbi->getConnection()->prepare($query);

        $dataset = [
            "id_owner"  =>  1,
            "is_public" =>  input('club:add:is_public') ? 1 : 0,
            "lat"       =>  input('club:add:lat'),
            "lng"       =>  input('club:add:lng'),
            "title"     =>  input('club:add:title'),
            "desc"      =>  input('club:add:desc'),
            "address"   =>  input('club:add:address'),
            "address_city" => input('club:add:address_city'),
            "banner_horizontal" =>  input('club:add:banner_horizontal'),
            "banner_vertical"   =>  input('club:add:banner_vertical'),
            "url_site"       =>  input('club:add:url_site'),
            "ipv4_add"  =>  getIp()
        ];
        if (!$dataset['address_city']) {
            $dataset['address_city'] = getCityByCoords($dataset['lat'], $dataset['lng'])['city'];
        }

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        response()->redirect( url('clubs_list') );
    }

    /* ===================================================== */
    /* ============   анонимное добавление данных ========== */
    /* ===================================================== */
    public function form_unauth_add_any_club() {
        $template = new Template('form_unauth_add_any_club.html', '$/templates/clubs');

        $template->set('html/title', "Добавление клуба неавторизованным пользователем");

        $template->set('href', [
            'frontpage'         =>  url('frontpage'),
            'form_action_submit'=>  url('club_callback_unauth_add_any_club'),
            'ajax_get_city'     =>  url('ajax_get_city_by_coords')
        ]);
        $template->set('options', [
            'captcha_enabled'   =>  StaticConfig::get('google_recaptcha/enable'),
            'captcha_sitekey'   =>  StaticConfig::get('google_recaptcha/site_key')
        ]);

        return $template->render();
    }

    public function callback_unauth_add_any_club()
    {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        if (StaticConfig::get('google_recaptcha/enable') == 1) {
            // проверяем капчу
            $recaptcha = new ReCaptcha('6Lf3akQUAAAAAO3czhvEBEX0bda2NtwIJ8YorYHK');
            $checkout = $recaptcha->verify(input('g-recaptcha-response'), getIp());

            // неправильная капча?
            if (!$checkout->isSuccess()) {
                response()->redirect( url('club_form_unauthorized_add') ); // и как-то надо передать сообщение, что ошибка в капче. КАК?
            }
        }

        $query = "
        INSERT INTO {$table}
        (
          `id_owner`,
          `is_public`,
          `owner_email`,
          `owner_about`,
          `title`,
          `desc`,
          `lat`,
          `lng`,
          `zoom`,
          `address_city`,
          `address`,
          `banner_horizontal`,
          `banner_vertical`,
          `url_site`,
          `ipv4_add`
        )
        VALUES
        (
          :id_owner,
          :is_public,
          :owner_email,
          :owner_about,
          :title,
          :desc,
          :lat,
          :lng,
          :zoom,
          :address_city,
          :address,
          :banner_horizontal,
          :banner_vertical,
          :url_site,
          INET_ATON(:ipv4_add)
        )
        ";
        $sth = $dbi->getConnection()->prepare($query);

        $dataset = [
            "id_owner"      =>  0,
            "is_public"     =>  0,
            "owner_email"   =>  input('club:anonadd:owner_email'),
            "owner_about"   =>  input('club:anonadd:owner_about'),
            "title"         =>  input('club:anonadd:title'),
            "desc"          =>  input('club:anonadd:desc'),
            "zoom"          =>  12,                                     //@todo: фронтэнд-обработка (зум карты меняет значение в поле)
            "address_city"  =>  input('club:anonadd:address_city'),
            "address"       =>  input('club:anonadd:address'),
            "banner_horizontal" =>  input('club:anonadd:banner_horizontal'),
            "banner_vertical"   =>  input('club:anonadd:banner_vertical'),
            "url_site"      =>  input('club:anonadd:url_site'),
            "ipv4_add"      =>  getIp()
        ];
        if (!(input('club:anonadd:lat')&&input('club:anonadd:lng')) && (input('club:anonadd:latlng'))) {
            // координаты заданы строкой с карты
            // '59.925483, 30.259649'
            // trim, explode by ', '
            $set = explode(', ', trim(input('club:anonadd:latlng')));
            $dataset['lat'] = $set[0];
            $dataset['lng'] = $set[1];
        } else {
            $dataset['lat'] = input('club:anonadd:lat');
            $dataset['lng'] = input('club:anonadd:lng');
        }

        if (!$dataset['address_city']) {
            $dataset['address_city'] = getCityByCoords($dataset['lat'], $dataset['lng'])['city'];
        }

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        // сделать отправку письма на почту админу с датасетом

        response()->redirect( url('frontpage') );
    }


    /* ===================================================== */
    /* ============   VK Club Add  ========================= */
    /* ===================================================== */
    public function form_unauth_add_vk_club()
    {
        $template = new Template('form_unauth_add_vk_club.html', '$/templates/clubs');

        $template->set('html/title', "Добавление клуба неавторизованным пользователем");

        $template->set('href', [
            'frontpage'             =>  url('frontpage'),
            'form_action_submit'    =>  url('club_callback_unauth_add_vk_club'),

            'ajax_get_vk_club_info'     =>  url('ajax_get_vk_club_info'),
            'ajax_get_city_by_coords'   =>  url('ajax_get_city_by_coords'),
            'ajax_get_coords_by_address'=>  url('ajax_get_coords_by_address')
        ]);
        $template->set('options', [
            'captcha_enabled'   =>  StaticConfig::get('google_recaptcha/enable'),
            'captcha_sitekey'   =>  StaticConfig::get('google_recaptcha/site_key')
        ]);

        return $template->render();
    }

    public function callback_unauth_add_vk_club()
    {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        if (StaticConfig::get('google_recaptcha/enable') == 1) {
            // проверяем капчу
            $recaptcha = new ReCaptcha('6Lf3akQUAAAAAO3czhvEBEX0bda2NtwIJ8YorYHK');
            $checkout = $recaptcha->verify(input('g-recaptcha-response'), getIp());

            // неправильная капча?
            if (!$checkout->isSuccess()) {
                response()->redirect( url('club_form_unauth_add_vk_club') ); // и как-то надо передать сообщение, что ошибка в капче. КАК?
            }
        }

        $query = "
        INSERT INTO {$table}
        (
          `id_owner`,
          `is_public`,
          `owner_email`,
          `owner_about`,
          `title`,
          `desc`,
          `lat`,
          `lng`,
          `zoom`,
          `address_city`,
          `address`,
          `banner_horizontal`,
          `banner_vertical`,
          `url_site`,
          `ipv4_add`
        )
        VALUES
        (
          :id_owner,
          :is_public,
          :owner_email,
          :owner_about,
          :title,
          :desc,
          :lat,
          :lng,
          :zoom,
          :address_city,
          :address,
          :banner_horizontal,
          :banner_vertical,
          :url_site,
          INET_ATON(:ipv4_add)
        )
        ";
        $sth = $dbi->getConnection()->prepare($query);

        $dataset = [
            "id_owner"      =>  0,
            "is_public"     =>  0,
            "owner_email"   =>  input('club:unauthadd:owner_email'),
            "owner_about"   =>  input('club:unauthadd:owner_about'),
            "title"         =>  input('club:unauthadd:title'),
            "desc"          =>  input('club:unauthadd:description'),
            "zoom"          =>  12,                                     //@todo: фронтэнд-обработка (зум карты меняет значение в поле)
            "address_city"  =>  input('club:unauthadd:address_city'),
            "address"       =>  input('club:unauthadd:address'),
            "banner_horizontal" =>  input('club:unauthadd:vk_banner'),
            "banner_vertical"   =>  input('club:unauthadd:banner_other'),
            "url_site"      =>  input('club:unauthadd:url_site'),
            "ipv4_add"      =>  getIp()
        ];
        if (!(input('club:unauthadd:lat')&&input('club:unauthadd:lng')) && (input('club:unauthadd:latlng'))) {
            // координаты заданы строкой с карты
            // '59.925483, 30.259649'
            // trim, explode by ', '
            $set = explode(', ', trim(input('club:unauthadd:latlng')));
            $dataset['lat'] = $set[0];
            $dataset['lng'] = $set[1];
        } else {
            // координаты заданы полями
            $dataset['lat'] = input('club:unauthadd:lat');
            $dataset['lng'] = input('club:unauthadd:lng');
        }

        if (!$dataset['address_city']) {
            $dataset['address_city'] = getCityByCoords($dataset['lat'], $dataset['lng'])['city'];
        }

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        // сделать отправку письма на почту админу с датасетом

        response()->redirect( url('frontpage') );


    }


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
            'profile'           =>  url('profile_view'),
            'frontpage'         =>  url('frontpage'),
            'clubs_list'        =>  url('clubs_list'),
            'ajax_get_city'     =>  url('ajax_get_city_by_coords'),
            'form_action_submit'=>  url('club_callback_edit', ['id' => $id]),
            'form_action_delete'=>  url('club_callback_delete', ['id' => $id]),
            'form_action_toggle'=>  url('club_callback_toggle', ['id' => $id]),
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
            "id"        =>  input('club:edit:id'),
            "id_owner"  =>  input('club:edit:id_owner'),
            "is_public" =>  input('club:edit:is_public') ? 1 : 0,
            "lat"       =>  input('club:edit:lat'),
            "lng"       =>  input('club:edit:lng'),
            "title"     =>  input('club:edit:title'),
            "desc"      =>  input('club:edit:desc'),
            "address"   =>  input('club:edit:address'),
            "banner_horizontal" =>  input('club:edit:banner_horizontal'),
            "banner_vertical"   =>  input('club:edit:banner_vertical'),
            "url_site"       =>  input('club:edit:url_site'),
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

        response()->redirect( url('clubs_list') );
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

        response()->redirect( url('clubs_list') );
    }

    public function callback_club_visibility_toggle($id)
    {
        $dbi = DBStatic::getInstance();
        $table = $dbi::$_table_prefix . 'clubs';

        $query = "";

        $sth = $dbi->getConnection()->prepare($query);
        $dataset = [
            "id" =>  $id,
        ];

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        // ajax result

    }

}