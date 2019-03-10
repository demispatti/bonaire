<?php

use Bonaire\Admin\Includes as AdminIncludes;
use Bonaire\Admin\Partials as Partials;

/**
 * Class Bonaire_Dashboard_Widget_IntegrationTest
 */
class Bonaire_Dashboard_Widget_IntegrationTest extends WP_UnitTestCase {
	
	private $name;
	
	private $domain;
	
	private $version;
	
	public function setUp() {
		
		global $name;
		global $domain;
		global $version;
		
		$this->name = $name;
		$this->domain = $domain;
		$this->version = $version;
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-dashboard-widget.php';
		//require_once BONAIRE_ROOT_DIR . 'admin/partials/class-item-display.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_add_hooks() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		//$Bonaire_Item_Display = new Partials\Bonaire_Item_Display( $this->domain );
		
		$Instance = new AdminIncludes\Bonaire_Dashboard_Widget( $this->domain/*, $Bonaire_Item_Display*/, $Bonaire_Options );
		$Instance->add_hooks();
		
		$this->assertSame(
			10,
			has_action( 'wp_dashboard_setup', array( $Instance, 'add_dashboard_widget' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_add_dashboard_widget() {
		
		global $wp_filter;
		
		$wp_filter['wp_dashboard_setup']->callbacks['10'][0]['function'][0] = 'Bonaire';
		$wp_filter['wp_dashboard_setup']->callbacks['10'][0]['function'][1] = 'add_dashboard_widget';
		
		$callbacks_of_interest = $wp_filter['wp_dashboard_setup']->callbacks['10'];
		
		$found_item = 0;
		foreach ( $callbacks_of_interest as $widget => $args ) {
			if ( 'add_dashboard_widget' === $args['function'][1] && is_a( $args['function'][0], 'Bonaire\Admin\Includes\Bonaire_Dashboard_Widget' ) ) {
				$found_item ++;
			}
		}
		
		$this->assertNotEquals( 0, $found_item, 'Failed to register widget with WordPress.' );
	}
	
}
