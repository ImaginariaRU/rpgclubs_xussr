<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Добавляем тикет</title>
    <link   href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet">
    <link href="/frontend/jquery/jquery.notifyBar.css" rel="stylesheet">
    <script src="/frontend/jquery/jquery-3.2.1_min.js" type="text/javascript" ></script>
    <script src="/frontend/jquery/jquery.notifyBar.js"></script>

    <script src="/frontend/helper.notifyBar.js"></script>
    <script src="/frontend/helper.dataActionRedirect.js"></script>

    <style>
        *[required] {
            background-image: radial-gradient(#F00 15%, transparent 16%), radial-gradient(#F00 15%, transparent 16%);
            background-size: 1em 1em;
            background-position: right top;
            background-repeat: no-repeat;
            border-color: orange;
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
        $(document).ready(function() {
            const session_values = {$session|default:'{}'};
            const flash_messages = {$flash_messages|default:'[]'};
            NotifyBarHelper.notifyFlashMessages(flash_messages);

            Object.keys(session_values).forEach(function (key) {
                $(`[name='${ key }']`).val( session_values[key] );
            });
        });
    </script>
</head>
<body>

<h1>Добавление тикета</h1>
<form action="{Arris\AppRouter::getRouter('callback.add.ticket')}" method="post">
    <table>
        <tr>
            <td>POI id</td>
            <td>
                <input type="text" value="{$id_poi}" name="id_poi">
            </td>
        </tr>
        <tr>
            <td>Whoami</td>
            <td>
                <input type="text" value="" name="sender" required>
            </td>
        </tr>
        <tr>
            <td>
                EMail
            </td>
            <td>
                <input type="text" value="" name="email" required>
            </td>
        </tr>
        <tr>
            <td>
                Info:
            </td>
            <td>
                <textarea name="content" id="" cols="30" rows="10" required></textarea>
            </td>
        </tr>

        {if !$_auth.is_logged_in}
            <tr>
                <td>
                    Капча
                </td>
                <td>
                    <img src="/kcaptcha.php" id="captcha" alt="captcha" onclick="$('#captcha').attr('src', '/kcaptcha.php?r='+Math.random()); return false;" ><br>
                    <input type="text" name="captcha" class="small" id="captcha" tabindex="8" style="width: 120px; display: inline-block;" >
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <span style="color: navy">При необходимости мы свяжемся с вами и уточним детали.</span>
                </td>
            </tr>
        {/if}

        <tr>
            <td colspan="2" align="center">
                <button type="submit">Подать заявку</button>
            </td>
        </tr>
    </table>
</form>

</body>
</html>
