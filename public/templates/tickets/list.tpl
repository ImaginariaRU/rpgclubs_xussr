<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <script src="/frontend/jquery/jquery.min.js"></script>
    <script src="/frontend/jquery/jquery.notifyBar.js"></script>
    <script src="/frontend/helper.notifyBar.js"></script>
    <script src="/frontend/helper.dataActionRedirect.js"></script>
    <title>Список тикетов</title>
    <script></script>
    <style>
        table {
            text-align: center;
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
    </style>
</head>
<body>
<div class="flex-container">
    <button class="flex-button" type="button" data-action="redirect" data-url="{Arris\AppRouter::getRouter('view.admin.page.main')}">В АДМИНКУ</button>
    <button class="flex-button" type="button" data-action="redirect" data-url="{Arris\AppRouter::getRouter('view.main.page')}">К КАРТЕ</button>
</div>
<hr>
<table width="100%" border="1">
    <tr>
        <th>ID</th>
        <th>Status</th>
        <th>E-Mail</th>
        <th>Sender</th>
        <th>POI</th>
        <th>-</th>
    </tr>
    {foreach $dataset as $ticket}
    <tr{* style="background-color: {$ticket.color}"*}>
        <td>{$ticket.id}</td>
        <td>{$ticket.status}</td>
        <td>{$ticket.email}</td>
        <td>{$ticket.sender}</td>
        <td>{$ticket.id_poi}</td>
        <td>
            <button type="button"
                    data-action="redirect"
                    data-url="{Arris\AppRouter::getRouter('form.ticket.view', [ 'id' => $ticket.id ])}">Редактировать</button>
        </td>
    </tr>
    {/foreach}
</table>

</body>
</html>

