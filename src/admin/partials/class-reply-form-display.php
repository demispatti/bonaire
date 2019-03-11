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
 * @since             0.9.0
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Reply_Form_Display {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.0
	 * @access   public static
	 */
	public static $domain;
	
	/**
	 * Bonaire_Reply_Form_Display constructor.
	 *
	 * @param string $domain
	 *
	 * @since 0.9.0
	 * @return void
	 */
	public function __construct( $domain ) {
		
		self::$domain = $domain;
	}
	
	/**
	 * Returns a string containing the reply form.
	 *
	 * @param string $subject_text
	 * @param object $account_settings
	 *
	 * @since 0.9.0
	 * @return string $html
	 */
	public static function reply_form_display( $subject_text, $account_settings ) {
		
		$name = $account_settings->fromname;
		$email = $account_settings->from;
		$subject = 'RE: ' . $subject_text;
		$nonce = wp_create_nonce( 'bonaire_reply_form_nonce' );
		
		ob_start();
		?>
        <!-- a fix, maybe @todo if not wp-related... -->
        <form></form>
        <div class="bonaire-reply-form-container">
            <form id="bonaire_reply_form" method="post" data-nonce="<?php echo $nonce ?>">
                <div>
                    <label for="name"><?php echo __( 'Your Name', self::$domain ) ?></label>
                    <input type="text" name="name" data-key="name" data-form-input="bonaire" title="<?php echo __( 'Name', self::$domain ) ?>"
                        value="<?php echo $name ?>">
                </div>
                <div>
                    <label for="email"><?php echo __( 'Recipient', self::$domain ) ?></label>
                    <input type="email" name="email" data-key="email" data-form-input="bonaire" title="<?php echo __( 'Email', self::$domain ) ?>"
                        value="<?php echo $email ?>" disabled>
                </div>
                <div>
                    <label for="subject"><?php echo __( 'Subject', self::$domain ) ?></label>
                    <input type="text" name="subject" data-key="subject" data-form-input="bonaire"
                        title="<?php echo __( 'Subject', self::$domain ) ?>" value="<?php echo $subject ?>">
                </div>
                <div>
                    <input id="cb_parallax_options[attachments]" type="hidden" name="cb_parallax_options[attachments]" data-value=""/>
                    <ul class="attachment-list"></ul>
                </div>
                <div>
                    <label for="textarea"><?php echo __( 'Message', self::$domain ) ?></label>
                    <textarea name="textarea" id="" data-key="message" data-form-input="bonaire" title="<?php echo __( 'Message', self::$domain ) ?>"
                        cols="30" rows="10"></textarea>
                </div>

                <input class="button button-primary button-large bonaire-submit-reply-button" type="submit"
                    value="<?php echo __( 'Submit', self::$domain ) ?>">
            </form>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
