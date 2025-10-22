/**
 * UI Manager
 *
 * @package Bonaire
 * @since 1.0.0
 */

export class UIManager {
    constructor($, alertify) {
        this.$ = $;
        this.alertify = alertify;
        this.loader = null;
    }

    initLoader() {
        const $ = this.$;
        const loaderHTML = '<div class="bonaire-loader" style="display:none;"><div class="bonaire-spinner"></div></div>';
        $('#wpbody').prepend(loaderHTML);
        this.loader = $('.bonaire-loader');
    }

    showLoader() {
        if (this.loader) {
            this.loader.fadeIn();
        }
    }

    hideLoader() {
        if (this.loader) {
            this.loader.fadeOut();
        }
    }

    initTabs() {
        const $ = this.$;
        if (typeof $.fn.tabs === 'function') {
            $('.bonaire-tabs').tabs();
        }
    }

    initContextualHelp() {
        const $ = this.$;
        // Initialize contextual help if needed
        $('.bonaire-help-button').on('click', function (e) {
            e.preventDefault();
            $('#contextual-help-link').trigger('click');
        });
    }

    showNotification(message, type = 'success') {
        switch (type) {
            case 'success':
                this.alertify.success(message);
                break;
            case 'error':
                this.alertify.error(message);
                break;
            case 'warning':
                this.alertify.warning(message);
                break;
            default:
                this.alertify.message(message);
        }
    }

    confirmAction(title, message, onConfirm, onCancel = null) {
        this.alertify.confirm(
            title,
            message,
            onConfirm,
            onCancel || function () {}
        );
    }
}
