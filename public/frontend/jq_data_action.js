$(document).ready(function() {
    /**
     * // Action redirect
     *
     * data-action="redirect"
     * data-target : _blank|?
     * data-url
     * data-confirm-message
     */
    $("*[data-action='redirect']").on('click', function (event) {
        let url = $(this).data('url');
        let target = $(this).data('target') || '';
        let confirm_message = $(this).data('confirm-message') || '';

        console.log(url, target, confirm_message);

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