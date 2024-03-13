<?php

namespace RPGCAtlas\Controllers;

use Arris\AppRouter;
use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;

class PublicFormController extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    /**
     * Рисует форму добавления нового клуба
     *
     * @return void
     */
    public function view_form_poi_add()
    {
        $this->template->setTemplate('public/form_add_club.tpl');

        /*$template->set('options', [
            'captcha_enabled'   =>  StaticConfig::get('google_recaptcha/enabled'),
            'captcha_sitekey'   =>  StaticConfig::get('google_recaptch/site_key')
        ]);*/
    }

    /**
     * Коллбэк формы добавления нового клуба
     *
     * see /var/www/47news/engine/Units/Site/Feedback.php
     */
    public function callback_club_add()
    {
        if ($_REQUEST['captcha'] != $_SESSION['captcha_keystring']) {
            $ERRORS[] = "Вы неправильно ввели надпись с картинки";
        }

        var_dump($ERRORS);
        die;

        $query = "
        INSERT INTO {$this->tables->clubs}
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

        $sth = $this->pdo->prepare($query);

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
            "ipv4_add"  =>  Server::getIP()
        ];
        if (!$dataset['address_city']) {
            $dataset['address_city'] = getCityByCoords($dataset['lat'], $dataset['lng'])['city'];
        }

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        $this->template->setRedirect( AppRouter::getRouter('view.poi.list'));
    }


}