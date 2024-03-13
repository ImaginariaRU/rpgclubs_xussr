<?php
/**
 * User: Arris
 *
 * Class Exoterical
 * Namespace: Units
 *
 * Date: 09.02.2018, time: 5:39
 */


use ReCaptcha\ReCaptcha;
use RPGCAtlas\Classes\DBStatic;
use RPGCAtlas\Classes\StaticConfig;
use RPGCAtlas\Classes\Template;

class Exoterical
{
    /* ===================================================== */
    /* ============   анонимное добавление данных ========== */
    /* ===================================================== */
    public function form_unauth_add_any_club() {
        $template = new Template('form_unauth_add_any_club.html', '$/templates/clubs');

        $template->set('html/title', "Добавление клуба неавторизованным пользователем");

        $template->set('href', [
            'frontpage'         =>  \RPGCAtlas\Units\url('frontpage'),
            'form_action_submit'=>  \RPGCAtlas\Units\url('club_callback_unauth_add_any_club'),
            'ajax_get_city'     =>  \RPGCAtlas\Units\url('ajax_get_city_by_coords')
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

        $recaptcha_secret = StaticConfig::get('google_recaptcha/secret_key');

        if (StaticConfig::get('google_recaptcha/enable') == 1) {
            // проверяем капчу
            $recaptcha = new ReCaptcha($recaptcha_secret);
            $checkout = $recaptcha->verify(\RPGCAtlas\Units\input('g-recaptcha-response'), getIp());

            // неправильная капча?
            if (!$checkout->isSuccess()) {
                \RPGCAtlas\Units\response()->redirect( \RPGCAtlas\Units\url('club_form_unauthorized_add')); // и как-то надо передать сообщение, что ошибка в капче. КАК?
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
            "owner_email"   =>  \RPGCAtlas\Units\input('club:anonadd:owner_email'),
            "owner_about"   =>  \RPGCAtlas\Units\input('club:anonadd:owner_about'),
            "title"         =>  \RPGCAtlas\Units\input('club:anonadd:title'),
            "desc"          =>  \RPGCAtlas\Units\input('club:anonadd:desc'),
            "zoom"          =>  12,                                     //@todo: фронтэнд-обработка (зум карты меняет значение в поле)
            "address_city"  =>  \RPGCAtlas\Units\input('club:anonadd:address_city'),
            "address"       =>  \RPGCAtlas\Units\input('club:anonadd:address'),
            "banner_horizontal" =>  \RPGCAtlas\Units\input('club:anonadd:banner_horizontal'),
            "banner_vertical"   =>  \RPGCAtlas\Units\input('club:anonadd:banner_vertical'),
            "url_site"      =>  \RPGCAtlas\Units\input('club:anonadd:url_site'),
            "ipv4_add"      =>  getIp()
        ];
        if (!(\RPGCAtlas\Units\input('club:anonadd:lat') && \RPGCAtlas\Units\input('club:anonadd:lng')) && (\RPGCAtlas\Units\input('club:anonadd:latlng'))) {
            // координаты заданы строкой с карты
            // '59.925483, 30.259649'
            // trim, explode by ', '
            $set = explode(', ', trim(input('club:anonadd:latlng')));
            $dataset['lat'] = $set[0];
            $dataset['lng'] = $set[1];
        } else {
            $dataset['lat'] = \RPGCAtlas\Units\input('club:anonadd:lat');
            $dataset['lng'] = \RPGCAtlas\Units\input('club:anonadd:lng');
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

        \RPGCAtlas\Units\response()->redirect( \RPGCAtlas\Units\url('frontpage'));
    }


    /* ===================================================== */
    /* ============   VK Club Add  ========================= */
    /* ===================================================== */
    public function form_unauth_add_vk_club()
    {
        $template = new Template('form_unauth_add_vk_club.html', '$/templates/clubs');

        $template->set('html/title', "Добавление клуба неавторизованным пользователем");

        $template->set('href', [
            'frontpage'             =>  \RPGCAtlas\Units\url('frontpage'),
            'form_action_submit'    =>  \RPGCAtlas\Units\url('club_callback_unauth_add_vk_club'),

            'ajax_get_vk_club_info'     =>  \RPGCAtlas\Units\url('ajax_get_vk_club_info'),
            'ajax_get_city_by_coords'   =>  \RPGCAtlas\Units\url('ajax_get_city_by_coords'),
            'ajax_get_coords_by_address'=>  \RPGCAtlas\Units\url('ajax_get_coords_by_address')
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

        $recaptcha_secret = StaticConfig::get('google_recaptcha/secret_key');

        if (StaticConfig::get('google_recaptcha/enable') == 1) {
            // проверяем капчу
            $recaptcha = new ReCaptcha($recaptcha_secret);
            $checkout = $recaptcha->verify(\RPGCAtlas\Units\input('g-recaptcha-response'), getIp());

            // неправильная капча?
            if (!$checkout->isSuccess()) {
                \RPGCAtlas\Units\response()->redirect( \RPGCAtlas\Units\url('club_form_unauth_add_vk_club')); // и как-то надо передать сообщение, что ошибка в капче. КАК?
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
            "owner_email"   =>  \RPGCAtlas\Units\input('club:unauthadd:owner_email'),
            "owner_about"   =>  \RPGCAtlas\Units\input('club:unauthadd:owner_about'),
            "title"         =>  \RPGCAtlas\Units\input('club:unauthadd:title'),
            "desc"          =>  \RPGCAtlas\Units\input('club:unauthadd:description'),
            "zoom"          =>  12,                                     //@todo: фронтэнд-обработка (зум карты меняет значение в поле)
            "address_city"  =>  \RPGCAtlas\Units\input('club:unauthadd:address_city'),
            "address"       =>  \RPGCAtlas\Units\input('club:unauthadd:address'),
            "banner_horizontal" =>  \RPGCAtlas\Units\input('club:unauthadd:vk_banner'),
            "banner_vertical"   =>  \RPGCAtlas\Units\input('club:unauthadd:banner_other'),
            "url_site"      =>  \RPGCAtlas\Units\input('club:unauthadd:url_site'),
            "ipv4_add"      =>  getIp()
        ];
        if (!(\RPGCAtlas\Units\input('club:unauthadd:lat') && \RPGCAtlas\Units\input('club:unauthadd:lng')) && (\RPGCAtlas\Units\input('club:unauthadd:latlng'))) {
            // координаты заданы строкой с карты
            // '59.925483, 30.259649'
            // trim, explode by ', '
            $set = explode(', ', trim(input('club:unauthadd:latlng')));
            $dataset['lat'] = $set[0];
            $dataset['lng'] = $set[1];
        } else {
            // координаты заданы полями
            $dataset['lat'] = \RPGCAtlas\Units\input('club:unauthadd:lat');
            $dataset['lng'] = \RPGCAtlas\Units\input('club:unauthadd:lng');
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

        \RPGCAtlas\Units\response()->redirect( \RPGCAtlas\Units\url('frontpage'));
    }








}