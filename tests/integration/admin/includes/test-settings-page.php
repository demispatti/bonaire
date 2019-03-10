<?php /** @noinspection MultiAssignmentUsageInspection */

/**
 * Class Bonaire_Settings_Page_IntegrationTest
 */
class Bonaire_Settings_Page_IntegrationTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-page.php';
		require_once BONAIRE_ROOT_DIR . 'admin/partials/class-settings-page-display.php';
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
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		
		//$Bonaire_Settings_Page_Display = new Bonaire\Admin\Partials\Bonaire_Settings_Page_Display( $this->domain, $Bonaire_Options );
		
		$Instance = new Bonaire\Admin\Includes\Bonaire_Settings_Page( $this->domain, $Bonaire_Options/*, $Bonaire_Settings_Page_Display*/ );
		$Instance->add_hooks();
		
		$this->assertSame(
			20,
			has_action( 'admin_enqueue_scripts', array( $Instance, 'localize_script' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'admin_menu', array( $Instance, 'add_settings_page' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_add_settings_page() {
		
		global $wp_filter;
		
		$function_name = 'add_settings_page';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Settings_Page';
		
		$count = 0;
		foreach ( $wp_filter as $tag ) {
			// Retrieve callbacks
			$callbacks = (array) $tag->callbacks;
			
			foreach ( $callbacks as $priority => $hooks ) {
				
				foreach ( $hooks as $hook => $args ) {
					// Retrieve method name
					$queued_function_name = $args['function'][1];
					// Retrieve passed object
					$queued_instance = $args['function'][0];
					// Count the amount of times the method got hooked.
					if ( $function_name === $queued_function_name && is_a( $queued_instance, $classname ) ) {
						$count ++;
					}
				}
			}
		}
		
		$this->assertTrue( 1 <= $count, 'Settings page did not load.' );
	}
	
}
