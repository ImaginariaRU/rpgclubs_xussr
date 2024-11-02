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
        <tr>
            <td colspan="2" align="center">
                <button type="submit">Подать заявку</button>
            </td>
        </tr>
    </table>
</form>