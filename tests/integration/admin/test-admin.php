<?php

/**
 * Class Bonaire_Admin_IntegrationTest
 */
class Bonaire_Admin_IntegrationTest extends WP_UnitTestCase {
	
	private $name;
	
	private $domain;
	
	private $version;
	
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
		
		$Instance = new Bonaire\Admin\Bonaire_Admin( $this->name, $this->domain, $this->version );
		$Instance->add_hooks();
		
		$this->assertSame(
			10,
			has_action( 'init', array( $Instance, 'init_dependencies' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'admin_enqueue_scripts', array( $Instance, 'enqueue_styles' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'admin_enqueue_scripts', array( $Instance, 'enqueue_scripts' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'admin_enqueue_scripts', array( $Instance, 'maybe_update_post' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_enqueue_styles() {
		
		global $wp_styles;
		
		$Instance = new Bonaire\Admin\Bonaire_Admin( $this->name, $this->domain, $this->version );
		$Instance->enqueue_styles( self::$plugin_hook_suffixes['dashboard'] );
		
		$handles = array(
			'bonaire-inc-tooltipster-core-css',
			'bonaire-inc-tooltipster-bundle-css',
			'bonaire-inc-tooltipster-theme-shadow-css',
			'bonaire-inc-alertify-min-css',
			'bonaire-inc-alertify-theme-bootstrap-min-css',
			'bonaire-admin-css'
		);
		
		$queue = $wp_styles->queue;
		
		$missing_files = 0;
		foreach ( $handles as $i => $handle ) {
			if ( ! in_array( $handle, $queue, true ) ) {
				$missing_files ++;
			}
		}
		
		$this->assertSame( 0, $missing_files, 'Failed to register method with WordPress.' );
	}
	
	public function test_enqueue_scripts() {
		
		global $wp_scripts;
		
		$Instance = new Bonaire\Admin\Bonaire_Admin( $this->name, $this->domain, $this->version );
		$Instance->enqueue_scripts( self::$plugin_hook_suffixes['dashboard'] );
		
		$handles = array(
			'bonaire-inc-tooltipster-core-min-js',
			'bonaire-inc-tooltipster-svg-min-js',
			'bonaire-inc-tooltipster-bundle-min-js',
			'bonaire-tooltips-js',
			'bonaire-inc-alertify-min-js',
			'bonaire-admin-js'
		);
		
		$queue = $wp_scripts->queue;
		
		$missing_files = 0;
		foreach ( $handles as $i => $handle ) {
			if ( ! in_array( $handle, $queue, true ) ) {
				$missing_files ++;
			}
		}
		
		$this->assertSame( 0, $missing_files, 'Failed to register method with WordPress.' );
	}
	
}
