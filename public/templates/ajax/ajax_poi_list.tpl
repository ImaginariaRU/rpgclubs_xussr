<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Список клубов</title>

    <script src="/frontend/jquery/jquery-3.2.1_min.js"></script>
    <script src="/frontend/jquery/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="/frontend/jquery/bootstrap.min.css">
    <link rel="stylesheet" href="/frontend/jquery/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="/frontend/jquery/jquery.dataTables.min.css">
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
<br>
<div class="container">
    <table id="navigation" width="99%" border="0">
        <tr>
            <td style="text-align: center">
                Всего клубов: <span style="color: blue">{$summary.clubs_visible}</span><br><br>
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

        {if $summary.clubs_visible > 8}
            <tfoot>
            <tr>
                <th>Город</th>
                <th>Название</th>
                <th>Адрес</th>
                <th>Сайт</th>
            </tr>
            </tfoot>
        {/if}

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

<script type="text/javascript">
    $(document).ready(function() {
        $('#public_clubs').DataTable({
            "pageLength": 8,
            "lengthChange": false,
        });
    } );
</script>

</body>
</html>
