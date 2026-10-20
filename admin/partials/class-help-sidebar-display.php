<?php
namespace Bonaire\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the help sidebar.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Help_Sidebar_Display {
	
	/**
	 * Returns a string containing the content of the 'Help Sidebar'.
	 *
	 * @param string $domain
	 * @param \WP_Screen $current_screen
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	public static function help_sidebar_display( $domain, $current_screen ) {
		
		$html = $current_screen->get_help_sidebar();
		
		ob_start();
		?>

        <p><?php esc_html_e( 'For more information on SALT-Keys, please read', "bonaire" ) ?>
            <a target="_blank"
                href="https://www.elegantthemes.com/blog/tips-tricks/what-are-wordpress-salt-keys-and-how-can-you-change-them"><?php esc_html_e( 'What are wordpress salt keys and how can you change them', "bonaire" ) ?></a>&nbsp;<?php esc_html_e( '(english)', "bonaire" ) ?>
            .
        </p>
		
		<?php
		$html .= ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
