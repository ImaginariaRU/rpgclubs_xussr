# ролевыеклубы.рф

## Сборка

```
npm install
npm install gulp
make update 
make build
```

# Map providers

[OpenStreetMap_Mapnik]
href = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
max_zoom = 19
attribution = '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>'

[OpenStreetMaps]
href = 'http://{s}.tile.osm.org/{z}/{x}/{y}.png'
attribution = '&copy;<a href="https://osm.org/copyright">OpenStreetMap</a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>'

[MapBox]
href = 'https://api.mapbox.com/styles/v1/mapbox/streets-v9/tiles/{z}/{x}/{y}?access_token=pk.eyJ1Ijoia2FyZWx3aW50ZXJza3kiLCJhIjoiY2psbThkMHVlMTF3bTNxcnp1MGNqZmpwYSJ9.yeQjE--UG82dwU2u5svK3w'
attribution = '&copy;<a href="https://www.mapbox.com/">Map Box</a>a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>'

[DoubleGIS]
href = 'https://tile2.maps.2gis.com/tiles?x={x}&y={y}&z={z}&v=46'
attribution = '&copy;<a href="https://2gis.ru/">GIS</a>a>, Geotargeting: <a href="https://yandex.ru">Yandex</a>'
