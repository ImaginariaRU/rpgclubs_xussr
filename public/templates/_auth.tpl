{* контейнер страниц авторизации *}
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>{$title}</title>

    <link   href="/frontend/jquery/jquery.notifyBar.css" rel="stylesheet">
    <link   href="/frontend/colorbox/colorbox.css" rel="stylesheet">

    <script src="/frontend/jquery/jquery-3.2.1_min.js"></script>
    <script src="/frontend/jquery/jquery.notifyBar.js"></script>
    <script src="/frontend/helper.notifyBar.js"></script>
    <script src="/frontend/helper.dataActionRedirect.js"></script>

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
    <script>
        const flash_messages = {$flash_messages};
    </script>

</head>
<body>
<div class="content-center">
    {include file=$inner_template}
</div>
</body>
</html>



