<?php

/**
 * Class Bonaire_Tooltips_IntegrationTest
 */
class Bonaire_Tooltips_IntegrationTest extends WP_UnitTestCase {
	
	private $name;
	
	private $domain;
	
	private $version;
	
	/**
	 * The array holding the suffixes where we hook our files.
	 *
	 * @var array $plugin_hook_suffixes
	 */
	public static $plugin_hook_suffixes = array(
		'settings_page' => 'settings_page_bonaire',
		'flamingo_inbound' => 'flamingo_page_flamingo_inbound',
		'dashboard' => 'index.php'
	);
	
	public function setUp() {
		
		global $name;
		global $domain;
		global $version;
		
		$this->name = $name;
		$this->domain = $domain;
		$this->version = $version;
		
		require_once BONAIRE_ROOT_DIR . 'admin/class-admin.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-tooltips.php';
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
		$options_meta = $Bonaire_Options->get_options_meta();
		
		$Instance = new Bonaire\Admin\Includes\Bonaire_Tooltips( $this->domain, $options_meta );
		$Instance->add_hooks();
		
		$this->assertSame(
			40,
			has_action( 'admin_enqueue_scripts', array( $Instance, 'localize_script' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_localize_script() {
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$options_meta = $Bonaire_Options->get_options_meta();
		
		// Enqueue the script to be localized.
		$Instance = new Bonaire\Admin\Bonaire_Admin( $this->name, $this->domain, $this->version );
		$Instance->enqueue_scripts( self::$plugin_hook_suffixes['dashboard'] );
		// Localize.
		$Instance = new Bonaire\Admin\Includes\Bonaire_Tooltips( $this->domain, $options_meta );
		$Instance->localize_script( self::$plugin_hook_suffixes['dashboard'] );
		
		// Retrieve data from WordPress.
		global $wp_scripts;
		$data = $wp_scripts->get_data( 'bonaire-admin-js', 'data' );
		
		self::assertNotFalse( $data, 'Failed to retrieve localized data from WordPress.' );
	}
}
