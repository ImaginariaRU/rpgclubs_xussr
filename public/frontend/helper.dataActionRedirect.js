/**
 * Action redirect helper
 * @version '2024-11-03'
 *
 * data-action="redirect"
 * data-target : _blank|?
 * data-url
 * data-confirm-message
 *
 * no jQuery!
 */
document.addEventListener("DOMContentLoaded", function() {
    const elements = document.querySelectorAll(`[data-action="redirect"]`);

    elements.forEach(function(element) {
        element.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();

            let url = element.getAttribute('data-url');
            let target = element.getAttribute('data-target') || '';
            let confirmMessage = element.getAttribute('data-confirm-message') || '';

            if (confirmMessage.length > 0) {
                if (!confirm(confirmMessage)) {
                    return false;
                }
            }

            if (target === "_blank") {
                const newWindow = window.open(url, '_blank');
                if (newWindow) newWindow.focus(); // Проверяем, что новое окно успешно открылось
            } else {
                window.location.assign(url);
            }
        });
    });

    // Action close
    const closeActions = document.querySelectorAll(".action-close");
    closeActions.forEach(function(closeAction) {
        closeAction.addEventListener('click', function() {
            window.close();
        });
    });
});

// -eof- //
