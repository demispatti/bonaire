/**
 * Bonaire Admin Class
 *
 * @package Bonaire
 * @since 1.0.0
 */

import { FormHandler } from './FormHandler';
import { AjaxHandler } from './AjaxHandler';
import { UIManager } from './UIManager';

export class BonaireAdmin {
    constructor($, alertify) {
        this.$ = $;
        this.alertify = alertify;
        this.body = $('body');
        this.options = window.BonaireOptions || {};

        // Initialize sub-modules
        this.formHandler = new FormHandler($, this.options);
        this.ajaxHandler = new AjaxHandler($, alertify, this.options);
        this.uiManager = new UIManager($, alertify);
    }

    init() {
        this.setupAlertify();
        this.initUIComponents();
        this.bindEvents();
    }

    setupAlertify() {
        this.alertify.defaults = {
            autoReset: true,
            basic: false,
            closable: true,
            closableByDimmer: true,
            frameless: false,
            maintainFocus: true,
            maximizable: true,
            modal: true,
            movable: true,
            moveBounded: false,
            overflow: true,
            padding: true,
            pinnable: true,
            pinned: true,
            preventBodyShift: false,
            resizable: true,
            startMaximized: false,
            transition: 'zoom',
            notifier: {
                delay: 5,
                position: 'top-right',
                closeButton: true
            },
            glossary: {
                title: 'Bonaire',
                ok: 'OK',
                cancel: 'Cancel'
            }
        };
    }

    initUIComponents() {
        this.uiManager.initTabs();
        this.uiManager.initLoader();
        this.uiManager.initContextualHelp();
    }

    bindEvents() {
        const $ = this.$;

        // Settings page events
        $('.bonaire-save-options-button').on('click', (e) => {
            e.preventDefault();
            this.handleSaveOptions();
        });

        $('.bonaire-reset-options-button').on('click', (e) => {
            e.preventDefault();
            this.handleResetOptions();
        });

        $('.bonaire-test-smtp-settings-button').on('click', (e) => {
            e.preventDefault();
            this.handleTestSMTP();
        });

        // Reply form events
        $('.bonaire-submit-reply-button').on('click', (e) => {
            e.preventDefault();
            this.handleSubmitReply($(e.target));
        });

        // Dashboard widget events
        $('.bonaire-mark-as-read-button').on('click', (e) => {
            e.preventDefault();
            this.handleMarkAsRead($(e.target));
        });
    }

    handleSaveOptions() {
        const formData = this.formHandler.getFormData('#bonaire_settings_form');

        if (!formData) {
            this.alertify.error('Please fill in all required fields.');
            return;
        }

        this.uiManager.showLoader();

        this.ajaxHandler.saveOptions(formData)
            .then((response) => {
                this.uiManager.hideLoader();
                this.alertify.success(response.message || 'Settings saved successfully.');
            })
            .catch((error) => {
                this.uiManager.hideLoader();
                this.alertify.error(error.message || 'Failed to save settings.');
            });
    }

    handleResetOptions() {
        this.alertify.confirm(
            'Reset Settings',
            'Are you sure you want to reset all settings to defaults?',
            () => {
                this.uiManager.showLoader();

                this.ajaxHandler.resetOptions()
                    .then((response) => {
                        this.uiManager.hideLoader();
                        this.alertify.success(response.message || 'Settings reset successfully.');
                        location.reload();
                    })
                    .catch((error) => {
                        this.uiManager.hideLoader();
                        this.alertify.error(error.message || 'Failed to reset settings.');
                    });
            },
            () => {
                // User cancelled
            }
        );
    }

    handleTestSMTP() {
        this.uiManager.showLoader();

        this.ajaxHandler.testSMTPSettings()
            .then((response) => {
                this.uiManager.hideLoader();
                this.alertify.success(response.message || 'SMTP connection successful.');
            })
            .catch((error) => {
                this.uiManager.hideLoader();
                this.alertify.error(error.message || 'SMTP connection failed.');
            });
    }

    handleSubmitReply($button) {
        const postId = $button.data('post-id');
        const replyData = {
            post_id: postId,
            subject: $('#bonaire_reply_subject').val(),
            message: $('#bonaire_reply_message').val()
        };

        if (!replyData.subject || !replyData.message) {
            this.alertify.error('Please fill in subject and message.');
            return;
        }

        this.uiManager.showLoader();

        this.ajaxHandler.submitReply(replyData)
            .then((response) => {
                this.uiManager.hideLoader();
                this.alertify.success(response.message || 'Reply sent successfully.');
                $('#bonaire_reply_subject').val('');
                $('#bonaire_reply_message').val('');
            })
            .catch((error) => {
                this.uiManager.hideLoader();
                this.alertify.error(error.message || 'Failed to send reply.');
            });
    }

    handleMarkAsRead($button) {
        const postId = $button.data('post-id');

        this.ajaxHandler.markAsRead(postId)
            .then((response) => {
                this.alertify.success(response.message || 'Message marked as read.');
                $button.closest('.bonaire-message-item').fadeOut();
            })
            .catch((error) => {
                this.alertify.error(error.message || 'Failed to mark message as read.');
            });
    }
}
