;$.fn.escape = function (callback) {
    return this.each(function () {
        $(document).on("keydown", this, function (e) {
            let keycode = ((typeof e.keyCode != 'undefined' && e.keyCode) ? e.keyCode : e.which);
            if (keycode === 27) {
                callback.call(this, e);
            }
        });
    });
};

;(function(user_location, poi_list, map_provider, $){
    let is_infobox_visible = 0;
    let map_provider_data = map_provider;
    let map = null;

    $(function (){
        // GetUserGeolocation();

        map = MapActions.createMap(L, "map", user_location, map_provider_data);

        MapBoxes.addControl_Zoom(L, map, 'bottomright'); // вынести наружу

        MapBoxes.createControl_InfoBox(L, map, "section-infobox", false);
        map.addControl( new L.Control.AddInfoBox() );

        MapBoxes.createControl_About(L, map, "section-about");
        map.addControl( new L.Control.AddAboutBox() );

        MapBoxes.createControl_PoiList(L, map, 'section-poi-list');
        map.addControl( new L.Control.AddPOIList() );

        // MapBoxes.createControl_Compass(L, map, 'section-compass');
        // map.addControl( new L.Control.AddCompass() );

        // создаем слой "группа кластеров/маркеров" и добавляем его на карту
        let layer_ows = L.markerClusterGroup({
            'disableClusteringAtZoom': 16,
            'spiderfyOnMaxZoom': false,
        });
        layer_ows.addTo(map);

        // события мы привяжем ниже
        $.each(poi_list, function (index, data) {
            let fa = MapActions.getFAIconStyle(data);
            let marker = L.marker(new L.latLng( data.location ), {
                title: data.title,
                icon: L.icon.fontAwesome({
                    iconClasses: `fa ${fa.icon}`,
                    markerColor: fa.markerColor,
                    iconColor: fa.iconColor,
                    iconXOffset: fa.iconXOffset,
                    iconYOffset: fa.iconYOffset,
                }),
                data: {
                    id: data.id,
                    value: 'place',
                    lat: data.lat,
                    lng: data.lng
                }
            });
            layer_ows.addLayer(marker);
        });

        // по onClick меняем window.location.hash
        // его изменение ловится ниже
        layer_ows.on('click', function (owner) {
            let data = owner.layer.options.data;

            window.location.hash = "#poi=" + data.id;
        });

        // openPlaceCardByWLH(map, items_list); // ONLOAD-анализ window.location.hash и показ модалки при необходимости
    }).on('click', 'a', function () {
        // все ссылки, ведущие вовне открывать в новой вкладке
        if (($(this).attr('href') != window.location.hostname) && ($(this).attr('target') == '')) {
            $(this).attr('target', '_blank');
        }
    }).on('click', '#actor_compass_button', function () {
        // клик на кнопке КОМПАСА
        //console.log('compass click');
        // actorSetGeoCoods(map);
    }).on('click', '#actor-about-toggle', function() {

        let state = $(this).data('content-is-visible');
        $('#' + $(this).data('content')).toggle();
        $(this).data('content-is-visible', !state);

    }).escape(function() {

        $("#section-infobox").hide();
        history.pushState('', document.title, window.location.pathname + window.location.search);
        // document.title = `${__frontpage_title} ${__frontpage_title_mdash} ${__frontpage_title_sub}`;
        is_infobox_visible = 0;

    }).on('click', '#actor-infobox-close', function() {

        $("#section-infobox").hide();
        history.pushState('', document.title, window.location.pathname + window.location.search);
        is_infobox_visible = 0;
        // document.title = `${__frontpage_title} ${__frontpage_title_mdash} ${__frontpage_title_sub}`;

    }).on('click', '#actor-list-popup', function () {
        let url = $(this).data('actor-url');

        $.colorbox({
            href: url,
            width: '60%',
            height: '60%',
        });

    });

    window.addEventListener(`hashchange`, function () {
        let default_zoom = window.engine_options.zoom.close;

        let wlh_options = MapActions.wlhParseAction();
        let id = -1;

        if (wlh_options.id_region > 0) {
            let id = wlh_options.id_region;
            let poi_record = window.poi_list[ id ];
            let lat = poi_record.lat;
            let lng = poi_record.lng;
            let zoom = poi_record.zoom || window.engine_options.zoom.close;
            let title = poi_record.title || '';

            // document.title = `${window.engine_options.titles.main} ${window.engine_options.titles.mdash} ${title}`;

            if (!is_infobox_visible) {
                is_infobox_visible = id;
                // map.addControl( new L.Control.AddInfoBox() );
            }

            // load POI content
            MapActions.poiShowContent(id);

            // focus
            map.setView([lat, lng], window.engine_options.zoom.close, { animate: true });
        }

    }, false);


}(user_location, poi_list, map_provider, $));