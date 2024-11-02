<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список клубов</title>
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
    <style type="text/css">
        button {
            font-size: large;
        }
        div.container {
            width: 60%;
        }
    </style>
</head>
<body>

<div class="container">
    <table id="navigation" width="99%" border="0">
        <tr>
            <td width="33%" style="text-align: left">
            </td>
            <td width="33%" style="text-align: center">
                Всего клубов: <span style="color: blue">{$summary.clubs_total}</span><br>
                На карте клубов: <span style="color: blue">{$summary.clubs_visible}</span>
            </td>
            <td width="33%" style="text-align: right">
                <button id="actor-back-to-frontpage" data-url="{Arris\AppRouter::getRouter('view.main.page')}">НА КАРТУ</button>
            </td>
        </tr>
    </table>
    <hr>

    <table border="1" width="100%" id="public_clubs" cellspacing="0" class="display table table-striped table-bordered">
        <thead>
        <tr>
            {* <th>Одобрено</th> *}
            <th>Город</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Сайт</th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            {* <th>Одобрено</th> *}
            <th>Город</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Сайт</th>
        </tr>
        </tfoot>

        <tbody>
        {foreach $dataset as $row}

            <tr>
                {*<td align="center">
                    {if $row.is_public}
                        <h1>+</h1>
                    {else}
                        <h1>-</h1>
                    {/if}
                </td>*}
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
            "lengthChange": false
        });
    } );

    ;$(function(){
    }).on('click', '#actor-back-to-frontpage', function(){
        document.location.href = $(this).data('url');
    });
</script>


</body>
</html>