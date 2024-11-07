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

    <script src="/frontend/helper.notifyBar.js"></script>
    <script src="/frontend/helper.dataActionRedirect.js"></script>

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
        #form_add_poi input {
            margin-bottom: 0.5em;
        }
    </style>
    <script>
        $(document).ready(function() {
            const flash_messages = {$flash_messages|default:'[]'};
            NotifyBarHelper.notifyFlashMessages(flash_messages);

            // заполняем инпуты значениями из flash-сессии
            const session_values = {$session|default:'{}'};
            Object.keys(session_values).forEach(function (key) {
                let element = document.querySelector(`[name='${ key }']`);
                if (element) {
                    if (element.tagName.toLowerCase() === 'textarea' || element.tagName.toLowerCase() === 'input') {
                        element.value = session_values[key];
                    }
                }
            });
        });
    </script>
</head>
<body>
<h2>Добавление клуба</h2>
<form method="post" action="{Arris\AppRouter::getRouter('callback.add.poi')}" id="form_add_poi">
    <table width="100%">
        <tr>
            <td valign="top">
                Об отправителе
            </td>
            <td>
                <label>
                    E-Mail:<br>
                    <input type="email" size="80" name="owner_email" value="{if $_auth.is_logged_in}{$_auth.email}{/if}" required><br><br>
                </label>
                <label>
                    <small>Расскажите немного о себе. Кто вы? Кого вы представляете? Как с вами связаться кроме электронной почты?</small><br>
                    <input type="text" size="80" name="owner_about" value=""><br><br>
                </label>

            </td>
        </tr>

        <tr>
            <td valign="top">
                URL сайта/группы клуба
            </td>

            <td>
                <small>Пожалуйста, укажите здесь URL страницы вашего клуба в VKontakte. Есть таковой нет - ссылки на сайт, дискорд, телеграм и так далее укажите ниже.</small><br>
                <label>
                    <input type="text" value="" size="80" name="url_site">
                </label>
                <br><br>
                {if $_auth.is_logged_in}
                    <button id="actor-resolve-vk-data" data-url="{Arris\AppRouter::getRouter('ajax.get_vk_club_info')}" data-source="url_site">Попробовать извлечь информацию о клубе из VKontakte</button>
                {/if}
                <br><br>
            </td>
        </tr>

        <tr>
            <td valign="top">Название клуба</td>
            <td>
                <label>
                    <input type="text" value="" size="80" name="title" required><br><br>
                </label>
            </td>
        </tr>

        <tr>
            <td valign="top">Описание клуба</td>
            <td>
                <label for="textarea_club_description"></label><textarea cols="70" rows="7" name="description" id="textarea_club_description" required></textarea>
                <br><br>
            </td>
        </tr>

        <tr>
            <td valign="top">
                Контакты клуба:
            </td>
            <td>
                <label>
                    E-Mail:<br>
                    <input type="text" name="contact_email" value="" size="80">
                </label><br>

                <label>
                    Телеграм:<br>
                    <input type="text" name="contact_telegram" value="" size="80" placeholder="Telegram">
                </label><br>

                <label>
                    Дискорд:<br>
                    <input type="text" name="contact_discord" value="" size="80" placeholder="Discord">
                </label><br>

                <label>
                    Сайт:<br>
                    <input type="text" name="contact_site" value="" size="80" placeholder="Сайт">
                </label><br>

                <label>
                    Телефон:<br>
                    <input type="text" name="contact_phone" value="" size="80" placeholder="Телефон">
                </label><br>

                <br>
            </td>
        </tr>

        <tr>
            <td valign="top">VK-banner</td>
            <td>
                <small>Горизонтальный баннер 400×134 пикселей, обычно из группы ВКонтакте (<a href="https://vk.com/cherniy_les" target="_blank">пример</a>)<br>
                    Если вы не знаете как указать ссылку на эту картинку - просто напишите сюда: "надо взять из группы ВК"</small><br>
                <label>
                    <input type="text" value="" size="80" name="vk_banner">
                </label>
                <br>
                <small>Если у вас нет баннера для группы ВК - укажите хоть какой-нибудь. Мы подумаем, как его показать.</small>
                <br><br>
            </td>
        </tr>

        <tr>
            <td valign="top">
                Координаты:
            </td>
            <td>
                <label>
                    <strong>Адрес:</strong><br>
                    <input type="text" value="" size="70" name="address"><br>
                </label>
                <br>
                {if $_auth.is_logged_in}
                    <br>
                    <button id="actor-parse-address" data-url="{Arris\AppRouter::getRouter('ajax.get_coords_by_address')}">Попытаться определить координаты (и город) по адресу</button>
                {/if}

                <label>
                    Lat:
                    <input type="text" value="" size="20" name="lat">
                </label>
                |
                <label>
                    Lng:
                    <input type="text" value="" size="20" name="lng">
                </label>
                |
                <label>
                    <strong>Город:</strong>
                    <input type="text" value="" size="40" name="address_city">
                </label>

                <br><br>

                <label>
                    <small>А здесь, если есть какие-то особенности адреса (домофон, охрана в бизнес-центре или
                        третий поворот направо во втором дворе в доме напротив памятника Радагасту) укажите их. Это поможет людям найти вас!</small>
                    <br>
                    <textarea cols="70" rows="7" name="address_hint"></textarea>
                </label>
                <br>

                <h4>Указать координаты вручную?</h4>

                <label>
                    Найдите свой клуб, кликните на здание с клубом, а потом скопируйте координаты в это поле:<br>
                    <input type="text" size="20" value="" name="latlng">
                </label>
                <br><br>
                <small>
                    Где найти координаты? Например, на <a href="https://yandex.ru/maps/" target="_blank">яндекс-карте</a> (откроется в новой вкладке).<br>
                    <img src="/frontend/images/coord_at_yandex_map_new.png" alt="yandex map example"><br>
                </small>
                {if $_auth.is_logged_in}
                    <button id="actor-parse-address" data-url="{Arris\AppRouter::getRouter('ajax.get_coords_by_address')}">Попытаться определить координаты и город по адресу</button>
                {/if}
            </td>
        </tr>

        {if !$_auth.is_logged_in}
            <tr>
                <td>
                    Капча
                </td>
                <td>
                    <img src="/kcaptcha.php" id="captcha" alt="captcha" onclick="$('#captcha').attr('src', '/kcaptcha.php?r='+Math.random()); return false;" title="кликните по картинке для обновления капчи" ><br>
                    <span style="font-size: small">кликните по картинке для обновления</span><br>
                    <label for="captcha">
                        <input type="text" name="captcha" class="small" id="captcha" tabindex="8" style="width: 120px; display: inline-block;">
                    </label>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td colspan>
            <span style="color: navy">Вы же понимаете, что мы не можем сразу взять и показать ваш клуб на карте? Информацию нужно проверить.
                    <br>
                    При необходимости мы свяжемся с вами и уточним детали. После этого, скорее всего, ваш клуб появится на карте.
                </span>
                </td>
            </tr>
        {/if}

    </table>
    <br>
    <hr>
    <div class="flex-container">
        <button class="flex-button" type="button" data-action="redirect" data-url="{Arris\AppRouter::getRouter('view.poi.list')}">НАЗАД,<br>К СПИСКУ</button>

        {if $_auth.is_logged_in}
            <button class="flex-button" type="submit" tabindex="8">СОХРАНИТЬ</button>
        {else}
            <button class="flex-button" type="submit" tabindex="8">ПОДАТЬ ЗАЯВКУ<br> НА РАССМОТРЕНИЕ</button>
        {/if}
    </div>
</form>

<script src="/frontend/edit.js"></script>

</body>
</html>
