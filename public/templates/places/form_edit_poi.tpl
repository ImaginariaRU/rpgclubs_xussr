<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Редактирование точки интереса</title>
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" />
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
    <script src="/frontend/jquery/jquery.jgrowl.min.js" type="text/javascript"></script>
    <link   href="/frontend/jquery/jquery.jgrowl.min.css" rel="stylesheet" />
    <script src="/frontend/helper.notifyBar.js"></script>
    <script src="/frontend/helper.dataActionRedirect.js"></script>

    <style>
        input[required] {
            background-image: radial-gradient(#F00 15%, transparent 16%), radial-gradient(#F00 15%, transparent 16%);
            background-size: 1em 1em;
            background-position: right top;
            background-repeat: no-repeat;
        }
        button {
            font-size: large;
        }
        body {
            font-family: 'PT Sans', sans-serif;
        }
        textarea {
            resize: vertical;
        }
        .g-recaptcha.error {
            border: solid 2px #c64848;
            padding: .2em;
            width: 19em;
        }
        .jGrowl .error {
            background-color: #FFF1C2;
            color: red;
            font-size: large;
        }
        .jGrowl .success {
            background-color: 		#FFF1C2;
            color: 					navy;
            font-size: large;
        }
        .invisible {
            display: none;
        }

        .flex-container {
            display: flex;
            justify-content: space-between; /* Устанавливает равномерное расстояние между кнопками */
            padding: 10px; /* Отступ от краев контейнера */
            margin: 0 auto; /* Центрирует контейнер, если его ширина меньше 100% */
            max-width: 800px; /* Максимальная ширина контейнера */
        }
        .flex-button {
            flex: 1; /* Задает кнопкам равные размеры */
            margin: 0 15px; /* Отступ между кнопками */
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<h2>Редактирование клуба</h2>
<form method="post" action="{Arris\AppRouter::getRouter('callback.edit.poi')}" id="form_add_poi">
    <input type="hidden" name="id" value="{$item.id}">
    <table border="0" width="100%">
        <tr>
            <td>
                Об отправителе
            </td>
            <td>
                <label>
                    E-Mail:<br>
                    <input type="email" size="80" name="owner_email" value="{$item.email|escape}" required><br><br>
                </label>
                <label>
                    Информация:<br>
                    <input type="text" size="80" name="owner_about" value="{$item.owner_about}"><br><br>
                </label>

            </td>
        </tr>

        <tr>
            <td valign="top">
                URL сайта/группы клуба
            </td>
            <td>
                <label>
                    <input type="text" value="{$item.url_site}" size="80" name="url_site">
                </label> <br><br>
                <button id="actor-resolve-vk-data" data-url="{Arris\AppRouter::getRouter('ajax.get_vk_club_info')}" data-source="url_site">Попробовать извлечь информацию о клубе из VKontakte</button>
                <br><br>
            </td>
        </tr>

        <tr>
            <td valign="top">Название клуба</td>
            <td>
                <label>
                    <input type="text" value="{$item.title|escape}" size="80" name="title" required><br><br>
                </label>
            </td>
        </tr>

        <tr>
            <td valign="top">Описание клуба</td>
            <td>
                <label for="textarea_club_description"></label><textarea cols="70" rows="7" name="description" id="textarea_club_description" required>{$item.description}</textarea>
                <br><br>
            </td>
        </tr>
        <tr>
            <td valign="top">
                Контакты:
            </td>
            <td>
                - емейл, телефон, телеграм, дискорд, VK, сайт
            </td>
        </tr>

        <tr>
            <td valign="top">
                Координаты:
            </td>
            <td>
                <label>
                    <strong>Адрес:</strong><br>
                    <input type="text" value="{$item.address|escape}" size="70" name="address"><br>
                </label>
                <br>
                <button id="actor-parse-address" data-url="{Arris\AppRouter::getRouter('ajax.get_coords_by_address')}">Попытаться определить координаты (и город) по адресу</button>

                <br><br>
                <label>
                    Lat:
                    <input type="text" value="{$item.lat|escape}" size="20" name="lat">
                </label>
                <label>
                    Lng:
                    <input type="text" value="{$item.lng|escape}" size="20" name="lng">
                </label>
                <label>
                    <strong>Город:</strong>
                    <input type="text" value="{$item.address_city|escape}" size="40" name="address_city">
                </label>

                <br><br>

                <label>
                    Особенности адреса: <br>
                    <textarea cols="70" rows="7" name="address_hint">{$item.address_hint|escape}</textarea>
                </label>
                <br>

                <h4>Указать координаты вручную?</h4>

                <label>
                    Найдите свой клуб, кликните на здание с клубом, а потом скопируйте координаты в это поле:<br>
                    <input type="text" size="20" value="{$item.lat}, {$item.lng}" name="latlng">
                </label>
                <br><br>
                <small>
                        Где найти координаты? Например, на <a href="https://yandex.ru/maps/" target="_blank">яндекс-карте</a> (откроется в новой вкладке).<br>
                        <img src="/frontend/images/coord_at_yandex_map_new.png" alt="yandex map example"><br>
                </small>

                {*<button id="actor-resolve-city" data-url="{Arris\AppRouter::getRouter('ajax.get_city_by_coords')}" data-target="address_city">Определить по координатам город</button><br>*}


            </td>
        </tr>

        <tr>
            <td valign="top">VK-banner</td>
            <td>
                {*<small>Горизонтальный баннер 400×134 пикселей, обычно из группы ВКонтакте (<a href="https://vk.com/cherniy_les" target="_blank">пример</a>)<br>
                    Если вы не знаете как указать ссылку на эту картинку - просто напишите сюда: "надо взять из группы ВК"</small><br>*}
                <label>
                    <input type="text" value="{$item.banner_url}" size="80" name="vk_banner">
                </label> <br>
            </td>
        </tr>
        <tr>
            <td>
                Управление
            </td>
            <td style="display: flex;">
                <fieldset>
                    <legend>
                        Публичность:
                    </legend>
                    <label>
                        <input type="radio" name="is_public" value="N"  style="transform: scale(1.3)" {if $item.is_public eq 0}checked{/if}> Скрыть
                    </label>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <label>
                        <input type="radio" name="is_public" value="Y" style="transform: scale(1.3)" {if $item.is_public eq 1}checked{/if}> Показать
                    </label>
                </fieldset>
                <fieldset>
                    <legend>
                        Тип объекта:
                    </legend>
                    <label>
                        <select name="poi_type">
                            <option value="club">Клуб</option>
                            <option value="market">Магазин</option>
                        </select>
                    </label>
                </fieldset>
            </td>
        </tr>
    </table>
    <br>
    <hr>

    <div class="flex-container">
        <button class="flex-button" type="button" data-action="redirect" data-url="{Arris\AppRouter::getRouter('view.poi.list')}">НАЗАД,<br>К СПИСКУ</button>
        <button class="flex-button"
                type="button"
                data-action="redirect"
                data-confirm-message="Точно удалить?"
                data-url="{Arris\AppRouter::getRouter('callback.delete.poi', [ 'id' => $item.id ])}"
        >Удалить</button>
        <button class="flex-button" type="submit" tabindex="8">СОХРАНИТЬ</button>
    </div>
</form>

<script src="/frontend/edit.js"></script>

<div style="float: right">

</div>


</body>