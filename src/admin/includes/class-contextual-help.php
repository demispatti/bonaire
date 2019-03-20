<?php

namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Partials as AdminPartials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'AdminPartials\Bonaire_Help_Tab_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/partials/class-help-tab-display.php';
}
if ( ! class_exists( 'AdminPartials\Bonaire_Help_Sidebar_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/partials/class-help-sidebar-display.php';
}

/**
 * The class responsible for creating and displaying the help tab
 *
 * @since            0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Contextual_Help {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since   0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Bonaire_Contextual_Help constructor.
	 *
	 * @param string $domain
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
		
		$this->initialize();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function add_hooks() {
		
		add_action( 'in_admin_header', array( $this, 'add_contextual_help' ), 20 );
		
		add_action( 'load-post.php', array( $this, 'add_contextual_help' ), 10 );
		add_action( 'load-post-new.php', array( $this, 'add_contextual_help' ), 11 );
		add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'add_contextual_help' ), 12 );
	}
	
	/**
	 * Adds the method to the queue of actions.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function initialize() {
		
		add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'add_contextual_help' ), 15 );
	}
	
	/**
	 * Displays the Help Tab and the Help Sidebar.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function add_contextual_help() {
		
		$current_screen = get_current_screen();
		
		$current_screen->add_help_tab( array(
			'id' => 'bonaire-help-tab',
			'title' => __( 'Bonaire Help', $this->domain ),
			'content' => AdminPartials\Bonaire_Help_Tab_Display::help_tab_display( $this->domain )
		) );
		
		$current_screen->set_help_sidebar(
			AdminPartials\Bonaire_Help_Sidebar_Display::help_sidebar_display( $this->domain, $current_screen )
		);
	}
	
}
