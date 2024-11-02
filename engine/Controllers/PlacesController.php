<?php

namespace RPGCAtlas\Controllers;

use AJUR\FluentPDO\Exception;
use AJUR\FluentPDO\Query;
use Arris\AppRouter;
use Arris\Helpers\Server;
use PDOException;
use Psr\Log\LoggerInterface;
use RPGCAtlas\App;
use RPGCAtlas\Units\GeoCoderDadata;
use RPGCAtlas\Units\Mailer;
use RPGCAtlas\Units\POI;

/**
 * Контроллер про работу с POI как от лица админа, так и нет
 *
 * От лица админа нет капчи и есть больше функций
 * От лица анона есть капча, нет определений координат и
 */
class PlacesController extends \RPGCAtlas\AbstractClass
{
    public function __construct($options = [], LoggerInterface $logger = null)
    {
        parent::__construct($options, $logger);
    }

    public function viewList()
    {
        $dataset = (new POI())->getList(App::$auth->isLoggedIn());

        $this->template->assign('dataset', $dataset);
        $this->template->assign('summary', [
            'poi_total'   =>  count($dataset),
            'poi_visible' =>  count(array_filter($dataset, function($data){ return !!$data['is_public']; }) )
        ]);

        $this->template->setTemplate("places/list.tpl");
    }

    public function formAdd()
    {
        $this->template->setTemplate('places/form_add_poi.tpl');
    }

    public function callbackAdd()
    {
        // check kCaptcha (not for admins)

        $query = new Query(App::$pdo, includeTableAliasColumns: false);
        $geocoder = new GeoCoderDadata();

        $lng = input('lng', 0);
        $lat = input('lat', 0);
        $latlng = input('latlng');
        $address = input('address');

        // координаты заданы в строке, но не заданы в полях
        if ((empty($lat) || empty($lng)) && !empty($latlng)) {
            $match = \Spatie\Regex\Regex::match("/(?'lat'[\d.]+),\s?(?'lng'[\d.]+)/", $latlng);
            if ($match->hasMatch()) {
                $lat = $match->namedGroup('lat');
                $lng = $match->namedGroup('lng');
            }
        }

        // определение координат и так далее выполняется только под админом
        if (App::$auth->isLoggedIn()) {
            // сначала вытащим координаты

            if (!empty($address)) {
                $result = $geocoder->getCoordsByAddress($address);

                if ($result->is_success) {
                    $lat = $result->lat;
                    $lng = $result->lng;
                    $address_city = $result->city;
                }
            }

        }

        $dataset = [
            "id_owner"      =>  config('auth.id'),
            "is_public"     =>  0, // для админов 1 или отдельно интерфейс подтверждения валидности?
            'lat'           =>  $lat,
            'lng'           =>  $lng,
            "zoom"          =>  12,                                     //@todo: фронтэнд-обработка (зум карты меняет значение в поле)
            "title"         =>  input('title'),
            "description"   =>  input('description'),
            "address"       =>  $address,
            "address_hint"  =>  input('address_hint'),
            "address_city"  =>  $address_city,
            "banner_url"    =>  input('vk_banner'),
            "url_site"      =>  input('url_site'),

            "poi_type"      =>  'club',

            "owner_email"   =>  input('owner_email'),
            "owner_about"   =>  input('owner_about'),

            "ipv4_add"      =>  ip2long(Server::getIP()),
        ];

        try {
            $query = $query
                ->insertInto($this->tables->poi)
                ->values($dataset);

            $query->execute();

        } catch (Exception $e) {
            dd($e);
        }

        (new Mailer())->mailToAdmin("Новый клуб", "Некто с адресом {$dataset['owner_email']} подал заявку на добавление клуба {$dataset['title']}");

        $target
            = App::$auth->isLoggedIn()
            ? AppRouter::getRouter('view.poi.list')
            : AppRouter::getRouter('view.main.page');

        $this->template->setRedirect( $target );
    }

    public function formEdit($id)
    {
        $item = (new POI())->getItem($id);

        if (empty($item['email'])) {
            $item['email'] = App::$auth->getEmail();
        }

        $this->template->assign('item', $item);

        $this->template->setTemplate('places/form_edit_poi.tpl');
    }

    public function callbackUpdate()
    {
        $query = new Query(App::$pdo, includeTableAliasColumns: false);

        $dataset = [
            "id_owner"      =>  config('auth.id'),
            "is_public"     =>  input('is_public') == 'Y' ? 1: 0,
            'lat'           =>  input('lat'),
            'lng'           =>  input('lng'),
            "zoom"          =>  12,
            "title"         =>  input('title'),
            "description"   =>  input('description'),
            "address"       =>  input('address'),
            "address_hint"  =>  input('address_hint'),
            "address_city"  =>  input('address_city'),
            "banner_url"    =>  input('vk_banner'),
            "url_site"      =>  input('url_site'),

            "poi_type"      =>  input('poi_type'),

            "owner_email"   =>  input('owner_email'),
            "owner_about"   =>  input('owner_about'),

            "ipv4_update"      =>  ip2long(Server::getIP()),
        ];

        try {
            $query = $query
                ->update(
                    $this->tables->poi,
                    $dataset,
                    primaryKey: input('id'));

            $query->execute();

        } catch (PDOException|Exception $e) {
            d($dataset);
            dd($e);
        }

        $target
            = App::$auth->isLoggedIn()
            ? AppRouter::getRouter('view.poi.list')
            : AppRouter::getRouter('view.main.page');

        $this->template->setRedirect( $target );
    }

    public function callbackDelete($id)
    {
        $query = new Query(App::$pdo, includeTableAliasColumns: false);

        try {
            $query->delete($this->tables->poi, $id)->execute();
        } catch (Exception $e) {
            dd($e);
        }

        $this->template->setTemplate("places/form_deleted_poi.tpl");
    }

}