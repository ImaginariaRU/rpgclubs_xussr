<style>
    .content-center {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: calc(100vh);
        text-align: center;
    }
    .left-align {
        text-align: left;
    }
</style>
<script src="/frontend/helper.dataActionRedirect.js"></script>
<div class="content-center">
    <div>
        404 Error <br> Страница не найдена<br><br>
        Возможно, ваша рабочая сессия в админке истекла. <br> Теперь внутренние страницы недоступны. <br> Для продолжения
        работы с админкой вам нужно снова залогиниться на <a href="{Arris\AppRouter::getRouter('view.form.login')}">странице входа</a>.
    </div>
</div>
