class MapActions {

    /**
     * Open infobox content
     *
     * @param id
     * @param target
     */
    static poiShowContentInfobox(id, target = "section-infobox") {
        let $target = $(`#${target}`);
        let url = window.urls['poi.get'] + id;

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            async: false
        }).done(function (data) {
            $target
                .hide()
                .html('')
                .scrollTop(0)
                .html(data)
                .show();
        });
    }

    /**
     * Open colorbox
     * @param id
     */
    static poiShowContentColorbox(id) {
        let url = `${window.urls['poi.get']}${id}?mode=colorbox`;

        $.colorbox({
            href: url,
            width: '60%',
            height: '60%',
        });
    }

    static getUserGeoLocation() {
        if (document.location.protocol === 'https' && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position){
                let message = `Latitude: ${position.coords.latitude} Longitude: ${position.coords.longitude}`;
            });
        }
    }

    /**
     * Создает карту
     *
     * @param LEAFLET
     * @param target
     * @param user_location
     * @param map_provider_data
     * @returns {*}
     */
    static createMap(LEAFLET, target, user_location, map_provider_data) {
        const __LatLngCenter
            = !!(user_location.city_lat && user_location.city_lng)
            ? new LEAFLET.LatLng(user_location.city_lat, user_location.city_lng)
            : new LEAFLET.LatLng(user_location.lat, user_location.lng);

        let zoom = user_location.zoom || window.engine_options.zoom.default;

        let map = LEAFLET.map(target, {
            maxZoom: 18,
            minZoom: 4,
            zoomControl: false
        });

        // вместо этого должно быть
        // MapActions.setViewMap(LEAFLET, map, location);
        map.setView(__LatLngCenter, zoom);

        let map_provider = L.tileLayer(map_provider_data.href, {
            maxZoom: +map_provider_data.maxZoom,
            attribution: map_provider_data.attribution
        });
        map_provider.addTo(map);

        return map;
    }

    /**
     * Устанавливает точку видимости карты
     *
     * @param LEAFLET
     * @param map
     * @param location
     */
    static setViewMap(LEAFLET, map, location) {
        let _LatLngCenter = new LEAFLET.LatLng(location.lat, location.lng);
        map.setView(_LatLngCenter, location.zoom);
        console.log('Leaflet_SetView : ', location.state);
    }

    /**
     * По типу объекта определяет иконку
     *
     * @param data
     * @returns {{iconXOffset: number, markerColor: string, icon: string, iconColor: string, iconYOffset: number}}
     */
    static getFAIconStyle = function (data) {
        let style = {};
        data.type = 'any';
        switch (data.type) {
            default: {
                style = {
                    icon: 'fa-cubes',
                    markerColor: '#00a9ce',
                    iconColor: '#FFF',
                    iconXOffset: -4,
                    iconYOffset: -1
                };
            }
        }

        return style;
    };

    /**
     * Определяет тип события, заданного в location.hash
     * В данный момент поддерживается одно событие `#place=N`
     *
     *
     * @returns {{action: string, id_region: string}}
     */
    static wlhParseAction() {
        let wlh = window.location.hash;
        let wlh_params = wlh.match(/(poi)=(.*)/);
        let options = {
            id_region: -1
        };

        if (
            ((wlh.length > 1) && (wlh_params !== null))
            &&
            ((wlh_params[1] == 'poi') && (wlh_params[2] != ''))
        ) {
            options.action = wlh_params[1]; // place
            options.id_region = wlh_params[2]; // 17
        }

        return options;
    }

    static wlhParseCoords() {
        let wlh = window.location.hash;
        let wlh_params = wlh.match(/(coords)=(.*),(.*),(.*)/);
        let data = {
            lat: 0,
            lng: 0,
            zoom: 0
        };

        if (
            ((wlh.length > 1) && (wlh_params !== null))
            &&
            ((wlh_params[1] == 'coords') && (wlh_params[2] != '') && (wlh_params[3] != ''))
        ) {
            data = {
                lat: wlh_params[2],
                lng: wlh_params[3],
                zoom: wlh_params[4] || 14
            };
        }
        return data;
    }


    /**
     *
     * @param params
     * @returns {{}}
     */
    static parseQueryParams(params) {
        if (params === "") return {};
        let parsed = {};
        for (let i = 0; i < params.length; ++i)
        {
            let p = params[i].split('=', 2);
            parsed[p[0]]
                = (p.length === 1)
                ? parsed[p[0]] = ""
                : decodeURIComponent(p[1].replace(/\+/g, " "));
        }
        return parsed;
    }

    static wlhClear() {
        if ('pushState' in history) {
            window.history.pushState('', window.title, window.location.pathname + window.location.search);
        } else {
            window.location.hash = '';
        }
    }


}