<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Добавление клуба</title>
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" />
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
    <script src="/frontend/jquery/jquery.jgrowl.min.js" type="text/javascript"></script>
    <script src="/frontend/admin.js"></script>
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
</head>
<body>
<h2>Редактирование клуба</h2>
<form method="post" action="{Arris\AppRouter::getRouter('callback.edit.poi')}" id="form_add_poi">
    <input type="hidden" name="id" value="{$item.id}">
    <table border="1" width="100%">

        <tr>
            <td>E-Mail отправителя заявки</td>
            <td>
                <input type="email" size="80" name="owner_email" value="{$item.email}" required>
            </td>
        </tr>

        <tr>
            <td>Информация об отправителе</td>
            <td>
                <input type="text" size="80" name="owner_about" value="{$item.owner_about}">
            </td>
        </tr>

        <tr>
            <td>URL сайта/группы клуба</td>
            <td>
                <input type="text" value="{$item.url_site}" size="80" name="url_site"> <br>
                <button id="actor-resolve-vk-data" data-url="{Arris\AppRouter::getRouter('ajax.get_vk_club_info')}" data-source="url_site">Попробовать извлечь информацию о клубе из VKontakte</button>
            </td>
        </tr>

        <tr>
            <td>Название клуба</td>
            <td><input type="text" value="{$item.title}" size="80" name="title" required></td>
        </tr>

        <tr>
            <td>Описание клуба</td>
            <td>
                <textarea cols="70" rows="7" name="description" id="textarea_club_description" required>{$item.description}</textarea>
            </td>
        </tr>

        <tr>
            <td>Тип объекта</td>
            <td>
                <select name="poi_type">
                    <option value="club">Клуб</option>
                    <option value="market">Магазин</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>Адрес:</td>
            <td>
                <input type="text" value="{$item.address}" size="70" name="address"><br>
                Особенности адреса: <br>
                <textarea cols="70" rows="7" name="address_hint">{$item.address_hint}</textarea>
                <br>
                <button id="actor-parse-address" data-url="{Arris\AppRouter::getRouter('ajax.get_coords_by_address')}">Попытаться определить координаты и город по адресу</button>
            </td>
        </tr>

        <tr>
            <td>Координаты</td>
            <td>
                <br>
                Lat: <input type="text" value="{$item.lat}" size="20" name="lat">
                /
                Lng: <input type="text" value="{$item.lng}" size="20" name="lng">
                /
                <strong>Город:</strong> <input type="text" value="{$item.address_city}" size="40" name="address_city">
                <div id="set-coords-manually">
                    <br>
                    <small>
                        Где найти координаты? Например, на <a href="https://yandex.ru/maps/" target="_blank">яндекс-карте</a> (откроется в новой вкладке).<br>
                        <img src="/frontend/coord_at_yandex_map.png"><br>
                        Найдите свой клуб, кликните на здание с клубом, а потом скопируйте координаты в это поле:<br>
                    </small>
                    <br>
                    <strong>Координаты:</strong> <input type="text" size="20" value="{$item.lat}, {$item.lng}" name="latlng"> <br><br>

                    <button id="actor-resolve-city" data-url="{Arris\AppRouter::getRouter('ajax.get_city_by_coords')}" data-target="club:address_city">Определить по координатам город</button><br>
                </div>
            </td>
        </tr>

        <tr>
            <td>VK-banner</td>
            <td>
                <small>Горизонтальный баннер 795×200 пикселей, обычно из группы ВКонтакте (<a href="https://vk.com/blackforrest" target="_blank">пример</a>)<br>
                    Если вы не знаете как указать ссылку на эту картинку - просто напишите сюда: "надо взять из группы ВК"</small><br>
                <input type="text" value="{$item.banner_url}" size="80" name="vk_banner"> <br>
            </td>
        </tr>
        <tr>
            <td>
                Опубликовать?
            </td>
            <td>
                <div style="font-size: x-large">
                    <label>
                        <input type="radio" name="is_public" value="Y" style="transform: scale(1.3)" {if $item.is_public eq 1}checked{/if}> Да
                    </label>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <label>
                        <input type="radio" name="is_public" value="N"  style="transform: scale(1.3)" {if $item.is_public eq 0}checked{/if}> Нет
                    </label>
                </div>

            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td width="50%" style="text-align: center">
                <button data-action="redirect" data-url="{Arris\AppRouter::getRouter('view.places.list')}">НАЗАД,<br>К СПИСКУ</button>
            </td>
            <td width="50%" style="text-align: center">
                <button type="submit" tabindex="8">СОХРАНИТЬ</button>
            </td>
        </tr>
    </table>
</form>

<script>
    $(function (){
        $(`input[name='owner_email']`).focus();
    }).on('click', '#actor-resolve-city', function(event) {

        event.preventDefault();
        event.stopPropagation();

        let url = $(this).data('url');
        let target = $(this).data('target');

        $.get(url, {
            lat: $("input[name='lat']").val(),
            lng: $("input[name='lng']").val(),
        }, function(answer){
            if (answer.state != 'error') {
                let data = answer.data;

                $(`input[name="${ target }"]`).val( data['city'] );

                $.jGrowl("Определили", { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'success' });

            } else {
                $.jGrowl("Не удалось определить город по координатам", { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'error' });
            }


        });

    }).on('click', '#actor-resolve-vk-data', function(event){
        event.preventDefault();
        event.stopPropagation();

        let request_url = $(this).data('url');
        let src = $(this).data('source');
        let club_url = $(`input[name="${ src }"]`).val();

        if (club_url == '') {
            $.jGrowl("Нечего анализировать", { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'error' });
            return false;
        }
        let club_url_parts = club_url.split('/');
        let club_id = club_url_parts[club_url_parts.length-1];

        $.getJSON(request_url, {
            club_id: club_id,
        }, function(answer){
            if (answer.state != 'error') {
                let data = answer.data;

                $.jGrowl("Данные из сети ВКонтакте загружены", { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'success' });
                // раскладываем данные
                $("input[name='title']").val( data['name'] );
                $("input[name='address']").val(data['address']);
                $("input[name='address_city']").val(data['city']);
                $("textarea[name='description']").html(data['description']);
                $("input[name='lat']").val(data['lat']);
                $("input[name='lng']").val(data['lon']);
                $("input[name='vk_banner']").val(data['picture']);
                $("#set-coords-manually").hide();

            } else {
                $.jGrowl("Данные из ВКонтакте загрузить не удалось, скорее всего нет такой группы!", { header: 'ВАЖНО', position: 'top-right', life: 10000, theme: 'error', speed: 'slow' });
            }
        });
    }).on('click', '#actor-parse-address', function(event){
        event.preventDefault();
        event.stopPropagation();

        let url = $(this).data('url');
        let address = $("input[name='address']").val();

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
                    $("input[name='lat']").val(data['lat']);
                    $("input[name='lng']").val(data['lon']);

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
                    header: 'ОШИБКА',
                    position: 'top-right',
                    life: 5000,
                    theme: 'error',
                    speed: 'slow'
                });
            }
        });



    }).on('submit', '#form_add_poi', function(event){
        return true;
    })
</script>

</body>