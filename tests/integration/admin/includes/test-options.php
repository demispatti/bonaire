<?php

/**
 * Class Bonaire_Options_IntegrationTest
 */
class Bonaire_Options_IntegrationTest extends WP_UnitTestCase {
	
	private $name;
	
	private $domain;
	
	private $version;
	
	/**
	 * Holds the names of the plugin hook suffixes
	 * the plugin will load its related classes (or not).
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
		
		$options = array();
		$options[0] = array(
			'channel' => '',
			'username' => '',
			'password' => '',
			'smtp_host' => '',
			'smtp_port' => 465,
			'smtpsecure' => 'ssl',
			'fromname' => '',
			'from' => '',
			'save_reply' => 'no',
			'imapsecure' => 'ssl',
			'imap_host' => '',
			'imap_port' => 993
		);
		$options[1] = array(
			'smtp_hash' => md5( serialize( array(
				'channel' => '',
				'username' => '',
				'password' => '',
				'smtp_host' => '',
				'smtp_port' => 465,
				'smtpsecure' => 'ssl',
				'fromname' => '',
				'from' => ''
			) ) ),
			'imap_hash' => md5( serialize( array(
				'channel' => '',
				'username' => '',
				'password' => '',
				'smtp_host' => '',
				'smtp_port' => 465,
				'smtpsecure' => 'ssl',
				'fromname' => '',
				'from' => '',
				'imapsecure' => 'ssl',
				'imap_host' => '',
				'imap_port' => 993,
			) ) ),
			'smtp_settings_state' => 'red',
			'imap_settings_state' => 'red'
		);
		add_option( 'bonaire_options', $options );
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
		
		delete_option( 'bonaire_options' );
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_add_hooks() {
		
		$Instance = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		
		add_action( 'admin_enqueue_scripts', array( $Instance, 'localize_script' ), 20 );
		
		$this->assertSame(
			20,
			has_action( 'admin_enqueue_scripts', array( $Instance, 'localize_script' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_localize_script() {
		
		// Enqueue the script to be localized.
		$Instance = new Bonaire\Admin\Bonaire_Admin( $this->name, $this->domain, $this->version );
		$Instance->enqueue_scripts( self::$plugin_hook_suffixes['dashboard'] );
		// Localize.
		$Instance = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Instance->localize_script();
		
		// Retrieve data from WordPress.
		global $wp_scripts;
		$data = $wp_scripts->get_data( 'bonaire-admin-js', 'data' );
		
		self::assertNotFalse( $data, 'Failed to retrieve localized data from WordPress.' );
	}
	
}
