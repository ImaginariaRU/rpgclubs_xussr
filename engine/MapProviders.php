<?php

namespace RPGCAtlas;

class MapProviders
{
    const PROVIDERS = [
        'OpenStreetMap_Mapnik' => [
            'href'      =>  'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'attr'      =>  '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>'
        ],
        'DoubleGIS'         => [
            'href'      =>  'https://tile2.maps.2gis.com/tiles?x={x}&y={y}&z={z}&v=46',
            'attr'      =>  '<a href="https://2gis.ru/">GIS</a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>',
        ],
        'OpenStreetMaps'    =>  [
            'href'      =>  'http://{s}.tile.osm.org/{z}/{x}/{y}.png',
            'attr'      =>  '&copy;<a href="https://osm.org/copyright">OpenStreetMap</a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>',
        ],
        'MapBox'            =>  [
            'href'      =>  'https://api.mapbox.com/styles/v1/mapbox/streets-v9/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoia2FyZWx3aW50ZXJza3kiLCJhIjoiY2psbThkMHVlMTF3bTNxcnp1MGNqZmpwYSJ9.yeQjE--UG82dwU2u5svK3w',
            'attr'      =>  '&copy;<a href="https://www.mapbox.com/">Map Box</a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>'
        ],

    ];

}