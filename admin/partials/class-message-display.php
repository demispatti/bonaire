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
	 * @param string $your_message
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function message_display( $your_message ) {

		ob_start();
		?>
        <!-- a fix, maybe an @todo if not wp-related... -->
        <form></form>
        <div class="bonaire-message-container">
            <div id="bonaire_message">
                <div>
                    <textarea disabled name="textarea" data-key="message" data-form-input="bonaire" cols="30" rows="10"><?php echo $your_message ?></textarea>
                </div>
            </div>
        </div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
