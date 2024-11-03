/*
Обвязка для добавления/редактирования POI
 */
$(function (){
    $(`input[name='owner_email']`).focus();
}).on('click', '#actor-resolve-city', function(event) {
    // определить по координатам город

    event.preventDefault();
    event.stopPropagation();

    let url = $(this).data('url');
    let target = $(this).data('target');
    let lat = $("input[name='lat']").val();
    let lng = $("input[name='lng']").val();

    if (lat.length + lng.length == 0) {
        $.jGrowl("Координаты не указаны", { header: 'ПРОБЛЕМА', position: 'top-right', life: 3000, theme: 'error' });
        return false;
    }

    $.get(url, {
        lat: lat,
        lng: lng,
    }, function(answer){
        if (answer.state != 'error') {
            let data = answer.data;

            $(`input[name="${ target }"]`).val( answer['city'] );

            $.jGrowl(`Определили город \n ${ answer['city'] }`, { header: 'ВАЖНО', position: 'top-right', life: 3000, theme: 'success' });

        } else {
            $.jGrowl("Не удалось определить город по координатам", { header: 'ОШИБКА', position: 'top-right', life: 3000, theme: 'error' });
        }
    });

}).on('click', '#actor-resolve-vk-data', function(event){
    // вытащить из ВК данные о клубе (экспериментальный механизм)

    event.preventDefault();
    event.stopPropagation();

    let request_url = $(this).data('url');
    let src = $(this).data('source');
    let poi_url = $(`input[name="${ src }"]`).val();

    if (poi_url == '') {
        $.jGrowl("Нечего анализировать", { header: 'ПРОБЛЕМА', position: 'top-right', life: 3000, theme: 'error' });
        return false;
    }
    let poi_url_parts = poi_url.split('/');
    let poi_id = poi_url_parts[poi_url_parts.length-1];

    $.getJSON(request_url, {
        poi_id: poi_id,
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
            // $("#set-coords-manually").hide();

        } else {
            $.jGrowl("Данные из ВКонтакте загрузить не удалось, скорее всего нет такой группы!", { header: 'ВАЖНО', position: 'top-right', life: 10000, theme: 'error', speed: 'slow' });
        }
    });
}).on('click', '#actor-parse-address', function(event){
    // ajax.get_coords_by_address
    event.preventDefault();
    event.stopPropagation();

    let url = $(this).data('url');
    let address = $("input[name='address']").val();

    if (address.length == 0) {
        $.jGrowl('Адрес не указан', {
            header: 'ПРОБЛЕМА',
            position: 'top-right',
            life: 5000,
            theme: 'error',
            speed: 'slow'
        });
        return false;
    }

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
                $(`input[name="lat"]`).val(data['lat']);
                $(`input[name="lng"]`).val(data['lng']);
                $(`input[name="address_city"]`).val(data['city']);
                $(`input[name="latlng"]`).val(`${ data['lat']}, ${ data['lng']}`);

            } else {
                $.jGrowl("Не удалось получить координаты по адресу.", {
                    header: 'ОШИБКА',
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