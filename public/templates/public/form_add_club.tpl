<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Добавление клуба</title>
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" />
    <link   href="/frontend/colorbox/colorbox.css" rel="stylesheet" />
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
    <script src="/frontend/colorbox/jquery.colorbox-min.js" type="text/javascript"></script>
    <script src="/frontend/jquery/jquery.jgrowl.min.js" type="text/javascript"></script>
    <link   href="/frontend/jquery/jquery.jgrowl.min.css" rel="stylesheet" />

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

    </style>
    <script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>
<h2>Добавление клуба</h2>

<form action="{Arris\AppRouter::getRouter('callback.form.add.poi')}" method="POST" id="form-unautharized-add-club">
    <table border="1" width="100%">
{*
        <tr>
            <td colspan="100">
                <strong>ВАЖНО:</strong>
                В этом случае просто отправьте письмо на ящик <a href="mailto:rpgclubsrf@yandex.ru">rpgclubsrf@yandex.ru</a>,
                сообщив нам про свой клуб всё, что ввели бы в эту форму (скорее всего достаточно будет только адреса клуба).
            </td>
        </tr>
*}
        <!--<tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td rowspan="100" width="40%">карта</td>
        </tr>-->
        <tr>
            <td>Email <br> (для обратной связи)</td>
            <td>
                <input type="email" value="" size="80" name="club:unauthadd:owner_email" required>
            </td>
        </tr>
        <tr>
            <td>Кто вы</td>
            <td>
                <small>Расскажите немного о себе. Кто вы? Кого вы представляете? Как с вами связаться кроме электронной почты?</small><br>
                <input type="text" size="80" name="club:unauthadd:owner_about" />
            </td>
        </tr>
        <tr>
            <td>URL сайта клуба</td>
            <td>
                <small>Пожалуйста, укажите здесь URL страницы вашего клуба в VKontakte. Если таковой нет - укажите просто сайт.
                    Дополнительные сайты нужно указывать <strong>описании</strong>.</small> <br>
                <input type="text" value="" size="80" name="club:unauthadd:url_site"> <br>

                <button id="actor-resolve-vk-data" data-url="{Arris\AppRouter::getRouter('ajax_get_vk_club_info')}" data-source="club:unauthadd:url_site">Попробовать извлечь информацию о клубе из VKontakte</button>
            </td>
        </tr>

        <tr>
            <td>Название клуба</td>
            <td><input type="text" value="" size="80" name="club:unauthadd:title" required></td>
        </tr>
        <tr>
            <td>Описание клуба</td>
            <td>
                <textarea cols="70" rows="7" name="club:unauthadd:description" id="textarea_club_description" required></textarea>
            </td>
        </tr>
        <tr>
            <td>Адрес:</td>
            <td>
                <small>Укажите здесь адрес клуба: </small><br>
                <input type="text" value="" size="70" name="club:unauthadd:address"><br>
                <small>А здесь, если есть какие-то особенности адреса (домофон, охрана в бизнес-центре или
                    третий поворот направо во втором дворе в доме напротив памятника Радагасту) укажите их. Это поможет людям найти вас!</small>
                <textarea cols="70" rows="7" name="club:unauthadd:address_hint"></textarea>
                <br />
                <button id="actor-parse-address" data-url="{Arris\AppRouter::getRouter('ajax_get_coords_by_address')}">Попытаться определить координаты и город по адресу</button>
            </td>
        </tr>
        <tr>
            <td>Координаты</td>
            <td>
                Lat: <input type="text" value="" size="20" name="club:unauthadd:lat">
                /
                Lng: <input type="text" value="" size="20" name="club:unauthadd:lng">
                /
                Город: <input type="text" value="" size="40" name="club:unauthadd:address_city">
                <div id="set-coords-manually">
                    <br>
                    <small>
                        Где найти координаты? Например, на <a href="https://yandex.ru/maps/" target="_blank">яндекс-карте</a> (откроется в новой вкладке).</br>
                        <img src="/frontend/coord_at_yandex_map.png"> <br>
                        Найдите свой клуб, кликните на здание с клубом, а потом скопируйте координаты в это поле:
                    </small>
                    <br>
                    <input type="text" size="20" value="" name="club:unauthadd:latlng"> <br>

                    <button id="actor-resolve-city" data-url="{Arris\AppRouter::getRouter('ajax_get_city_by_coords')}" data-target="club:unauthadd:address_city">Определить по координатам город</button><br>
                    <small>Если вы не знаете координаты и не смогли узнать по координатам город - оставьте поля пустыми.</small>
                </div>
            </td>
        </tr>
        <tr>
            <td>VK-banner</td>
            <td>
                <small>Горизонтальный баннер 795×200 пикселей, обычно из группы ВКонтакте (<a href="https://vk.com/blackforrest" target="_blank">пример</a>)<br>
                    Если вы не знаете как указать ссылку на эту картинку - просто напишите сюда: "из группы ВК"</small><br>
                <input type="text" value="" size="80" name="club:unauthadd:vk_banner">
            </td>
        </tr>
        <tr>
            <td>Баннер (другой)</td>
            <td>
                <small>Если у вас нет баннера для группы ВК - укажите хоть какой-нибудь. Мы подумаем, как его показать.</small><br>
                <input type="text" value="" size="80" name="club:unauthadd:banner_other">
            </td>
        </tr>
        <!--
                <tr>
                    <td>Формат <br>инфобокса</td>
                    <td>
                        <small>Тут всё просто. Если вы указали баннер из группы ВК - ничего не меняйте. На самом деле вы можете указать оба
                            баннера, а этой опцией позднее можно будет выбрать режим отображения информации о клубе.</small><br>
                        <label>VK Style<input type="radio" name="club:unauthadd:infobox_layout" value="VKBanner" checked></label><br>
                        <label>Other<input type="radio" name="club:unauthadd:infobox_layout" value="Other"></label>
                    </td>
                </tr>
        -->
        <tr>
            <td>
                Разумеется, капча:
            </td>
            <td>
                <img src="/kcaptcha.php" id="captcha" alt="captcha" onclick="$('#captcha').attr('src', '/kcaptcha.php?r='+Math.random()); return false;" ><br >
                <input type="text" name="captcha" class="small" id="captcha" tabindex="8" style="width: 120px; display: inline-block;" >
                {*<div id="recaptcha" class="g-recaptcha" data-sitekey="*}{*options.captcha_sitekey*}{*"></div>
                <small>Есть маленький нюанс. Это временная форма добавления данных, поэтому если вы не пройдете гуглокапчу, но
                    нажмете на "подать заявку на рассмотрение", скорее всего вы потеряете все данные, которые сейчас ввели. <br>
                    Будьте внимательны!!!</small>*}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span style="color: navy">Вы же понимаете, что я не могу сразу взять и показать ваш клуб на карте? Информацию нужно проверить.
                    <br>
                    При необходимости мы свяжемся с вами и уточним детали. После этого, скорее всего, ваш клуб появится на карте.
                    Произойдет это в течение суток.
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <button data-url="{Arris\AppRouter::getRouter('view.main.page')}" id="actor-quit">НАЗАД,<br>НА КАРТУ</button>
            </td>
            <td>
                <span style="float: right">
                    <button type="submit" tabindex="8">ПОДАТЬ ЗАЯВКУ<br> НА РАССМОТРЕНИЕ</button>
                </span>
            </td>
        </tr>
    </table>
</form>
{*
<form action="{Arris\AppRouter::getRouter('view.main.page')}" method="POST" id="form-unautharized-add-result" class="invisible">
    <div class="text-align:center">
        <button style="height: 200px; width: 200px; font-size: 600%"></button>
    </div>
</form>
*}

<script type="text/javascript">
    ;$(function(){
        $("input[name='club:unauthadd:owner_email']").focus();
    }).on('click', '#actor-quit', function(event){

        event.preventDefault();
        event.stopPropagation();
        document.location.href = $(this).data('url');

    }).on('click', '#actor-resolve-city', function(event){
        event.preventDefault();
        event.stopPropagation();

        let url = $(this).data('url');
        let target = $(this).data('target');

        $.get(url, {
            lat: $("input[name='club:unauthadd:lat']").val(),
            lng: $("input[name='club:unauthadd:lng']").val(),
        }, function(data){
            $("input[name='" + target +"']").val( data );
        });

    }).on('click', '#actor-resolve-vk-data', function(event){
        event.preventDefault();
        event.stopPropagation();

        let request_url = $(this).data('url');
        let src = $(this).data('source');
        let club_url = $("input[name='"+ src +"']").val();

        if (club_url == '') {
            $.jGrowl("Нечего анализировать", { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'error' });
            return false;
        }
        let club_url_parts = club_url.split('/');
        let club_id = club_url_parts[club_url_parts.length-1];

        $.getJSON(request_url, {
            club_id: club_id,
        }, function(data){
            if (data.state != 'error') {
                $.jGrowl("Данные из сети ВКонтакте загружены", { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'success' });
                // раскладываем данные
                $("input[name='club:unauthadd:title']").val( data.name );
                $("input[name='club:unauthadd:address']").val(data.address);
                $("input[name='club:unauthadd:address_city']").val(data.city);
                $("textarea[name='club:unauthadd:description']").html(data.description);
                $("input[name='club:unauthadd:lat']").val(data.lat);
                $("input[name='club:unauthadd:lng']").val(data.lon);
                $("input[name='club:unauthadd:vk_banner']").val(data.picture);
                $("#set-coords-manually").hide();

            } else {
                $.jGrowl("Данные из ВКонтакте загрузить не удалось, скорее всего нет такой группы!", { header: 'ВАЖНО', position: 'top-right', life: 10000, theme: 'error', speed: 'slow' });
            }
        });
    }).on('click', '#actor-parse-address', function(event){
        event.preventDefault();
        event.stopPropagation();

        let request_url = $(this).data('url');
        let address = $("input[name='club:unauthadd:address']").val();

        $.getJSON(request_url, { club_address: address }, function(data){
            if (!data) {
                $.jGrowl("Не удалось получить координаты по адресу.", { header: 'ВАЖНО', position: 'top-right', life: 5000, theme: 'error', speed: 'slow' });
            } else {
                $.jGrowl("Нам удалось определить координаты по адресу", { header: 'ВАЖНО', position: 'top-right', life: 5000, theme: 'success', speed: 'slow' });

                $("input[name='club:unauthadd:lat']").val(data.city_lat);
                $("input[name='club:unauthadd:lng']").val(data.city_lng);
            }
        });


    }).on('submit', '#form-unautharized-add-club', function(event){
        // сабмит формы - мы не используем гуглокапчу
        /*let $captcha = $( '#recaptcha' );
        let response = grecaptcha.getResponse();

        if (response.length === 0) {
            if( !$captcha.hasClass( "error" ) ){
                $captcha.addClass( "error" );
            }
            return false;
        } else {
            $captcha.removeClass( "error" );
            return true;
        }*/
    });
</script>

</body>
</html>