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

;(function(user_location, clubs_list, map_providers, map){
    let is_infobox_present = false;
    let map_provider_data = map_providers;

    let /**
     * Создает в объекте L область для справочного окна (по всему проекту)
     */
    createControl_InfoBox = function () {
        L.Control.InfoBox = L.Control.extend({
            is_content_visible: false,
            options: {
                position: $("#section-infobox").data('leaflet-control-position')
            },
            onAdd: function (map) {
                var div = L.DomUtil.get('section-infobox');
                // L.DomUtil.removeClass(div, 'invisible');  // don't show infobox by default
                L.DomUtil.enableTextSelection();
                L.DomEvent.disableScrollPropagation(div);
                L.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function (map) {
            }
        });
    };

    let /**
     * Создает в объекте L область для информационного окна о клубе
     */
    createControl_AboutBox = function () {
        L.Control.AboutBox = L.Control.extend({
            is_content_visible: false,
            options: {
                position: $("#section-about").data('leaflet-control-position')
            },
            onAdd: function (map) {
                var div = L.DomUtil.get('section-about');
                L.DomUtil.removeClass(div, 'invisible');
                L.DomUtil.enableTextSelection();
                L.DomEvent.disableScrollPropagation(div);
                L.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function (map) {
            }
        });
    };

    let /**
     * Создает в объекте L активную область для кнопки "Список клубов"
     */
    createControl_ButtonActorList = function () {
        L.Control.ButtonActorList = L.Control.extend({
            is_content_visible: true,
            options: {
                position: 'bottomleft'
            },
            onAdd: function (map) {
                var div = L.DomUtil.get('section-actorlistbutton');
                L.DomUtil.removeClass(div, 'invisible');
                L.DomEvent.disableScrollPropagation(div);
                L.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function (map) {
            }
        });
    };

    let load_poi_content = function (id, container) {
        let target = container || "section-infobox-content";
        let $target = $('#' + container);
        let url = '/ajax/get:poi/' + id;

        $target.html('').scrollTop(0);

        $.ajax({
            url: url,
            type: 'GET',
            async: false
        }).done(function (data) {
            $target.html(data).show();
        });

        return {
            type: 'poi',
            id: id
        };
    };

    let __CreateMap = function (target, location) {
        const __LatLngCenter
            = !!(location.city_lat && location.city_lng)
            ? new L.LatLng(location.city_lat, location.city_lng)
            : new L.LatLng(location.ip_lat, location.ip_lng);

        let zoom = location.zoom || 12; // hardcoded 12-th zoom

        let map = L.map(target, {
            maxZoom: 18,
            minZoom: 4,
            zoomControl: false
        });
        map.setView(__LatLngCenter, zoom)
        map.addControl(new L.Control.Zoomslider({position: 'bottomright'}));

        let map_provider = L.tileLayer(map_provider_data.href, {
            maxZoom: +map_provider_data.maxZoom,
            attribution: map_provider_data.attribution
        });

        map_provider.addTo(map);

        return map;
    };

    __GetUserCoordsIPInfo = function() {};

    GetUserGeolocation = function(){
        if (document.location.protocol === 'https' && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                let message = "Latitude: " + position.coords.latitude + " Longitude: " + position.coords.longitude;
            });
        }
    };

    $(function(){
        // GetUserGeolocation();

        let map = __CreateMap("map", user_location);

        createControl_InfoBox();

        createControl_AboutBox();
        map.addControl( new L.Control.AboutBox() );

        createControl_ButtonActorList();
        map.addControl( new L.Control.ButtonActorList() );

        let layer_ows = L.markerClusterGroup();
        layer_ows.addTo(map);

        $.each(clubs_list, function(index, data){
            let marker = L.marker([data.lat, data.lng], {
                icon: L.icon.fontAwesome({
                    iconClasses: 'fa fa-cubes', // cube, cubes, eyes, smile
                    markerColor: '#00a9ce',
                    iconColor: '#FFF',
                    iconXOffset: -4,
                    iconYOffset: -1,
                }),
                id: data.id
            }).on('click', function() {
                if (!is_infobox_present) {
                    is_infobox_present = true;
                    map.addControl( new L.Control.InfoBox() );
                }

                load_poi_content(this.options.id, "section-infobox");

                map.setView([data.lat, data.lng], 14, {animate: true});

            } );
            layer_ows.addLayer(marker);
        });

    }).on('click', '#actor-about-toggle', function() {
        let state = $(this).data('content-is-visible');
        $('#' + $(this).data('content')).toggle();
        $(this).data('content-is-visible', !state);
    }).escape(function(){
        $("#section-infobox").hide();
    }).on('click', '#actor-infobox-close', function(){
        $("#section-infobox").hide();
    }).on('click', '#actor-list-popup', function () {
        let url = $(this).data('actor-url');

        $.colorbox({
            href: url,
            width: '60%',
            height: '60%',
        });

    }).on('click', 'a', function(){
        if (($(this).attr('href') != window.location.hostname) && ($(this).attr('target') == '')) {
            $(this).attr('target', '_blank');
        }
    });

    /*$(document).on('click', 'a', function(){
        if (($(this).attr('href') != window.location.hostname) && ($(this).attr('target') == '')) {
            $(this).attr('target', '_blank');
        }
    });*/

    /*$(document).on('click', '#actor-list-popup', function () {
        $.colorbox({
            url: '/exoterical/list/',
            // html: '',
            width: 800,
            height: 600,
        });
    });*/

}(user_location, clubs_list, map_providers, $));