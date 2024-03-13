<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ролевые клубы на карте мира</title>

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta name="keywords" content="map, rpg, clubs, ролевые клубы, карта, настольные, ролевые, игры, поиграть, НРИ, антикафе, игротеки"/>
    <meta name="description" content="Ролевые клубы на карте России и ближайшего зарубежья"/>
    <meta name="subject" content="Ролевые клубы на карте России и ближайшего зарубежья" />
    <meta name="copyright" content="Karel Wintersky, 2018-2044" />
    <meta name="language" content="RU">
    <meta name="robots" content="index,follow" />
    <meta name="revised" content="March 10th, 2024" />
    <meta name="author" content="Karel Wintersky, rpgclubsrf@yandex.ru">
    <meta name="url" content="">

    {* always remote *}
    <link href="https://fonts.googleapis.com/css?family=PT+Serif" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" rel="stylesheet" />

    <script type="text/javascript" id="data">
        var user_location = {
            ip_lat: '{$location.ip_lat}',
            ip_lng: '{$location.ip_lng}',
            zoom: 4,
            city: '{$location.city}',
            city_lat: {$location.city_lat},
            city_lng: {$location.city_lng}
        };
        var clubs_list = [
            {foreach $dataset_clubs_list as $club} {
                id: {$club.id},
                lat: {$club.lat},
                lng: {$club.lng}
            },
            {/foreach}
        ];

        var map_provider = {
            "use": "{$map_provider.use}",
            "href" : '{$map_provider.href}',
            "attribution": '{$map_provider.attribution}',
            "max_zoom" : "{$map_provider.zoom}",
        };
        {* @todo: через getRouter() *}
        var urls = {
            'poi_get': '{Arris\AppRouter::getRouter('ajax.view.poi.info')}',
            'poi_list': '{Arris\AppRouter::getRouter('ajax.view.poi.list')}'
        };
    </script>

    {if getenv('ENV_STATE') == 'dev'}
        <script src="/frontend/jquery/jquery.min.js" type="text/javascript"></script>
        <script src="/frontend/leaflet/leaflet.js" type="text/javascript"></script>
        <link href="/frontend/leaflet/leaflet.css" rel="stylesheet" />

        <script src="/frontend/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>
        <link href="/frontend/colorbox/colorbox.css" rel="stylesheet">

        <!-- danwild/leaflet-fa-markers -->
        <script src="/frontend/leaflet/L.Icon.FontAwesome.js" type="text/javascript"></script>
        <link href="/frontend/leaflet/L.Icon.FontAwesome.css" rel="stylesheet" />

        <!-- marker clusters -->
        <link href="/frontend/leaflet/MarkerCluster.css" rel="stylesheet" />
        <link href="/frontend/leaflet/MarkerCluster.Default.css" rel="stylesheet" />
        <script src="/frontend/leaflet/leaflet.markercluster.js" type="text/javascript"></script>

        <!-- zoom slider -->
        <script src="/frontend/leaflet/L.Control.Zoomslider.js" type="text/javascript"></script>
        <link href="/frontend/leaflet/L.Control.Zoomslider.css" rel="stylesheet" />

        <script src="/frontend/front.js" type="text/javascript"></script>
        <link href="/frontend/front.css" rel="stylesheet" />
    {else}
        <link href="/styles.css" rel="stylesheet" />
        <script src="/scripts.js" type="text/javascript" ></script>
    {/if}



</head>
<body>
<div tabindex="0" class="leaflet-container leaflet-fade-anim leaflet-grab leaflet-touch-drag" id="map"></div>

<section id="section-actorlistbutton" class="section-actorlistbutton-wrapper invisible" data-leaflet-control-position="bottomleft">
    <span id="section-act"></span>
    <button id="actor-list-popup" data-actor-url="{Arris\AppRouter::getRouter('ajax.view.poi.list')}">Клубы на карте</button>
</section>

<section id="section-infobox" class="section-infobox-wrapper invisible" data-leaflet-control-position="{$section.infobox_position}"></section>

<section id="section-about" class="section-about-wrapper invisible" data-leaflet-control-position="{$section.about_position}">
    <div style="text-align: right">
        <button id="actor-about-toggle" class="section-about-button-toggle" data-content="section-about-content" data-content-is-visible="false">?</button>
    </div>
    <div id="section-about-content" class="invisible section-about-content">
        <h2>Ролевые клубы на карте России</h2>
        <div>
            <strong>Здравствуйте!</strong><br/><br/>
            На этой карте можно найти <a href="{Arris\AppRouter::getRouter('view.poi.list')}">ролевые клубы</a> России. Нажав на иконку на карте, вы узнаете, где этот клуб находится и
            что предлагают его создатели. Ссылка на сайт клуба или группу ВКонтакте прилагается.
            <br/><br/>
            Это совершенно некоммерческий проект и создатель не извлекает из него абсолютно никакой прибыли.
        </div>
        <div>
            Если вы хотите добавить клуб на карту, <a href="{$href.unauth_add_vk_club}" target="_self">подайте заявку через форму</a> или напишите
            сюда: <a href="mailto:rpgclubsrf@yandex.ru">rpgclubsrf@yandex.ru</a>.
            Жалобы, комментарии и предложения тоже отправляйте, пожалуйста, почтой.
        </div>
        {if $publish_options.allow_donate}
            <hr>
            <div>
                Если вы хотите помочь в поддержке и развитии атласа:<br>
                <iframe frameborder="0" allowtransparency="true" scrolling="no" src="https://money.yandex.ru/embed/donate.xml?account=41001445086806&quickpay=donate&payment-type-choice=on&mobile-payment-type-choice=on&default-sum=100&targets=%D0%9D%D0%B0+%D1%80%D0%B0%D0%B7%D0%B2%D0%B8%D1%82%D0%B8%D0%B5+%D0%BF%D1%80%D0%BE%D0%B5%D0%BA%D1%82%D0%B0+%D0%B0%D1%82%D0%BB%D0%B0%D1%81%D0%B0+%22%D0%A0%D0%BE%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5+%D0%BA%D0%BB%D1%83%D0%B1%D1%8B+%D0%BD%D0%B0+%D0%BA%D0%B0%D1%80%D1%82%D0%B5+%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D0%B8%22&target-visibility=on&project-name=%D0%90%D1%82%D0%BB%D0%B0%D1%81%3A+%D0%A0%D0%BE%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5+%D0%BA%D0%BB%D1%83%D0%B1%D1%8B+%D0%BD%D0%B0+%D0%BA%D0%B0%D1%80%D1%82%D0%B5+%D0%A0%D0%BE%D1%81%D1%81%D0%B8%D0%B8&project-site=http%3A%2F%2F%D1%80%D0%BE%D0%BB%D0%B5%D0%B2%D1%8B%D0%B5%D0%BA%D0%BB%D1%83%D0%B1%D1%8B.%D1%80%D1%84&button-text=05&successURL=" width="508" height="120"></iframe>
            </div>
        {/if}
        <span style="font-size: small">
            <a href="{$href.admin_clubs_list}" style="text-decoration: none; color: black;">©</a> Копирайты: Leaflet, OpenSteetMaps, Yandex Geocoder, ORDI
        </span>
    </div>
</section>



</body>
</html>