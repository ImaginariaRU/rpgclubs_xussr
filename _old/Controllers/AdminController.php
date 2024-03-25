<?php

namespace _old\Controllers;

use _old\GeoCoder;
use Arris\AppRouter;
use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;
use function RPGCAtlas\Controllers\dd;

class AdminController extends AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
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

        $query = "
        INSERT INTO {$this->tables->poi}
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
            "is_public" =>  input('club:is_public') ? 1 : 0,
            "lat"       =>  input('club:lat'),
            "lng"       =>  input('club:lng'),
            "title"     =>  input('club:title'),
            "desc"      =>  input('club:desc'),
            "address"   =>  input('club:address'),
            "address_city" => input('club:address_city'),
            "banner_horizontal" =>  input('club:banner_horizontal'),
            "banner_vertical"   =>  input('club:banner_vertical'),
            "url_site"       =>  input('club:url_site'),
            "ipv4_add"  =>  Server::getIP()
        ];
        if (!$dataset['address_city']) {
            $dataset['address_city'] = (new GeoCoder())->getCityByCoords($dataset['lat'], $dataset['lng'])['city'];
        }

        dd($dataset);

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        $this->template->setRedirect( AppRouter::getRouter('view.poi.list'));
    }

}