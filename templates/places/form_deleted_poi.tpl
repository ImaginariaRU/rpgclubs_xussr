<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Объект удалён</title>
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" />
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
    <script src="/frontend/jq_data_action.js"></script>
    <style>
        .content-center {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding-top: 10%;
            text-align: center;
        }
        .left-align {
            text-align: left;
        }
        input[required] {
            border: 1px solid teal ;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="content-center">
    Объект удалён <br>
    <hr>
    <button type="button"
            data-action="redirect"
            data-url="{Arris\AppRouter::getRouter('view.poi.list')}"
    >К списку объектов</button>
</div>
</body>
</html>