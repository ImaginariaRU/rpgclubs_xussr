<!DOCTYPE html>
<html lang="ru">
<head>
    <title>{$title}</title>

    <link   rel="stylesheet" href="/frontend/styles.css">
    <script src="/frontend/jquery/jquery-3.2.1_min.js"></script>

    <script src="/frontend/colorbox/jquery.colorbox-min.js"></script>
    <link   href="/frontend/colorbox/colorbox.css" rel="stylesheet">

    <script src="/frontend/jquery/jquery.notifyBar.js"></script>
    <link   rel="stylesheet" href="/frontend/jquery/jquery.notifyBar.css">
    <script src="/frontend/helper.notifyBar.js"></script>
    <script src="/frontend/helper.dataActionRedirect.js"></script>

    <script type="text/javascript">
        const flash_messages = {$flash_messages};
    </script>
    <style>
        #global_wrapper {
            display: flex;
        }

        #left-column {
            position: fixed;
            top: 0;
            height: 600px; /* Минимальная высота */
            width: 200px; /* Фиксированная ширина */
            display: flex;
            flex-direction: column;
            border-right: 3px solid black;
        }

        #right-column {
            margin-left: 220px; /* Отступ, чтобы не перекрывался левым блоком */
            width: calc(100% - 220px);
            /* Ваши стили для правого блока */
        }

        .top {
            /* Ваши стили для верхнего блока */
        }

        .spacer {
            flex-grow: 1;
        }

        .bottom {
            /* Ваши стили для нижнего блока */
        }

        ul.non-marked-list {
            list-style-type: none; /* Убираем маркеры списка */
            padding-left: 0; /* Убираем отступ слева */
        }

        ul.left-menu > li {
            padding: 5px 0 5px 0;
            /* Ваши стили для элементов списка */
        }

        ul.left-menu button {
            display: block;
            width: 188px;
            height: 55px;
        }
    </style>
</head>
<body>
<div id="global_wrapper">
    <div id="left-column">
        <div class="top">
            <ul class="non-marked-list left-menu">
                {if $inner_buttons}
                    {*рендер блока внутренних кнопок, заданных через хэлпер TemplateHelper*}
                    {foreach $inner_buttons as $button}
                        <li>
                            <button
                                    type="button"
                                    title="{$button.url}"
                                    data-action="redirect"
                                    data-url="{$button.url}"
                                    {if $button.class}class="{$button.class}"{/if}
                                    {if $button.disabled eq 'true'}disabled{/if}
                            >{$button.text}
                            </button>
                        </li>
                    {/foreach}
                {/if}
            </ul>
        </div>
        <div class="spacer"></div>
        <div class="bottom">
            <ul class="non-marked-list left-menu">
                {*{if $_config.auth.is_admin}
                    <li>
                        <button type="button" data-action="redirect" data-url="/admin/users/">Админка пользователей</button>
                    </li>
                {/if}*}
                <li>
                    <button type="button" data-action="redirect" data-url="/auth/logout/">Logout</button>
                </li>
                <li>
                    <small>
                        Logged as: <br>
                        {$_auth.username}<br>
                        ({$_auth.email})
                    </small>
                </li>
            </ul>
        </div>
    </div>
    <div id="right-column">
       {* {include file=$inner_template}*}
    </div>
</div>

</body>
</html>