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
                Всего клубов: <span style="color: blue">{$summary.clubs_visible}</span>
            </td>
        </tr>
    </table>

    <table border="1" width="100%" id="public_clubs" cellspacing="0" class="display table table-striped table-bordered">
        <thead>
        <tr>
            <th>Город</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Сайт</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Город</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Сайт</th>
        </tr>
        </tfoot>

        <tbody>
        {foreach $dataset as $row}
            <tr>
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
            </tr>
        {/foreach}

        </tbody>

    </table>
</div>

</body>
</html>
