$.fn.escape = function (callback) {
    return this.each(function () {
        $(document).on("keydown", this, function (e) {
            var keycode = ((typeof e.keyCode !='undefined' && e.keyCode) ? e.keyCode : e.which);
            if (keycode === 27) {
                callback.call(this, e);
            };
        });
    });
};

/**
 * Создает в объекте L Control элемент: информация о регионе
 */
createControl_InfoBox = function(){
    L.Control.InfoBox = L.Control.extend({
        is_content_visible: false,
        options: {
            position: $("#section-infobox").data('leaflet-control-position')
        },
        onAdd: function(map) {
            var div = L.DomUtil.get('section-infobox');
            L.DomUtil.removeClass(div, 'invisible');
            L.DomUtil.enableTextSelection();
            L.DomEvent.disableScrollPropagation(div);
            L.DomEvent.disableClickPropagation(div);
            return div;
        },
        onRemove: function(map) {}
    });
};

/**
 * Создает в объекте L Control-элемент: список регионов
 */
createControl_AboutBox = function() {
    L.Control.AboutBox = L.Control.extend({
        is_content_visible: false,
        options: {
            position: $("#section-about").data('leaflet-control-position')
        },
        onAdd: function(map) {
            var div = L.DomUtil.get('section-about');
            L.DomUtil.removeClass(div, 'invisible');
            L.DomUtil.enableTextSelection();
            L.DomEvent.disableScrollPropagation(div);
            L.DomEvent.disableClickPropagation(div);
            return div;
        },
        onRemove: function(map) {}
    });
};


/**
 *
 * @param id
 * @param container
 * @returns {{type: string, id: *}}
 */
var load_poi_content = function(id, container) {
    let target = container || "section-infobox-content";
    let $target = $('#' + container);
    let url = '/ajax/poi/' + id;

    $target.html('').scrollTop(0);

    $.ajax({
        url: url,
        type: 'GET',
        async: false
    }).done(function(data){
        $target.html(data).show();
    });

    return {
        type: 'poi',
        id:   id
    };
}

var __CreateMap = function(target, location, zoom) {
    var __LatLngCenter
        = !!(location.city_lat && location.city_lng)
        ? new L.LatLng(location.city_lat, location.city_lng)
        : new L.LatLng(location.ip_lat, location.ip_lng);

    // var __LatLngCenter = new L.LatLng(center.lat, center.lng);
    let map = L.map(target, {
        // renderer: L.canvas(),
        // zoom: 16,
        maxZoom: 18,
        minZoom: 4,
        zoomControl: false
    });
    map.setView(__LatLngCenter, zoom)
    map.addControl(new L.Control.Zoomslider({position: 'bottomright'}));

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    return map;
}

var __GetUserCoords = function() {
    $.ajax({
        async: false,
        cache: false,
        type: 'GET',
        url: "https://ipinfo.io/json",
        dataType: 'jsonp',
        success: function(response) {
            let c = response.loc.split(',', 2);
            let r = {
                lat: c[0],
                lng: c[1]
            };
            console.log('__GetUserCoords', r);
            return r;
        },
        error: function(response) {
            return {
                lat: 59.939031,
                lng: 30.315893
            };
        }
    });
}
