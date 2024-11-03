<h1>Просмотр тикета</h1>
<form action="{Arris\AppRouter::getRouter('callback.ticket.update')}" method="post">
    <input type="hidden" value="{$item.id}" name="id">
    <table>
        <tr>
            <td>POI id</td>
            <td>
                <input type="text" value="{$item.id_poi}" name="id_poi">
            </td>
        </tr>
        <tr>
            <td>Whoami</td>
            <td>
                <input type="text" value="{$item.sender}" name="sender">
            </td>
        </tr>
        <tr>
            <td>
                EMail
            </td>
            <td>
                <input type="text" value="{$item.email}" name="email">
            </td>
        </tr>
        <tr>
            <td>
                Info:
            </td>
            <td>
                <textarea name="content" id="" cols="30" rows="10">{$item.content}</textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Тут меняем статусы тикета
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <button type="submit">Обновить заявку</button>
            </td>
        </tr>
    </table>
</form>