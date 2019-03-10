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
 * @since      1.0.0
 * @package    Bonaire
 * @subpackage Bonaire/includes
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Deactivator {
	
	/**
	 * Deletes corrupted stored data if any on plugin deactivation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function deactivate() {
		
		$options = get_option( 'bonaire_options' );
		
		// If there are options and it is not an array, the options get deleted.
		if ( false !== $options && ! is_array( $options ) ) {
			
			delete_option( 'bonaire_options' );
		}
	}
	
}
