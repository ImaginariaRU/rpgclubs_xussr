<?php

namespace RPGCAtlas\Controllers;

use Arris\AppRouter;
use Arris\Helpers\Server;
use Psr\Log\LoggerInterface;
use RPGCAtlas\AbstractClass;
use RPGCAtlas\Units\GeoCoder;
use RPGCAtlas\Units\Mailer;

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
    }

    public function callback_club_add()
    {
        $query = "
        INSERT INTO {$this->tables->clubs}
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
        $sth = $this->pdo->prepare($query);

        $dataset = [
            "id_owner"      =>  0,
            "is_public"     =>  0,
            "owner_email"   =>  input('club:owner_email'),
            "owner_about"   =>  input('club:owner_about'),
            "title"         =>  input('club:title'),
            "desc"          =>  input('club:description'),
            "zoom"          =>  12,                                     //@todo: фронтэнд-обработка (зум карты меняет значение в поле)
            "address_city"  =>  input('club:address_city'),
            "address"       =>  input('club:address'),
            "banner_horizontal" =>  input('club:vk_banner'),
            "banner_vertical"   =>  input('club:banner_other'),
            "url_site"      =>  input('club:url_site'),
            "ipv4_add"      =>  Server::getIP()
        ];
        if (!(input('club:lat') && input('club:lng')) && (input('club:latlng'))) {
            // координаты заданы строкой с карты
            // '59.925483, 30.259649'
            // trim, explode by ', '
            $set = explode(', ', trim(input('club:latlng')));
            $dataset['lat'] = $set[0];
            $dataset['lng'] = $set[1];
        } else {
            // координаты заданы полями
            $dataset['lat'] = input('club:lat');
            $dataset['lng'] = input('club:lng');
        }

        if (!$dataset['address_city']) {
            $_r = (new GeoCoder())->getCityByCoords($dataset['lat'], $dataset['lng']);

            $dataset['address_city'] = $_r->__get('city');
        }

        try {
            $sth->execute($dataset);
        } catch (\PDOException $e) {
            dd($e->getMessage()); //@todo: MONOLOG
        }

        (new Mailer())->mailToAdmin("Новый клуб", "Некто с адресом {$dataset['owner_email']} подал заявку на добавление клуба {$dataset['title']}");

        $this->template->setRedirect( AppRouter::getRouter('view.main.page'));
    }


}