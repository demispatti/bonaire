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
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 * @todo              Hilfe-Texte
 */
class Bonaire_Help_Tab_Display {
	
	/**
	 * Returns a string containing the 'Help Tab' content.
	 *
	 * @param string $domain
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function help_tab_display( $domain ) {
		
		ob_start();
		?>

        <div id="bonaire-help-tabs">
            <ul class="nav">
                <li><a href="#tabs-1"><?php esc_html_e( 'Prerequisites', $domain ) ?></a></li>
                <li><a href="#tabs-2"><?php esc_html_e( 'Plugin Settings', $domain ) ?></a></li>
                <li><a href="#tabs-3"><?php esc_html_e( 'Contact Form 7 Settings', $domain ) ?></a></li>
                <li><a href="#tabs-4"><?php esc_html_e( 'Dashboard Widget', $domain ) ?></a></li>
                <li><a href="#tabs-5"><?php esc_html_e( 'Reply Form', $domain ) ?></a></li>
                <li><a href="#tabs-6"><?php esc_html_e( 'Tooltips', $domain ) ?></a></li>
                <li><a href="#tabs-7"><?php esc_html_e( 'Plugin Information and Privacy Notices', $domain ) ?></a></li>
            </ul>
            <div id="tabs-1"><?php echo self::tab_content_prerequisites( $domain ) ?></div>
            <div id="tabs-2"><?php echo self::tab_content_plugin_settings( $domain ) ?></div>
            <div id="tabs-3"><?php echo self::tab_content_contact_form_7_settings( $domain ) ?></div>
            <div id="tabs-4"><?php echo self::tab_content_dashboard_widget( $domain ) ?></div>
            <div id="tabs-5"><?php echo self::tab_content_reply_form( $domain ) ?></div>
            <div id="tabs-6"><?php echo self::tab_content_tooltips( $domain ) ?></div>
            <div id="tabs-7"><?php echo self::tab_content_plugin_information_and_privacy_notices( $domain ) ?></div>
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_prerequisites( $domain ) {
		
	    $link = '<a href="https://blog.cf7skins.com/matching-mail-tags-with-form-tags-in-contact-form-7/" target="_blank">' . __( 'Post', $domain ) . '</a>';
	    
		ob_start();
		?>

        <div class="item-description">
            <h5><?php esc_html_e( 'Prerequisites', $domain ) ?></h5>
            <ul class="list">
                <li>1. <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Cotact Form
                        7</a> <?php esc_html_e( 'needs to be installed and activated.', $domain ) ?></li>
                <li>2. <a href="https://wordpress.org/plugins/flamingo/"
                        target="_blank">Flamingo</a> <?php esc_html_e( 'needs to be installed and activated.', $domain ) ?></li>
                <li>
                    3. <?php esc_html_e( 'For full functionality, you need to have received some messages via Flamingo since plugin installation.', $domain ) ?></li>
            </ul>
            <h5><?php esc_html_e( 'Naming conventions', $domain ) ?></h5>
            <span><?php esc_html_e( 'In order to function propperly, please make sure that you do not use "Mail 2" option in Contact Form 7, and that the default input fields keep their default names:', $domain ) ?></span>
            <ul class="list">
                <li>1. <?php esc_html_e( 'your-name', $domain ) ?></li>
                <li>2. <?php esc_html_e( 'your-email', $domain ) ?></li>
                <li>3. <?php esc_html_e( 'your-subject', $domain ) ?></li>
                <li>4. <?php esc_html_e( 'your-message', $domain ) ?></li>
            </ul>
            <span><?php echo sprintf( __('See this %s for more details on how to set up Contact Form 7.', $domain), $link) ?></span>
        </div>
        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-naming-conventions-small.jpg' ?>"
                        alt="Contextual Help Image"/>
                </div>
            </div>
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-wpcf7-config-mail-2-small.jpg' ?>"
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_plugin_settings( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">

        </div>
        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-plugin-settings-default-web.jpg' ?>"
                        alt="Contextual Help Image"/>
                </div>
            </div>
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-plugin-settings-gmail-web.jpg' ?>"
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_contact_form_7_settings( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">

        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-wpcf7-config-1-small.jpg' ?>"
                        alt="Contextual Help Image"/>
                </div>
            </div>
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-wpcf7-config-mail-2-small.jpg' ?>"
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_dashboard_widget( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">

        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-dashboard-widget-demo-small.jpg' ?>"
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_reply_form( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">

        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-reply-form-demo-small.jpg' ?>"
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_tooltips( $domain ) {
		
		ob_start();
		?>

        <div class="item-description">
			<?php esc_html_e( 'Make sure to read trough the included tooltips!', $domain ) ?>
        </div>

        <div class="item-images">
            <div>
                <div class="image-holder">
                    <img src="<?php echo BONAIRE_ROOT_URL . 'admin/images/contextual-help/ch-read-tooltips.jpg' ?>"
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
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function tab_content_plugin_information_and_privacy_notices( $domain ) {
		
	    $cf7_link = '<a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a>';
	    $flamingo_link = '<a href="https://wordpress.org/plugins/flamingo/" target="_blank">Flamingo</a>';
	    
		ob_start();
		?>

        <div class="item-description">
            <p><?php esc_html_e( printf( __('Reply to messages you recieve trough a %s contact form and store with %s.', $domain ), $cf7_link, $flamingo_link)) ?> <?php esc_html_e( 'Register the email account that is related to the contct form in order to send replies and to save your reply in your mailserver\'s "Sent Items" folder.', $domain ) ?>
            </p>

            <h5><?php esc_html_e( 'What this plugin does:', $domain ) ?></h5>
            <ul class="list">
                <li>3. <?php esc_html_e( 'Adds a settings page.', $domain ) ?></li>
                <li>1. <?php esc_html_e( 'Adds a reply form at the bottom of a single message.', $domain ) ?></li>
                <li>2. <?php esc_html_e( 'Adds a widget to the dashboard.', $domain ) ?></li>
            </ul>
            <span class="info">
            </span>
            <h5><?php esc_html_e( 'This plugin does not:', $domain ) ?></h5>
            <ul class="list">
                <li>1. <?php esc_html_e( 'Track users', $domain ) ?></li>
                <li>2. <?php esc_html_e( 'Write personal user data to the database other than the necessary email account settings, and attaching the senders email address to the messages meta data, which is necessary to link the message to the email account in use.', $domain ) ?></li>
                <li>3. <?php esc_html_e( 'Send any data to external servers other than your reply and/or the data necessary to reach, connect and authenticate to the mail server. Once while sending it to it\'s recipient, and once to store it in your mail server\'s "sent items" folder if you choose to do so. The original message will not be attached and sent by this plugin, in both cases not.', $domain ) ?></li>
                <li>4. <?php esc_html_e( 'Use cookies', $domain ) ?></li>
            </ul>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
