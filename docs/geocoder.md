https://gist.github.com/nalgeon/79a7609bf24bc0e833699f7eca125e86

https://dadata.ru/pricing/

https://dadata.ru/api/geocode/  - 15 копеек за запрос

---

https://geocode.maps.co/

https://positionstack.com/product

https://github.com/filippotoso/positionstack/tree/master

Nominatim

https://github.com/osm-search/Nominatim
https://nominatim.org/

https://nominatim.org/release-docs/develop/api/Reverse/
https://nominatim.org/release-docs/develop/api/Search/



----------

# Nominatim

только адрес <==> координаты, город узнать нельзя, это ограничение всех пакетов Nominatim

https://packagist.org/packages/maxh/php-nominatim -- использует guzzle
https://packagist.org/packages/riverside/php-nominatim -- этот код мне нравится больше, нет зависимостей

их API:
https://nominatim.org/release-docs/develop/api/Reverse/ (адрес по координатам)
https://nominatim.org/release-docs/develop/api/Search/ 

https://nominatim.openstreetmap.org/search?q=%D0%9D%D0%B0%D0%B1%D0%B5%D1%80%D0%B5%D0%B6%D0%BD%D0%B0%D1%8F+%D0%BA%D1%83%D1%82%D1%83%D0%B7%D0%BE%D0%B2%D0%B0+18&format=json&addressdetails=1


```php
// работает только для пакета riverside/php-nominatim 

$response = $client->search('Набережная кутузова, 18, Санкт-Петербург, Россия', [
    'addressdetails'    =>  1
]);
```
Отдает инфу об адресе и городе, но латиницей. 

Похоже, информацию о российских городах надо брать с ДАДАТЫ



Вполне себе отдает инфу о городе

