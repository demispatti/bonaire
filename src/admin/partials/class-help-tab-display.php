<?php

namespace Bonaire\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the help tab.
 *
 * @since             1.0.0
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Help_Tab_Display {
	
	/**
	 * Returns a string containing the 'Help Tab' content.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function help_tab_display( $domain ) {
		
		ob_start();
		?>

        <div id="bonaire-help-tabs">
            <ul class="nav">
                <li><a href="#tabs-1"><?php echo __( 'Prerequisites', $domain ) ?></a></li>
                <li><a href="#tabs-2"><?php echo __( 'Plugin Settings', $domain ) ?></a></li>
                <li><a href="#tabs-3"><?php echo __( 'Contact Form 7 Settings', $domain ) ?></a></li>
                <li><a href="#tabs-4"><?php echo __( 'Dashboard Widget', $domain ) ?></a></li>
                <li><a href="#tabs-5"><?php echo __( 'Reply Form', $domain ) ?></a></li>
                <li><a href="#tabs-6"><?php echo __( 'Plugin Information and Privacy Notices', $domain ) ?></a></li>
            </ul>
            <div id="tabs-1"><?php echo self::tab_content_prerequisites( $domain ) ?></div>
            <div id="tabs-2"><?php echo self::tab_content_plugin_settings( $domain ) ?></div>
            <div id="tabs-3"><?php echo self::tab_content_contact_form_7_settings( $domain ) ?></div>
            <div id="tabs-4"><?php echo self::tab_content_dashboard_widget( $domain ) ?></div>
            <div id="tabs-5"><?php echo self::tab_content_reply_form( $domain ) ?></div>
            <div id="tabs-6"><?php echo self::tab_content_plugin_information_and_privacy_notices( $domain ) ?></div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function tab_content_prerequisites( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            <h5><?php echo __( 'Prerequisites', $domain ) ?></h5>
            <ul class="list">
                <li>1. <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Cotact Form 7</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?></li>
                <li>2. <a href="https://wordpress.org/plugins/flamingo/" target="_blank">Flamingo</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?></li>
                <li>3. <?php echo __( 'For full functionality, you need to have received some messages via Flamingo since plugin installation.', $domain ) ?></li>
            </ul>
            <h5><?php echo __( 'Naming conventions', $domain ) ?></h5>
            <span><?php echo __( 'In order to function propperly, please make sure that you do not use "Mail 2" option in Contact Form 7, and that the default input fields keep their default names:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'your-name', $domain ) ?></li>
                <li>2. <?php echo __( 'your-email', $domain ) ?></li>
                <li>3. <?php echo __( 'your-subject', $domain ) ?></li>
                <li>4. <?php echo __( 'your-message', $domain ) ?></li>
            </ul>
        </div>
        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-naming-conventions-small.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function tab_content_plugin_settings( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            <p><?php echo __( 'You can send replies to messages you recieve trough Contact Form 7 and got stored by Flamingo. Register the email account that is
            related to it in order to send replies and to save your reply in your mailserver\'s "Sent Items" folder.', $domain ) ?>
            </p>
            <span class="info">
            <?php echo __( 'As an example, you find the values for a Gmail account on the respective tooltip next to the input field.', $domain ) ?>
        </span>
            <h5><?php echo __( 'Prerequisites', $domain ) ?></h5>
            <ul class="list">
                <li>1. <?php echo __( 'your-name', $domain ) ?></li>
                <li>2. <?php echo __( 'your-email', $domain ) ?></li>
                <li>3. <?php echo __( 'your-subject', $domain ) ?></li>
                <li>4. <?php echo __( 'your-message', $domain ) ?></li>
            </ul>
            <h5><?php echo __( 'Privacy notices', $domain ) ?></h5>
            <span><?php echo __( 'With the default configuration, this plugin, does not:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'Track users', $domain ) ?></li>
                <li>2. <?php echo __( 'Write personal user data to the database other than the necessary email account settings, and attaching the senders email
                address to the messages meta data, which is necessary to link the message to the email account in use.', $domain ) ?>
                </li>
                <li>2. <?php echo __( 'Send any data to external servers other than your reply and/or the data necessary to reach, connect and authenticate to
                the mail server. Once while sending it to it\'s recipient, and once to store it in your mail server\'s "sent items" folder if you
                choose to do so. The original message will not be attached and sent by this plugin, in both cases not.', $domain ) ?>
                </li>
                <li>4. <?php echo __( 'Use cookies', $domain ) ?></li>
            </ul>
        </div>
        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-plugin-settings-default-web.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-plugin-settings-gmail-web.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function tab_content_contact_form_7_settings( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            <p><?php echo __( 'You can send replies to messages you recieve trough Contact Form 7 and got stored by Flamingo. Register the email account that is
            related to it in order to send replies and to save your reply in your mailserver\'s "Sent Items" folder.', $domain ) ?></p>
            <span
                class="info"><?php echo __( 'As an example, you find the values for a Gmail account on the respective tooltip next to the input field.', $domain ) ?></span>
            <h5><?php echo __( 'Prerequisites', $domain ) ?></h5>
            <ul class="list">
                <li>1. <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Cotact Form
                        7</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?>
                </li>
                <li>2. <a href="https://wordpress.org/plugins/flamingo/"
                        target="_blank">Flamingo</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?>
                </li>
                <li>
                    3. <?php echo __( 'For full functionality, you need to have received some messages via Flamingo since plugin installation.', $domain ) ?></li>
            </ul>
            <h5><?php echo __( 'Naming conventions', $domain ) ?></h5>
            <span><?php echo __( 'In order to function propperly, please make sure that you do not use "Mail 2" option in Contact Form 7, and that the default input fields keep their default names:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'your-name', $domain ) ?></li>
                <li>2. <?php echo __( 'your-email', $domain ) ?></li>
                <li>3. <?php echo __( 'your-subject', $domain ) ?></li>
                <li>4. <?php echo __( 'your-message', $domain ) ?></li>
            </ul>
            <span><button>Showme</button></span>
            <span><button>Showme</button></span>
            <h5><?php echo __( 'Privacy notices', $domain ) ?></h5>
            <span><?php echo __( 'With the default configuration, this plugin, does not:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'Track users', $domain ) ?></li>
                <li>2. <?php echo __( 'Write personal user data to the database other than the necessary email account settings, and attaching the senders email
                address to the messages meta data, which is necessary to link the message to the email account in use.', $domain ) ?>
                </li>
                <li>2. <?php echo __( 'Send any data to external servers other than your reply and/or the data necessary to reach, connect and authenticate to
                the mail server. Once while sending it to it\'s recipient, and once to store it in your mail server\'s "sent items" folder if you
                choose to do so. The original message will not be attached and sent by this plugin, in both cases not.', $domain ) ?>
                </li>
                <li>4. <?php echo __( 'Use cookies', $domain ) ?></li>
            </ul>
        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-wpcf7-config-1-small.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-wpcf7-config-mail-2-small.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function tab_content_dashboard_widget( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            <p><?php echo __( 'You can send replies to messages you recieve trough Contact Form 7 and got stored by Flamingo. Register the email account that is
            related to it in order to send replies and to save your reply in your mailserver\'s "Sent Items" folder.', $domain ) ?></p>
            <span
                class="info"><?php echo __( 'As an example, you find the values for a Gmail account on the respective tooltip next to the input field.', $domain ) ?></span>
            <h5><?php echo __( 'Prerequisites', $domain ) ?></h5>
            <ul class="list">
                <li>1. <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Cotact Form
                        7</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?>
                </li>
                <li>2. <a href="https://wordpress.org/plugins/flamingo/"
                        target="_blank">Flamingo</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?>
                </li>
                <li>
                    3. <?php echo __( 'For full functionality, you need to have received some messages via Flamingo since plugin installation.', $domain ) ?></li>
            </ul>
            <h5><?php echo __( 'Naming conventions', $domain ) ?></h5>
            <span><?php echo __( 'In order to function propperly, please make sure that you do not use "Mail 2" option in Contact Form 7, and that the default input fields keep their default names:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'your-name', $domain ) ?></li>
                <li>2. <?php echo __( 'your-email', $domain ) ?></li>
                <li>3. <?php echo __( 'your-subject', $domain ) ?></li>
                <li>4. <?php echo __( 'your-message', $domain ) ?></li>
            </ul>
            <span><button>Showme</button></span>
            <span><button>Showme</button></span>
            <h5><?php echo __( 'Privacy notices', $domain ) ?></h5>
            <span><?php echo __( 'With the default configuration, this plugin, does not:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'Track users', $domain ) ?></li>
                <li>2. <?php echo __( 'Write personal user data to the database other than the necessary email account settings, and attaching the senders email
                address to the messages meta data, which is necessary to link the message to the email account in use.', $domain ) ?>
                </li>
                <li>2. <?php echo __( 'Send any data to external servers other than your reply and/or the data necessary to reach, connect and authenticate to
                the mail server. Once while sending it to it\'s recipient, and once to store it in your mail server\'s "sent items" folder if you
                choose to do so. The original message will not be attached and sent by this plugin, in both cases not.', $domain ) ?>
                </li>
                <li>4. <?php echo __( 'Use cookies', $domain ) ?></li>
            </ul>
        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-dashboard-widget-demo-small.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function tab_content_reply_form( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            <p><?php echo __( 'You can send replies to messages you recieve trough Contact Form 7 and got stored by Flamingo. Register the email account that is
            related to it in order to send replies and to save your reply in your mailserver\'s "Sent Items" folder.', $domain ) ?></p>
            <span
                class="info"><?php echo __( 'As an example, you find the values for a Gmail account on the respective tooltip next to the input field.', $domain ) ?></span>
            <h5><?php echo __( 'Prerequisites', $domain ) ?></h5>
            <ul class="list">
                <li>1. <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Cotact Form
                        7</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?>
                </li>
                <li>2. <a href="https://wordpress.org/plugins/flamingo/"
                        target="_blank">Flamingo</a> <?php echo __( 'needs to be installed and activated.', $domain ) ?>
                </li>
                <li>
                    3. <?php echo __( 'For full functionality, you need to have received some messages via Flamingo since plugin installation.', $domain ) ?></li>
            </ul>
            <h5><?php echo __( 'Naming conventions', $domain ) ?></h5>
            <span><?php echo __( 'In order to function propperly, please make sure that you do not use "Mail 2" option in Contact Form 7, and that the default input fields keep their default names:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'your-name', $domain ) ?></li>
                <li>2. <?php echo __( 'your-email', $domain ) ?></li>
                <li>3. <?php echo __( 'your-subject', $domain ) ?></li>
                <li>4. <?php echo __( 'your-message', $domain ) ?></li>
            </ul>
            <span><button>Showme</button></span>
            <span><button>Showme</button></span>
            <h5><?php echo __( 'Privacy notices', $domain ) ?></h5>
            <span><?php echo __( 'With the default configuration, this plugin, does not:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'Track users', $domain ) ?></li>
                <li>2. <?php echo __( 'Write personal user data to the database other than the necessary email account settings, and attaching the senders email
                address to the messages meta data, which is necessary to link the message to the email account in use.', $domain ) ?>
                </li>
                <li>2. <?php echo __( 'Send any data to external servers other than your reply and/or the data necessary to reach, connect and authenticate to
                the mail server. Once while sending it to it\'s recipient, and once to store it in your mail server\'s "sent items" folder if you
                choose to do so. The original message will not be attached and sent by this plugin, in both cases not.', $domain ) ?>
                </li>
                <li>4. <?php echo __( 'Use cookies', $domain ) ?></li>
            </ul>
        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-reply-form-demo-small.jpg' ?>);"
                        alt="Contextual Help Image"/>
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the content of this 'Help Tab' tab.
	 *
	 * @param string $domain
	 *
	 * @since 1.0.0
	 * @return string $html
	 */
	public static function tab_content_plugin_information_and_privacy_notices( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
            <h5><?php echo __( 'Privacy notices', $domain ) ?></h5>
            <span><?php echo __( 'With the default configuration, this plugin does not:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php echo __( 'Track users', $domain ) ?></li>
                <li>2. <?php echo __( 'Write personal user data to the database other than the necessary email account settings, and attaching the senders email
                address to the messages meta data, which is necessary to link the message to the email account in use.', $domain ) ?>
                </li>
                <li>3. <?php echo __( 'Send any data to external servers other than your reply and/or the data necessary to reach, connect and authenticate to
                the mail server. Once while sending it to it\'s recipient, and once to store it in your mail server\'s "sent items" folder if you
                choose to do so. The original message will not be attached and sent by this plugin, in both cases not.', $domain ) ?>
                </li>
                <li>4. <?php echo __( 'Use cookies', $domain ) ?></li>
            </ul>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
