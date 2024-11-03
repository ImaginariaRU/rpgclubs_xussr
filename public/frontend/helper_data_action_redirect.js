/**
 * Action redirect helper
 * @version '2024-11-03'
 *
 * data-action="redirect"
 * data-target : _blank|?
 * data-url
 * data-confirm-message
 *
 * @todo: jQuery plugin ?
 */
$(document).ready(function() {
    $("*[data-action='redirect']").on('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        let url = $(this).data('url');
        let target = $(this).data('target') || '';
        let confirm_message = $(this).data('confirm-message') || '';

        if (confirm_message.length > 0) {
            if (!confirm(confirm_message)) {
                return false;
            }
        }

        if (target == "_blank") {
            window.open(url, '_blank').focus();
        } else {
            window.location.assign(url);
        }
    });

    // Action close
    $(".action-close").on('click', function (){
        window.close();
    });
});