<?php
namespace Bonaire\Includes;

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'AdminIncludes\Bonaire_Options' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
}

/**
 * The class responsible for activating the plugin.
 *
 * @since      0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/includes
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Activator {
	
	/**
	 * Instantiates the class responsible for activating the plugin.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function activate() {
		
		// Gets the administrator role.
		$role = get_role( 'administrator' );
		
		// If the acting user has admin rights, the capability gets added.
		if ( ! empty( $role ) ) {
			// Adds default settings data if there are no options stored yet.
			if ( false === get_option( 'bonaire_options' ) || '1' === get_transient( 'bonaire_reset_settings' ) ) {
				delete_option( 'bonaire_options' );
				$Bonaire_Options = new AdminIncludes\Bonaire_Options( 'bonaire' );
				
				$options[0] = (array) $Bonaire_Options->default_options->{0};
				$options[1] = (array) $Bonaire_Options->default_options->{1};
				
				add_option( 'bonaire_options', $options, '', true );
				delete_transient( 'bonaire_reset_settings' );
			}
		}
	}
	
}
