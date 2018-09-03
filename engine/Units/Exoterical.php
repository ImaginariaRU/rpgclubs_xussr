<?php
/**
 * User: Arris
 *
 * Class Exoterical
 * Namespace: Units
 *
 * Date: 09.02.2018, time: 5:39
 */

namespace RPGCAtlas\Units;

use ReCaptcha\ReCaptcha;
use RPGCAtlas\Classes\StaticConfig;
use RPGCAtlas\Classes\Template;
use RPGCAtlas\Classes\DBStatic;

class Exoterical
{
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

        doSendMail( StaticConfig::get('emails/club_add'), "Новый клуб", "Некто с адресом {$dataset['owner_email']} подал заявку на добавление клуба {$dataset['title']}");

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

        doSendMail( StaticConfig::get('emails/club_add'), "Новый клуб", "Некто с адресом {$dataset['owner_email']} подал заявку на добавление клуба {$dataset['title']}");

        response()->redirect( url('frontpage') );
    }




    public function public_clubs_list()
    {
        $template = new Template('public_view_list.html', '$/templates/clubs');

        $dbi = DBStatic::getInstance();

        $table = $dbi::$_table_prefix . 'clubs';

        $query = "SELECT * FROM {$table} WHERE `is_public` = 1 ORDER BY `is_public` DESC, `address_city`, `title` ";

        $dataset = [];
        foreach ($dbi->getConnection()->query($query)->fetchAll() as $row) {
            $data = $row;
            $data['coords'] = "{$row['lat']} / {$row['lng']}";
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
            'frontpage'     =>  url('frontpage')
        ]);

        return preg_replace('/^\h*\v+/m', '', $template->render());

    }

    public function public_clubs_list_colorbox()
    {
        $template = new Template('public_view_list_colorbox.html', '$/templates/clubs');

        $dbi = DBStatic::getInstance();

        $table = $dbi::$_table_prefix . 'clubs';

        $query = "SELECT * FROM {$table} WHERE `is_public` = 1 ORDER BY `is_public` DESC, `address_city`, `title` ";

        $dataset = [];
        foreach ($dbi->getConnection()->query($query)->fetchAll() as $row) {
            $data = $row;
            $data['coords'] = "{$row['lat']} / {$row['lng']}";
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
            'frontpage'     =>  url('frontpage')
        ]);

        return preg_replace('/^\h*\v+/m', '', $template->render());

    }



}