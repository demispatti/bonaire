<?php
namespace Bonaire\Includes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for deactivating the plugin.
 *
 * @since      0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/includes
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Deactivator {
	
	/**
	 * Deletes corrupted stored data if any on plugin deactivation.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function deactivate() {
		
		// Gets the administrator role.
		$role = get_role( 'administrator' );
		
		// If the acting user has admin rights, the capability gets added.
		if ( ! empty( $role ) ) {
			$options = get_option( 'bonaire_options' );
			
			// If there are options and it is not an array, the options get deleted.
			if ( false !== $options && ! is_array( $options ) ) {
				
				delete_option( 'bonaire_options' );
			}
		}
	}
	
}
