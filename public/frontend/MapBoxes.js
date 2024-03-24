/**
 *
 */
class MapBoxes {
    constructor(options) {
        this.options = options;
    }

    /**
     * Создает в объекте Leaflet область для справочного окна
     *
     * @param LEAFLET
     * @param map
     * @param container
     * @param is_visible true
     */
    static createControl_InfoBox(LEAFLET, map, container = "section-infobox", is_visible = false) {
        LEAFLET.Control.AddInfoBox = LEAFLET.Control.extend({
            is_content_visible: is_visible,
            options: {
                position: $(`#${container}`).data('leaflet-control-position')
            },
            onAdd: function(map) {
                let div = LEAFLET.DomUtil.get(container);
                LEAFLET.DomUtil.enableTextSelection();
                LEAFLET.DomEvent.disableScrollPropagation(div);
                LEAFLET.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function(map) {}
        });
    }

    /**
     * Добавляет к объекту LEAFLET конструктор окна ABOUT (!!! это правильное описание)
     *
     * @param LEAFLET
     * @param map
     * @param container
     * @param is_visible
     */
    static createControl_About(LEAFLET, map, container = "section-about", is_visible = false) {
        LEAFLET.Control.AddAboutBox = LEAFLET.Control.extend({
            is_content_visible: is_visible,
            options: {
                position: $(`#${container}`).data('leaflet-control-position')
            },
            onAdd: function(map) {
                let div = LEAFLET.DomUtil.get(container);
                LEAFLET.DomUtil.removeClass(div, 'invisible');
                LEAFLET.DomUtil.enableTextSelection();
                LEAFLET.DomEvent.disableScrollPropagation(div);
                LEAFLET.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function(map) {}
        });
    }

    /**
     * Создает в объекте L активную область для кнопки "Список мест" 
     * (не используется в AJURMAP)
     * 
     *
     * Возможно, позиция по-умолчанию 'bottomleft'
     *
     * @param LEAFLET
     * @param map
     * @param container
     * @param is_public
     */
    static createControl_PoiList(LEAFLET, map, container = 'section-actorlistbutton', is_public = true) {
        L.Control.AddPOIList = L.Control.extend({
            is_content_visible: is_public,
            options: {
                position: $(`#${container}`).data('leaflet-control-position')
            },
            onAdd: function(map) {
                let div = L.DomUtil.get(container);
                L.DomUtil.removeClass(div, 'invisible');
                L.DomEvent.disableScrollPropagation(div);
                L.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function(map){}
        });    
    }

    /**
     * Создает в объекте L активную область для кнопки "Добавить (фирму на карту)"
     * Используется в AJURMAP
     * 
     * @param LEAFLET
     * @param map
     * @param container
     * @param is_public
     */
    static createControl_AddFormBox(LEAFLET, map, container = 'section-addform', is_public = false) {
        LEAFLET.Control.AddFormBox = LEAFLET.Control.extend({
            is_content_visible: is_public,
            options: {
                position: $(`#${container}`).data('leaflet-control-position')
            },
            onAdd: function(map) {
                let div = LEAFLET.DomUtil.get(container);
                LEAFLET.DomUtil.removeClass(div, 'invisible');
                LEAFLET.DomUtil.enableTextSelection();
                LEAFLET.DomEvent.disableScrollPropagation(div);
                LEAFLET.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function(map) {}
        });
    };

    /**
     * Создает в объекте L активную область для кнопки "Поиск"
     * Используется в AJURMAP
     * 
     * @param LEAFLET
     * @param map
     * @param container
     * @param is_public
     */
    static createControl_AddSearchBox(LEAFLET, map, container = 'section-search', is_public = false) {
        LEAFLET.Control.AddSearchBox = LEAFLET.Control.extend({
            is_content_visible: is_public,
            options: {
                position: $(`#${container}`).data('leaflet-control-position')
            },
            onAdd: function(map) {
                let div = LEAFLET.DomUtil.get(container);
                LEAFLET.DomUtil.removeClass(div, 'invisible');
                LEAFLET.DomUtil.enableTextSelection();
                LEAFLET.DomEvent.disableScrollPropagation(div);
                LEAFLET.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function(map) {}
        });
    }

    /**
     * Создает в объекте L активную область для компаса
     *
     * @param LEAFLET
     * @param map
     * @param container
     * @param is_public
     */
    static createControl_Compass(LEAFLET, map, container = 'section-compass', is_public = false) {
        LEAFLET.Control.AddCompass = LEAFLET.Control.extend({
            is_content_visible: is_public,
            options: {
                position: $(`#${container}`).data('leaflet-control-position')
            },
            onAdd: function (map) {
                let div = LEAFLET.DomUtil.get(container);
                LEAFLET.DomUtil.removeClass(div, 'invisible');
                LEAFLET.DomUtil.enableTextSelection();
                LEAFLET.DomEvent.disableScrollPropagation(div);
                LEAFLET.DomEvent.disableClickPropagation(div);
                return div;
            },
            onRemove: function (map) { }
        });
    }

    /**
     * Создает контроллер управления ZOOM
     *
     * @param LEAFLET
     * @param map
     * @param position
     * @param is_visible
     * @returns {boolean}
     */
    static addControl_Zoom(LEAFLET, map, position = 'bottomright', is_visible = true) {
        if (is_visible) {
            map.addControl(new LEAFLET.Control.Zoomslider({
                position: position
            }));
        }
        return true;
    }

}