<?php

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * Class Bonaire_Contextual_Help_IntegrationTest
 */
class Bonaire_Contextual_Help_IntegrationTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-contextual-help.php';
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
		
		$Instance = new AdminIncludes\Bonaire_Contextual_Help( $this->domain );
		$Instance->add_hooks();
		
		$this->assertSame(
			20,
			has_action( 'in_admin_header', array( $Instance, 'add_contextual_help' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'load-post.php', array( $Instance, 'add_contextual_help' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			11,
			has_action( 'load-post-new.php', array( $Instance, 'add_contextual_help' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			12,
			has_action( "load-{$GLOBALS['pagenow']}", array( $Instance, 'add_contextual_help' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_initialize() {
		
		$Instance = new AdminIncludes\Bonaire_Contextual_Help( $this->domain );
		$Instance->initialize();
		
		add_action( "load-{$GLOBALS['pagenow']}", array( $this, 'add_contextual_help' ), 15 );
		
		$this->assertSame(
			15,
			has_action( "load-{$GLOBALS['pagenow']}", array( $Instance, 'add_contextual_help' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
}
