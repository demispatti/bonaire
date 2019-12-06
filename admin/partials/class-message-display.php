<?php

namespace Bonaire\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the metabox containing the reply form.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Message_Display {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   public static
	 */
	public static $domain;
	
	/**
	 * Bonaire_Reply_Form_Display constructor.
	 *
	 * @param string $domain
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain ) {
		
		self::$domain = $domain;
	}
	
	/**
	 * Returns a string containing the reply form.
	 *
	 * @param string $your_subject
	 * @param string $your_email
	 * @param object $account_settings
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function message_display( $your_name, $your_email, $your_subject, $your_message ) {
		
		//$name    = $account_settings->fromname;
		//$subject = 'RE: ' . $your_subject;
		//$nonce   = wp_create_nonce( 'bonaire_reply_form_nonce' );
		
		ob_start();
		?>
        <!-- a fix, maybe @todo if not wp-related... -->
        <form></form>
        <div class="bonaire-message-container">
            <fieldset id="bonaire_message">
                <!--<div>
                    <label for="name"><?php /*esc_html_e( 'From', self::$domain ) */?></label>
                    <input disabled type="text" name="name" data-key="name" data-form-input="bonaire"
                        title="<?php /*esc_html_e( 'Name', self::$domain ) */?>"
                        value="<?php /*esc_html_e( $your_name ) */?>">
                </div>
                <div>
                    <label for="email"><?php /*esc_html_e( 'Email', self::$domain ) */?></label>
                    <input disabled type="email" name="email" data-key="email" data-form-input="bonaire"
                        title="<?php /*esc_html_e( 'Email', self::$domain ) */?>"
                        value="<?php /*echo sanitize_email( $your_email ) */?>" disabled>
                </div>
                <div>
                    <label for="subject"><?php /*esc_html_e( 'Subject', self::$domain ) */?></label>
                    <input disabled type="text" name="subject" data-key="subject" data-form-input="bonaire"
                        title="<?php /*esc_html_e( 'Subject', self::$domain ) */?>" value="<?php /*echo $your_subject */?>">
                </div>-->
                <div>
                    <!--<label for="textarea"><?php /*esc_html_e( 'Message', self::$domain ) */?></label>-->
                    <textarea disabled name="textarea" id="" data-key="message" data-form-input="bonaire"
                        title="<?php esc_html_e( 'Message', self::$domain ) ?>"
                        cols="30" rows="10"><?php echo $your_message ?></textarea>
                </div>
            </fieldset>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
