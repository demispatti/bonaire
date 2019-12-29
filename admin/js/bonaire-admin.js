/**
 * The javascript for the admin part.
 *
 * @since      0.9.0
 * @package    Bonaire
 * @subpackage Bonaire/admin
 * @author     Demis Patti <demispatti@gmail.com>
 */
"use strict";
(function ( $, alertify ){

    function BonaireAdmin (){

        this.body = $('body');
        this.ajaxurl = BonaireOptions.ajaxurl;
        this.defaultOptions = BonaireOptions.default_options;
        this.optionsMeta = BonaireOptions.options_meta;
        this.hasEmptyField = BonaireOptions.has_empty_field;
        this.saveReply = BonaireOptions.save_reply;
        this.alertifyNotifications = BonaireOptionsPage.alertify_notifications;
        this.alertifyError = BonaireOptionsPage.alertify_error;
        this.replyError = BonaireOptionsPage.reply_error;
        this.emptyMessageError = BonaireOptionsPage.empty_message_error;
        this.optionsPageNotifications = BonaireOptionsPage.settings_page_notifications;
        this.resetOptionsConfirmation = BonaireOptionsPage.reset_options_confirmation;
        this.helpButton = $('#connection_details a.information');
        this.handleDiv = $('#bonaire_dashboard_widget > button.handlediv');
        // Dashboard Widget
        this.bonaireMarkAsReadButton = $('.bonaire-mark-as-read-button');
        this.markAsSpamButton = $('.bonaire-mark-as-spam-button');
        this.moveToTrashButton = $('.bonaire-move-to-trash-button');
        this.markAsReadAction = 'bonaire_mark_as_read';
        this.markAsSpamAction = 'bonaire_mark_as_spam';
        this.moveToTrashAction = 'bonaire_move_to_trash';
        this.noMessagesMessage = BonaireWidget.no_messages_message;
        // Reply Form Meta Box
        this.form = $('#bonaire-reply-form');
        this.bonaireReplyForm = $('form#bonaire_reply_form');
        this.bonaireReplyButton = $('.bonaire-submit-reply-button');
        this.bonaireReplyAction = 'bonaire_submit_reply';
        // Options Page
        this.settingsPageDisplayStrings = BonaireSettingsPageDisplay.strings;
        this.connectionDetailsContainer = $('#connection_details');
        this.bonaireOptionsForm = $('#bonaire_settings_form');
        this.bonaireOptionsFormInputFields = this.connectionDetailsContainer.find('input, select, textarea');
        this.bonaireSaveOptionsButton = this.bonaireOptionsForm.find('.bonaire-save-options-button');
        this.bonaireResetOptionsButton = this.bonaireOptionsForm.find('.bonaire-reset-options-button');
        this.bonaireSendTestMailButton = this.connectionDetailsContainer.find('.bonaire-send-testmail-button');
        this.bonaireTestContactFormButton = this.connectionDetailsContainer.find('.bonaire-test-contact-form-button');
        this.bonaireTestSmtpSettingsButton = this.connectionDetailsContainer.find('.bonaire-test-smtp-settings-button');
        this.bonaireTestImapSettingsButton = this.connectionDetailsContainer.find('.bonaire-test-imap-settings-button');
        this.bonaireSaveOptionsAction = 'bonaire_save_options';
        this.resetOptionsAction = 'bonaire_reset_options';
        this.testConnectionAction = 'bonaire_evaluate_settings';
        this.testContactFormAction = 'bonaire_test_contact_form';
        this.testSmtpSettingsAction = 'bonaire_test_smtp_settings';
        this.testImapSettingsAction = 'bonaire_test_imap_settings';
        this.sendTestMailAction = 'bonaire_send_testmail';
        this.bonaireUpdateSettingsStatusAction = 'bonaire_get_settings_status';

        // Helper
        this.loaderSpinner = null;
    }

    BonaireAdmin.prototype = {
        // Setup
        init: function (){
            this.setLoaderSpinner();
            this.setAlertify();
            this.initContextualHelp();
            this.addEvents();
        },
        setLoaderSpinner: function (){
            $('#wpbody').prepend('<div class="loader loader-default" data-text="' + this.optionsPageNotifications.working + '"></div>');
            this.loaderSpinner = $('.loader.loader-default');
            $(this.loaderSpinner).hide();
        },
        setAlertify: function (){
            alertify.defaults = {
                // dialogs defaults
                autoReset: true,
                basic: false,
                closable: true,
                closableByDimmer: true,
                frameless: false,
                maintainFocus: true, // <== global default not per instance, applies to all dialogs
                maximizable: true,
                modal: true,
                movable: true,
                moveBounded: false,
                overflow: true,
                padding: true,
                pinnable: true,
                pinned: true,
                preventBodyShift: false, // <== global default not per instance, applies to all dialogs
                resizable: true,
                startMaximized: false,
                transition: 'zoom',

                // notifier defaults
                notifier: {
                    // auto-dismiss wait time (in seconds)
                    delay: 5,
                    // default position
                    position: 'bottom-right'
                },

                // language resources
                glossary: {
                    // dialogs default title
                    title: 'Bonaire',
                    // ok button text
                    ok: this.alertifyNotifications.ok,
                    // cancel button text
                    cancel: this.alertifyNotifications.cancel
                },

                // theme settings
                theme: {
                    // class name attached to prompt dialog input textbox.
                    input: 'ajs-input',
                    // class name attached to ok button
                    ok: 'ajs-ok',
                    // class name attached to cancel button
                    cancel: 'ajs-cancel'
                }
            };
            $('.alertify').hide();
        },
        addEvents: function (){
            // Dashboard Widget
            this.bonaireMarkAsReadButton.bind('click', {context: this, element: this.bonaireMarkAsReadButton}, this.bonaireMarkAsRead);
            this.markAsSpamButton.bind('click', {context: this}, this.bonaireMarkAsSpam);
            this.moveToTrashButton.bind('click', {context: this}, this.bonaireMoveToTrash);
            this.handleDiv.bind('click', this.bonaireToggleWidgetContent);
            // Meta Box
            this.manageHandleDivs();
            this.bonaireReplyButton.bind('click', {context: this}, this.bonaireReply);
            // Options Page
            this.bonaireSaveOptionsButton.bind('click', {context: this}, this.bonaireSaveOptions);
            this.bonaireResetOptionsButton.bind('click', {context: this}, this.bonaireResetOptions);
            this.bonaireSendTestMailButton.bind('click', {context: this}, this.promptForEmailAddress);
            this.bonaireTestContactFormButton.bind('click', {context: this, protocol: 'cf7'}, this.bonaireEvaluateSettings);
            this.bonaireTestSmtpSettingsButton.bind('click', {context: this, protocol: 'smtp'}, this.bonaireEvaluateSettings);
            this.bonaireTestImapSettingsButton.bind('click', {context: this, protocol: 'imap'}, this.bonaireEvaluateSettings);
            this.bonaireOptionsFormInputFields.bind('focus', {context: this}, this.unsetInvalidInputFieldIndicator);
            this.helpButton.bind('click', {context: this}, function (){
                $('.button.show-settings').trigger('click');
            });
        },
        // Dashboard
        bonaireMarkAsRead: function ( event ){
            var $this = event.data.context;
            var element = $(this);

            var data = {
                action: $this.markAsReadAction,
                post_id: element.data('postid'),
                nonce: element.data('nonce')
            };

            $.post(ajaxurl, data, function ( response ){

                if (response.success === true){

                    alertify.notify(response.data.message);
                    $(element).parents('li').detach();
                    $this.updateWidgetDisplay();
                } else{
                    alertify.error(response.data.message);
                }
            });

        },
        bonaireMarkAsSpam: function ( event ){
            var $this = event.data.context;
            var element = $(this);

            var data = {
                action: $this.markAsSpamAction,
                post_id: element.data('postid'),
                nonce: element.data('nonce')
            };

            $.post(ajaxurl, data, function ( response ){

                if (response.success === true){

                    alertify.notify(response.data.message);
                    $(element).parents('li').detach();
                    $this.updateWidgetFooter('spam');
                    $this.updateWidgetDisplay();
                } else{
                    alertify.error(response.data.message);
                }
            });

        },
        bonaireMoveToTrash: function ( event ){
            var $this = event.data.context;
            var element = $(this);

            var data = {
                action: $this.moveToTrashAction,
                post_id: element.data('postid'),
                nonce: element.data('nonce')
            };

            $.post(ajaxurl, data, function ( response ){

                if (response.success === true){

                    alertify.notify(response.data.message);
                    $(element).parents('li').detach();
                    $this.updateWidgetFooter('trash');
                    $this.updateWidgetDisplay();
                } else{
                    alertify.error(response.data.message);
                }
            });

        },
        updateWidgetFooter: function ( location ){
            var element = $('.' + location + ' .' + location + '-count');
            var val = parseInt(element.html()) + 1;
            element.html(val);
        },
        bonaireToggleWidgetContent: function (){
            var postbox = $(this).parents('.postbox');
            var inside = postbox.find('.inside');

            if (postbox.hasClass('closed')){
                inside.hide();
            } else{
                inside.show();
            }
        },
        updateWidgetDisplay: function (){
            var inside = $('#bonaire_dashboard_widget > .inside');
            var list = inside.find('ul:first-of-type');
            if (list.find('li').length === 0){
                inside.prepend(this.noMessagesMessage);
            }
        },
        // Help Tab
        initContextualHelp: function (){
            $("#bonaire-help-tabs").tabs();
        },
        // Settings Page
        bonaireSaveOptions: function ( event ){
            event.preventDefault();
            var $this = event.data.context;

            var input = {};
            var output;
            // Retrieve values
            $.each($($this.bonaireOptionsFormInputFields), function (){
                if ('bonaire' === $(this).data('form-input')){
                    input[$(this).data('key')] = $(this).val();
                }
            });
            output = $this.bonairePreValidateOptions(input);

            if (false !== output){
                $this.bonaireSubmitOptions(output);
            } else{
                alertify.alert($this.optionsPageNotifications.save_options_notice);
            }

        },
        bonaireSubmitOptions: function ( input ){

            var $this = this;

            var data = {
                action: this.bonaireSaveOptionsAction,
                nonce: this.bonaireOptionsForm.data("nonce")
            };
            $.extend(data, input);

            this.showLoaderSpinner();

            $.post(ajaxurl, data, function ( response ){

                $this.hideLoaderSpinner();

                // @todo fix
                if (undefined === response.data){
                    response = $.parseJSON(response);
                }
                if (response.success === true){

                    $this.bonaireUpdateSettingsStatus();
                    
                    var password = $('input[name="bonaire_options[password]"]');
                    if ('' !== password.val() && '*****' !== password.val()){
                        password.val('*****');
                    }
                    alertify.success(response.data.message);
                } else{
                    alertify.success(response.data.message);
                }
            });
        },
        bonaireUpdateSettingsStatus: function(){
            var $this = this;

            var data = {
                action: this.bonaireUpdateSettingsStatusAction,
                nonce: this.bonaireOptionsForm.data("nonce")
            };

            $.post(ajaxurl, data, function ( response ){

                if (undefined === response.data){
                    response = $.parseJSON(response);
                }
                if (response.success === true){

                    var cf7_status = response.data.cf7_status;
                    if (undefined !== cf7_status){
                        $this.updateSettingsStatus('cf7', cf7_status);
                    }
                    var smtp_status = response.data.smtp_status;
                    if (undefined !== smtp_status){
                        $this.updateSettingsStatus('smtp', smtp_status);
                    }
                    var imap_status = response.data.imap_status;
                    if (undefined !== imap_status){
                        $this.updateSettingsStatus('imap', imap_status);
                    }
                }
            });
        },
        getSettingsStatus: function(protocol){

            var data = {
                action: this.bonaireUpdateSettingsStatusAction,
                nonce: this.bonaireOptionsForm.data("nonce")
            };

            var result = null;
            $.post(ajaxurl, data, function ( response ){

                if (undefined === response.data){
                    response = $.parseJSON(response);
                }
                if (response.success === true){

                    if('smtp' === protocol){
                        result = response.data.smtp_status;
                    } else if('imap' === protocol){
                        result = response.data.imap_status;
                    }else {
                        result = response.data.cf7_status;
                    }

                    return result;
                }

                return false;
            });
        },
        bonaireResetOptions: function ( event ){
            event.preventDefault();
            var $this = event.data.context;
            var element = this;

            alertify.set('confirm', 'title', $this.resetOptionsConfirmation.title);
            alertify.confirm($this.resetOptionsConfirmation.text, function (){
                var data = {
                    action: $this.resetOptionsAction,
                    nonce: $(element).data('nonce')
                };

                $this.showLoaderSpinner();

                $.post(ajaxurl, data, function ( response ){

                    $this.hideLoaderSpinner();

                    // @todo fix
                    if (undefined === response.data){
                        response = $.parseJSON(response);
                    }
                    if (response.success === true){
                        $this.bonaireUpdateSettingsStatus();
                        alertify.success(response.data.message);
                        $this.resetOptionsPageInputFields();
                    } else{
                        alertify.alert(response.data.message);
                    }
                });
            });
        },
        bonaireEvaluateSettings: function ( event ){
            event.preventDefault();
            var $this = event.data.context;
            var protocol = event.data.protocol;
            var data = {};

            if ('smtp' === protocol){

                data = {
                    action: $this.testSmtpSettingsAction
                };
            } else if('imap' === protocol){
                data = {
                    action: $this.testImapSettingsAction
                };
            } else {
                data = {
                    action: $this.testContactFormAction
                };
            }
            $.extend(data, {nonce: $(this).data('nonce')});

            $this.showLoaderSpinner();

            $.post(ajaxurl, data, function ( response ){
                // @todo fix
                if (undefined === response.data){
                    response = $.parseJSON(response);
                }

                if (response.success === true){
                    alertify.alert(response.data.message);
                } else{
                    alertify.alert(response.data.message);
                }

                $this.hideLoaderSpinner();

                $this.bonaireUpdateSettingsStatus();
            });
        },
        promptForEmailAddress: function ( event, error ){
            event.preventDefault();
            var $this = event.data.context;

            if (false === $this.hasAppropriateSettingsStatus()){
                alertify.notify($this.optionsPageNotifications.send_test_mail_notice);
            } else{

                var promptTitle = true === error ? $this.optionsPageNotifications.send_test_mail_prompt_review_email_title : $this.optionsPageNotifications.send_test_mail_prompt_title;
                alertify.prompt(promptTitle, 'Please enter Email', 'Enter Email',
                    function ( evt, value ){
                        var data = {
                            action: $this.sendTestMailAction,
                            nonce: $('.bonaire-send-testmail-button').data('nonce'),
                            value: value
                        };
                        $this.bonaireSendTestMail(event, data);
                    },
                    function (){
                        alertify.notify('Sending test message canceled.')
                    }
                );
            }
        },
        bonaireSendTestMail: function ( event, data ){
            event.preventDefault();
            var $this = event.data.context;

            $this.showLoaderSpinner();

            $.post(ajaxurl, data, function ( response ){

                $this.hideLoaderSpinner();

                // @todo fix
                if (undefined === response.data){
                    response = $.parseJSON(response);
                }
                if (response.success === true){
                    alertify.success(response.data.message);
                } else if (response.success === false && 'email_not_valid' === response.data.error){
                    $this.promptForEmailAddress(event, true);
                } else{
                    alertify.alert(response.data.message);
                }
            });
        },
        // Helper Functions
        updateSettingsStatus: function ( protocol, status ){
            var el = $('.' + protocol).find('.status-indicator');
            $(el).removeClass('red orange green inactive').addClass(status);
            $(el).find('i').prop('title', this.settingsPageDisplayStrings[protocol][status]);
        },
        hasAppropriateSettingsStatus: function (){

            // Check for account settings status
            var smtpStatus = BonaireOptions.smtp_status;
            var imapStatus = BonaireOptions.imap_status;
            var saveReply  = BonaireOptions.save_reply;

            return 'yes' === saveReply ? '1' === imapStatus : '1' === smtpStatus;
        },
        resetOptionsPageInputFields: function (){

            $.each(this.bonaireOptionsFormInputFields, function (){
                if ('bonaire' === $(this).data('form-input')){
                    var dataValue = $(this).data('default-value');

                    $(this).val(dataValue);
                    if ('smtpsecure' === $(this).data('key')){
                        $(this).val('ssl');
                    }
                    if ('save_reply' === $(this).data('key')){
                        $(this).val('no');
                    }
                    if ('channel' === $(this).data('key')){
                        $(this).val('none');
                    }
                }
            });
        },
        bonairePreValidateOptions: function ( data ){

            // noinspection Annotator
            var urlRegex = new RegExp(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[a-z0-9]+([\-.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i);

            var emailRegex = RegExp(/^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i);

            var alphaNumRegex = new RegExp(/^[A-Za-z0-9 _.-]+$/);

            var $this = this;
            var output = {};
            var errors = {};
            $.each(data, function ( key ){
                var result = null;

                if ('form_id' === key ){
                    result = '' !== data.channel ? data.channel.replace('_', '') : '';
                }
                if ('number_posts' === key){
                    result = $.isNumeric(parseInt(this)) ? this.toString() : false;
                    if (false !== result){
                        result = this;
                    } else{
                        result = '';
                    }
                }
                if ('smtp_host' === key || 'imap_host' === key){
                    result = urlRegex.test(this);
                    if (true === result){
                        result = this.toLowerCase();
                    } else{
                        result = '';
                    }
                }
                if ('smtpauth' === key){
                    result = true;
                }
                if ('save_reply' === key || 'password' === key || 'channel' === key || 'ssl_certification_validation' === key || 'inbox_folder_name' === key || 'inbox_folder_path' === key){
                    result = this;
                }
                if ('smtp_port' === key || 'imap_port' === key){
                    result = $.isNumeric(parseInt(this)) ? parseInt(this) : false;
                    if (false !== result){
                        result = this;
                    } else{
                        result = '';
                    }
                }
                if ('username' === key){
                    if (this.indexOf("@") >= 0){
                        result = emailRegex.test(this);
                    } else{
                        result = alphaNumRegex.test(this);
                    }
                    if (true === result){
                        result = this;
                    } else{
                        result = '';
                    }
                }
                if ('smtpsecure' === key || 'imapsecure' === key){
                    result = null;
                    if ('ssl' === this || 'tls' === this){
                        result = this.toLowerCase();
                    } else{
                        result = 'ssl';
                    }
                }
                if ('from' === key){
                    result = emailRegex.test(this);
                    if (true === result){
                        result = this.toLowerCase();
                    } else{
                        result = '';
                    }
                }
                if ('fromname' === key){
                    result = alphaNumRegex.test(this);
                    if (true === result){
                        result = this;
                    } else{
                        result = '';
                    }
                }
                if ('your_name' === key || 'your_email' === key || 'your_subject' === key || 'your_message' === key){
                    result = alphaNumRegex.test(this);
                    if (true === result){
                        result = this;
                    } else{
                        result = '';
                    }
                }

                output[key] = result;
            });

            if (false === $.isEmptyObject(errors)){

                $this.bonaireShowValidationErrors(errors);

                return false;
            } else{

                return output;
            }
        },
        bonaireShowValidationErrors: function ( errors ){
            var $this = this;

            alertify.set('notifier', 'position', 'bottom-right');
            alertify.set('notifier', 'delay', 1000);
            $this.notification = alertify.error($this.bonaireGetValidationErrors(errors));
            $this.notification.ondismiss = function (){
                $this.bonaireFailedValidationCallback(errors);
            };
            $this.body.one('click', function (){
                $this.notification.dismiss();
            });
        },
        bonaireGetValidationErrors: function ( errors ){

            var string = '<div class="message-wrap mailinvalid-wrap">';
            string += '<h3>' + this.alertifyError.title + '</h3>';
            $.each(errors, function (){
                string += '<div class="error-container"><strong>' + this.name + '</strong><span>' + this.error_message + '</span><span>(' + this.example + ')</span></div>';
            });
            string += '</div>';

            return string;
        },
        bonaireFailedValidationCallback: function ( errors ){
            var delay_offset = 162;

            $.each(errors, function ( i ){
                var delay = i * delay_offset;
                $("input[name='bonaire_options[" + this.id + "]']").delay(delay).queue(function ( next ){
                    $(this).addClass("has-error");
                    next();
                });
            });
        },
        unsetInvalidInputFieldIndicator: function (){
            $(this).removeClass('has-error');
        },
        // Flamingo Inbound
        bonaireReply: function ( event ){
            event.preventDefault();
            var $this = event.data.context;

            if (true === $this.hasEmptyField){

                alertify.notify('<div>' + $this.replyError.title + '</div><div><span>' + $this.replyError.text + '</span><a href="' + $this.replyError.link + '">' + $this.replyError.link_text + '</a></div>');
            } else if ($('[data-key="message"]').val() === ''){

                alertify.notify('<div>' + $this.emptyMessageError.title + '</div><div><span>' + $this.emptyMessageError.text + '</span></div>');
            } else{

                var inputFields = $this.bonaireReplyForm.find('input:not(.bonaire-submit-reply-button), textarea');
                var input = {};
                $.each($(inputFields), function (){
                    if ('bonaire' === $(this).data('form-input')){
                        input[$(this).data('key')] = $(this).val();
                    }
                });

                $this.bonaireSubmitReply(input);
            }
        },
        bonaireSubmitReply: function ( input ){

            var $this = this;

            var data = {
                action: this.bonaireReplyAction,
                nonce: this.bonaireReplyForm.data("nonce")
            };
            $.extend(data, input);

            this.showLoaderSpinner();

            $.post(ajaxurl, data, function ( response ){

                $this.hideLoaderSpinner();

                // @todo fix
                if (undefined === response.data){
                    response = $.parseJSON(response);
                }
                if (response.success === true){

                    alertify.success(response.data.message);
                    $this.bonaireReplyForm.find("input[data-key='name']").val(BonaireOptions.from_name);
                    $this.bonaireReplyForm.find("textarea, input[type='subject']").val('RE: ');
                    $this.bonaireReplyForm.find("textarea, input[type='text']").val('');
                } else{
                    alertify.alert(response.data.message);
                }
            });
        },
        // Helper Functions
        showLoaderSpinner: function (){
            this.loaderSpinner.addClass('is-active');
            this.loaderSpinner.fadeIn(162);
        },
        hideLoaderSpinner: function (){
            this.loaderSpinner.fadeOut(162);
            this.loaderSpinner.removeClass('is-active');
        },
        manageHandleDivs: function(){

            if ('1' === BonaireOptions.manage_handle_divs){
                $('#inboundfieldsdiv, #inboundmetadiv, #inboundconsentdiv').addClass('closed');
                $('#bonaire-message-meta-box, #bonaire-reply-meta-box').removeClass('closed');
            } else {
                $('#bonaire-message-meta-box, #inboundmetadiv, #inboundconsentdiv').addClass('closed');
                $('#inboundfieldsdiv, #bonaire-reply-meta-box').removeClass('closed');
            }
        }

    };

    $(document).one('ready', function (){

        var bonaireAdmin = new BonaireAdmin();
        bonaireAdmin.init();
    });

})(jQuery, alertify);
