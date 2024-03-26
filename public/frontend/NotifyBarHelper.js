class NotifyBarHelper {

    /**
     * Notify bar helper: success
     *
     * @param messages array
     * @param timeout seconds
     */
    static notifySuccess(messages, timeout = 1) {
        let msg = typeof messages == "string" ? [ messages ] : messages;
        $.notifyBar({
            html: msg.join('<br>'),
            delay: timeout * 1000,
            cssClass: 'success'
        });
    }

    /**
     * Notify bar helper: error
     *
     * @param messages
     * @param timeout
     */
    static notifyError(messages, timeout = 600) {
        let msg = typeof messages == "string" ? [ messages ] : messages;
        $.notifyBar({
            html: msg.join('<br>'),
            delay: timeout * 1000,
            cssClass: 'error'
        });
    }

    /**
     * Notify bar helper: custom class
     *
     * @param messages
     * @param timeout
     * @param custom_class
     */
    static notifyCustom(messages, timeout = 10, custom_class = '') {
        let msg = typeof messages == "string" ? [ messages ] : messages;
        $.notifyBar({
            html: msg.join('<br>'),
            delay: timeout * 1000,
            cssClass: custom_class
        });
    }

    /**
     * Notify bar helper: flash messages
     *
     * @param messages
     */
    static notifyFlashMessages(messages) {
        $.each(messages, function (key, value) {
            switch (key) {
                case 'success': {
                    notifySuccess(value);
                    break;
                }
                case 'error': {
                    notifyError(value);
                    break;
                }
                default: {
                    notifyCustom(value)
                    break;
                }
            }
        });
    }

} // class