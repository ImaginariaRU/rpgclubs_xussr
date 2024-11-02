$(document).ready(function() {
    if (window.flash_messages) {
        NotifyBarHelper.notifyFlashMessages(window.flash_messages);
    }

    // клик в любое место ячейки таблицы вызывает смену чекбокса
    $("td:has(label:has(input[type='checkbox']))").on('click', function (e){
        let checkbox = $(this).find('input:checkbox');
        checkbox.prop('checked', !checkbox.prop('checked'));
        e.preventDefault();
    });
});