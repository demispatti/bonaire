/**
 * AJAX Handler
 *
 * @package Bonaire
 * @since 1.0.0
 */

export class AjaxHandler {
    constructor($, alertify, options) {
        this.$ = $;
        this.alertify = alertify;
        this.options = options;
        this.ajaxurl = options.ajaxurl || '/wp-admin/admin-ajax.php';
    }

    request(action, data = {}) {
        const $ = this.$;

        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.ajaxurl,
                type: 'POST',
                data: {
                    action: action,
                    nonce: this.options.nonce || '',
                    ...data
                },
                success: function (response) {
                    if (response.success) {
                        resolve(response.data);
                    } else {
                        reject(response.data);
                    }
                },
                error: function (xhr, status, error) {
                    reject({
                        message: 'AJAX request failed: ' + error
                    });
                }
            });
        });
    }

    saveOptions(options) {
        return this.request('bonaire_save_options', { options });
    }

    resetOptions() {
        return this.request('bonaire_reset_options');
    }

    testSMTPSettings() {
        return this.request('bonaire_test_smtp_settings');
    }

    submitReply(replyData) {
        return this.request('bonaire_submit_reply', replyData);
    }

    markAsRead(postId) {
        return this.request('bonaire_mark_as_read', { post_id: postId });
    }

    testContactForm() {
        return this.request('bonaire_test_contact_form');
    }

    testIMAPSettings() {
        return this.request('bonaire_test_imap_settings');
    }
}
