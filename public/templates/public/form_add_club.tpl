<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавление клуба</title>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" />
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
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
    <script>
        $(function(){
            $("input[name='club:owner_email']").focus();
        }).on('click', "*[data-action='redirect']", function (event) {
            event.preventDefault();
            let url = $(this).data('url');
            let target = $(this).data('target') || '';
            let confirm_message = $(this).data('confirm-message') || '';

            if (confirm_message.length > 0) {
                if (!confirm(confirm_message)) {
                    return false;
                }
            }

            if (target == "_blank") {
                window.open(url, '_blank').focus();
            } else {
                window.location.assign(url);
            }
        }).on('click', '#actor-resolve-vk-data', function (event) {
            // Попробовать извлечь информацию о клубе из VKontakte
            event.preventDefault();
            event.stopPropagation();

            let url = $(this).data('url');

        }).on('click', '#actor-parse-address', function (event){
            // Попытаться определить координаты и город по адресу

            event.preventDefault();
            event.stopPropagation();

            let url = $(this).data('url');
            let address = $("input[name='club:address']").val();

            $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    async: false,
                    data: {
                        poi_address: address
                    },
                    success: function (answer) {
                        let data = answer.data;
                        console.log(data);

                        if (answer['is_success']) {
                            $.jGrowl("Нам удалось определить координаты по адресу", {
                                header: 'ВАЖНО',
                                position: 'top-right',
                                life: 5000,
                                theme: 'success',
                                speed: 'slow'
                            });
                            $("input[name='club:lat']").val(data.lat);
                            $("input[name='club:lng']").val(data.lon);

                        } else {
                            $.jGrowl("Не удалось получить координаты по адресу.", {
                                header: 'ВАЖНО',
                                position: 'top-right',
                                life: 5000,
                                theme: 'error',
                                speed: 'slow'
                            });
                        }
                    },
                    error: function (answer) {
                        $.jGrowl(answer, {
                            header: 'ВАЖНО',
                            position: 'top-right',
                            life: 5000,
                            theme: 'success',
                            speed: 'slow'
                        });
                    }
                });



        }).on('click', '#actor-resolve-city', function (event) {
            // Определить по координатам город

            event.preventDefault();
            event.stopPropagation();

            let url = $(this).data('url');
            let target = $(this).data('target');

            $.get(url, {
                lat: $("input[name='club:lat']").val(),
                lng: $("input[name='club:lng']").val(),
            }, function(data){
                $(`input[name="${ target }"]`).val( data );
            });

        })
        ;
    </script>
</head>
<body>
<h2>Добавление клуба</h2>

<form action="{Arris\AppRouter::getRouter('callback.form.add.poi')}" method="POST" id="form-unautharized-add-club">
    <table border="1" width="100%">
        <tr>
            <td>Email <br> (для обратной связи)</td>
            <td><input type="email" value="" size="80" name="club:owner_email" required></td>
        </tr>
        <tr>
            <td>Кто вы?</td>
            <td>
                <small>Расскажите немного о себе. Кто вы? Кого вы представляете? Как с вами связаться кроме электронной почты?</small><br><br>
                <input type="text" size="80" name="club:owner_about" /><br><br>
            </td>
        </tr>
        <tr>
            <td>URL сайта клуба</td>
            <td>
                <small>Пожалуйста, укажите здесь URL страницы вашего клуба в VKontakte. Если таковой нет - укажите просто сайт.
                    Дополнительные сайты нужно указывать <strong>описании</strong>.</small> <br>
                <input type="text" value="" size="80" name="club:url_site">
                <br><br>
                <button id="actor-resolve-vk-data" data-url="{Arris\AppRouter::getRouter('ajax_get_vk_club_info')}">Попробовать извлечь информацию о клубе из VKontakte</button>
                <br><br>
            </td>
        </tr>
        <tr>
            <td>Название клуба</td>
            <td>
                <input type="text" value="" size="80" name="club:title" required>
            </td>
        </tr>
        <tr>
            <td>Описание клуба</td>
            <td>
                <textarea cols="70" rows="7" name="club:description" id="textarea_club_description" required></textarea>
            </td>
        </tr>
        <tr>
            <td>Адрес:</td>
            <td>
                <small>Укажите здесь адрес клуба: </small><br>
                <input type="text" value="" size="70" name="club:address"><br>
                <button
                        id="actor-parse-address"
                        type="button"
                        data-url="{Arris\AppRouter::getRouter('ajax_get_coords_by_address')}"
                >Попытаться определить координаты и город по адресу</button>
                <hr>
                <small>Если есть какие-то особенности адреса (домофон, охрана в бизнес-центре или
                    третий поворот направо во втором дворе в доме напротив памятника Радагасту) укажите их здесь. Это поможет людям найти вас!</small>
                <textarea cols="70" rows="7" name="club:address_hint"></textarea>
            </td>
        </tr>
        <tr>
            <td>Координаты</td>
            <td>
                <br>
                Lat: <input type="text" value="" size="20" name="club:lat">
                /
                Lng: <input type="text" value="" size="20" name="club:lng">
                /
                Город: <input type="text" value="" size="40" name="club:address_city">

                <div id="set-coords-manually">
                    <br>
                    <small>
                        Где найти координаты? Например, на <a href="https://yandex.ru/maps/" target="_blank">яндекс-карте</a> (откроется в новой вкладке).</br>
                        <img src="/frontend/coord_at_yandex_map.png"> <br>
                        Найдите свой клуб, кликните на здание с клубом, а потом скопируйте координаты в это поле:
                    </small>
                    <br>
                    <input type="text" size="20" value="" name="club:latlng"> <br><br>

                    <button
                            id="actor-resolve-city"
                            data-url="{Arris\AppRouter::getRouter('ajax_get_city_by_coords')}"
                            data-target="club:address_city"
                            disabled
                    >Определить по координатам город</button><br>
                    <small>Если вы не знаете координаты и не смогли узнать по координатам город - оставьте поля пустыми.</small>
                </div>
            </td>
        </tr>
        <tr>
            <td>VK-banner</td>
            <td>
                <small>Горизонтальный баннер 795×200 пикселей, обычно из группы ВКонтакте (<a href="https://vk.com/blackforrest" target="_blank">пример</a>)<br>
                    Если вы не знаете как указать ссылку на эту картинку - просто напишите сюда: "из группы ВК"</small><br>
                <small>Если у вас нет баннера для группы ВК - укажите хоть какой-нибудь. Мы подумаем, как его показать.</small><br>
                <input type="text" value="" size="80" name="club:vk_banner"><br><br>
            </td>
        </tr>
        <tr>
            <td>Разумеется, капча:</td>
            <td>
                <img src="/kcaptcha.php" id="captcha" alt="captcha" onclick="$('#captcha').attr('src', '/kcaptcha.php?r='+Math.random()); return false;" ><br >
                <input type="text" name="captcha" class="small" id="captcha" tabindex="8" style="width: 120px; display: inline-block;" >
            </td>
        </tr>
        <tr>
            <td colspan="2">
            <span style="color: navy">Вы же понимаете, что я не могу сразу взять и показать ваш клуб на карте? Информацию нужно проверить.
                    <br>
                    При необходимости мы свяжемся с вами и уточним детали. После этого, скорее всего, ваш клуб появится на карте. <br>
                Постараемся это сделать в течение суток.
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <button
                        data-action='redirect'
                        data-url="{Arris\AppRouter::getRouter('view.main.page')}"
                        data-confirm-message="Точно вернуться назад? Данные не сохранятся!"
                        id="actor-quit">НАЗАД,<br>НА КАРТУ</button>
            </td>
            <td>
                <button type="submit" tabindex="8">ПОДАТЬ ЗАЯВКУ<br> НА РАССМОТРЕНИЕ</button>
            </td>
        </tr>
    </table>

</form>

</body>
</html>