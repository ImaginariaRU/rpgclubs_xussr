<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Список клубов</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">

    {if getenv('ENV_STATE') == 'dev'}
        <script src="/frontend/jquery/jquery.min.js"></script>
        <script src="/frontend/NotifyBarHelper.js"></script>
        <script src="/frontend/helper_data_action_redirect.js"></script>
    {else}
        <link href="/styles.css" rel="stylesheet">
        <script src="/scripts.js"></script>
    {/if}

    <script>
        const flash_messages = [];
    </script>

    <style type="text/css">
        button {
            font-size: large;
        }
        div.container {
            width: 95%;
            margin-left: 1px;
        }
    </style>
</head>
<body>

<div class="container">
    <table id="navigation" width="99%" border="0">
        <tr>
            <td style="text-align: center">
                {if $_auth.is_logged_in}
                    Всего мест: <span style="color: blue">{$summary.poi_total}</span>
                    (одобрено: <span style="color: blue">{$summary.poi_visible}</span>)
                {/if}
            </td>
        </tr>
    </table>


    <table border="1" width="100%" id="public_clubs" cellspacing="0" class="display table table-striped table-bordered">
        <thead>
        <tr>
            {if $_auth.is_logged_in}
                <th>
                    Public?
                </th>
            {/if}
            <th>Город</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Сайт</th>
            <th>
                <button type="button"
                        data-action="redirect"
                        data-url="/places/add">Добавить</button>
            </th>
        </tr>
        </thead>

        <tbody>
        {foreach $dataset as $row}
            <tr>
                {if $_auth.is_logged_in}
                    <td>
                        {if $row.is_public}Да{else}Нет{/if}
                    </td>
                {/if}
                <td>{$row.address_city}</td>
                <td>{$row.title}</td>
                <td>{$row.address}</td>
                <td>
                    {if $row.url_site}
                        <a href="{$row.url_site}" target="_blank">{$row.url_site}</a>
                    {else}
                        Не указан
                    {/if}
                </td>
                <td>
                    {if $_auth.is_logged_in}
                        <button type="button"
                                data-action="redirect"
                                data-url="{Arris\AppRouter::getRouter('form.edit.poi', ['id' => $row.id])}">Редактировать</button>
                    {else}

                    {/if}
                    <button type="button"
                            data-action="redirect"
                            data-url="{Arris\AppRouter::getRouter('form.add.ticket', ['id' => $row.id]) }">Complain</button>
                </td>
            </tr>
        {/foreach}

        </tbody>

    </table>
</div>

</body>
</html>
