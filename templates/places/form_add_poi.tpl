<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Добавление клуба</title>
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
    <link   href="/frontend/jquery/jquery.notifyBar.css" rel="stylesheet">
    <link   href="/frontend/jquery/jquery.jgrowl.min.css" rel="stylesheet" />
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
    <script src="/frontend/jquery/jquery.jgrowl.min.js" type="text/javascript"></script>
    <script src="/frontend/jquery/jquery.notifyBar.js"></script>

    <script src="/frontend/NotifyBarHelper.js"></script>
    <script src="/frontend/admin.js"></script>
    <script src="/frontend/jq_data_action.js"></script>

    <style>
        *[required] {
            background-image: radial-gradient(#F00 15%, transparent 16%), radial-gradient(#F00 15%, transparent 16%);
            background-size: 1em 1em;
            background-position: right top;
            background-repeat: no-repeat;
            border-color: orange;
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
    </style>
    <script>
        $(document).ready(function() {
            const session_values = {$session|default:'{}'};
            const flash_messages = {$flash_messages|default:'[]'};
            NotifyBarHelper.notifyFlashMessages(flash_messages);
            // заполняем инпуты значениями из flash-сессии
            Object.keys(session_values).forEach(function (key) {
                $(`[name='${ key }']`).val( session_values[key] );
            });
        });
    </script>
</head>
<body>
<h2>Добавление клуба</h2>
<form method="post" action="{Arris\AppRouter::getRouter('callback.add.poi')}" id="form_add_poi">
    <table border="1" width="100%">

        <tr>
            <td>Email <br> (для обратной связи)</td>
            <td>
                <input type="email" value="{if $_auth.is_logged_in}{$_auth.email}{/if}" size="80" name="owner_email" required>
            </td>
        </tr>

        <tr>
            <td>Кто вы</td>
            <td>
                <small>Расскажите немного о себе. Кто вы? Кого вы представляете? Как с вами связаться кроме электронной почты?</small><br>
                <input type="text" size="80" name="owner_about" value=""/>
            </td>
        </tr>

        <tr>
            <td>URL сайта/группы клуба</td>
            <td>
                <small>Пожалуйста, укажите здесь URL страницы вашего клуба в VKontakte. Если таковой нет - укажите просто сайт.
                    Остальные ссылки (дискорд, тг, итд) указывайте, пожалуйста, в <strong>описании</strong>.</small> <br>
                <input type="text" value="" size="80" name="url_site"> <br><br>
                {if $_auth.is_logged_in}
                    <button id="actor-resolve-vk-data" data-url="{Arris\AppRouter::getRouter('ajax.get_vk_club_info')}" data-source="url_site">Попробовать извлечь информацию о клубе из VKontakte</button>
                {/if}
            </td>
        </tr>

        <tr>
            <td>Название клуба</td>
            <td><input type="text" value="" size="80" name="title" required></td>
        </tr>

        <tr>
            <td>Описание клуба</td>
            <td>
                <textarea cols="70" rows="7" name="description" id="textarea_club_description" required></textarea>
            </td>
        </tr>

        <tr>
            <td>Адрес:</td>
            <td>
                <small>Укажите здесь адрес клуба: </small><br>
                <input type="text" value="" size="70" name="address"><br>
                <small>А здесь, если есть какие-то особенности адреса (домофон, охрана в бизнес-центре или
                    третий поворот направо во втором дворе в доме напротив памятника Радагасту) укажите их. Это поможет людям найти вас!</small>
                <textarea cols="70" rows="7" name="address_hint"></textarea>
                <br>
                {if $_auth.is_logged_in}
                    <button id="actor-parse-address" data-url="{Arris\AppRouter::getRouter('ajax.get_coords_by_address')}">Попытаться определить координаты и город по адресу</button>
                {/if}
            </td>
        </tr>

        <tr>
            <td>Координаты</td>
            <td>
                <br>
                <div  style="display: none">
                    Lat: <input type="text" value="" size="20" name="lat">
                    /
                    Lng: <input type="text" value="" size="20" name="lng">
                    /
                </div>
                <strong>Город:</strong> <input type="text" value="" size="40" name="address_city">
                <div id="set-coords-manually">
                    <br>
                    <small>
                        Где найти координаты? Например, на <a href="https://yandex.ru/maps/" target="_blank">яндекс-карте</a> (откроется в новой вкладке).<br>
                        <img src="/frontend/coord_at_yandex_map_new.png"><br>
                        Найдите свой клуб, кликните на здание с клубом, а потом скопируйте координаты в это поле:<br>
                    </small>
                    <br>
                    <strong>Координаты:</strong> <input type="text" size="20" value="" name="latlng"> <br><br>
                    {if $_auth.is_logged_in}
                    <button id="actor-resolve-city" data-url="{Arris\AppRouter::getRouter('ajax.get_city_by_coords')}" data-target="club:address_city">Определить по координатам город</button><br>
                    {/if}
                    {*<small>Если вы не знаете координаты и не смогли узнать по координатам город - оставьте поля пустыми.</small>*}
                </div>
            </td>
        </tr>

        <tr>
            <td>VK-banner</td>
            <td>
                <small>Горизонтальный баннер 400×134 пикселей, обычно из группы ВКонтакте (<a href="https://vk.com/cherniy_les" target="_blank">пример</a>)<br>
                    Если вы не знаете как указать ссылку на эту картинку - просто напишите сюда: "надо взять из группы ВК"</small><br>
                <input type="text" value="" size="80" name="vk_banner"> <br>
                <small>Если у вас нет баннера для группы ВК - укажите хоть какой-нибудь. Мы подумаем, как его показать.</small>
            </td>
        </tr>

        {if !$_auth.is_logged_in}
        <tr>
            <td>
                Капча
            </td>
            <td>
                <img src="/kcaptcha.php" id="captcha" alt="captcha" onclick="$('#captcha').attr('src', '/kcaptcha.php?r='+Math.random()); return false;" ><br>
                <input type="text" name="captcha" class="small" id="captcha" tabindex="8" style="width: 120px; display: inline-block;" >
            </td>
        </tr>
        <tr>
            <td colspan="2">
            <span style="color: navy">Вы же понимаете, что мы не можем сразу взять и показать ваш клуб на карте? Информацию нужно проверить.
                    <br>
                    При необходимости мы свяжемся с вами и уточним детали. После этого, скорее всего, ваш клуб появится на карте.
                </span>
            </td>
        </tr>
        {/if}
    </table>
    <br>
    <table width="100%">
        <tr>
            <td width="50%" style="text-align: center">
                <button type="button" data-action="redirect" data-url="{Arris\AppRouter::getRouter('view.main.page')}">НАЗАД,<br>НА КАРТУ</button>
            </td>
            <td width="50%" style="text-align: center">
                {if $_auth.is_logged_in}
                    <button type="submit" tabindex="8">СОХРАНИТЬ</button>
                {else}
                    <button type="submit" tabindex="8">ПОДАТЬ ЗАЯВКУ<br> НА РАССМОТРЕНИЕ</button>
                {/if}
            </td>
        </tr>
    </table>
</form>

<script src="/frontend/admin_edit.js"></script>

</body>