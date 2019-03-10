<?php

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * Class Bonaire_Post_Views_IntegrationTest
 */
class Bonaire_Post_Views_IntegrationTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
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
		
		$Instance = new AdminIncludes\Bonaire_Post_Views( $this->domain );
		$Instance->add_hooks();
		
		$this->assertSame(
			10,
			has_action( 'admin_notices', array( $Instance, 'count_message_views' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
}
