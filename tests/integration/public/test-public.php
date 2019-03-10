<?php

/**
 * Class Bonaire_Public_IntegrationTest
 */
class Bonaire_Public_IntegrationTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'public/class-public.php';
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
		
		$Instance = new Bonaire\Pub\Bonaire_Public();
		$Instance->add_hooks();
		
		$this->assertSame(
			10,
			has_action( 'wp_enqueue_scripts', array( $Instance, 'enqueue_scripts' ) ),
			'Failed to register method with WordPress.'
		);
		
		$this->assertSame(
			10,
			has_action( 'wpcf7_mail_sent', array( $Instance, 'wpcf7_mail_sent' ) ),
			'Failed to register method with WordPress.'
		);
		
		$this->assertSame(
			10,
			has_action( 'wpcf7_posted_data', array( $Instance, 'filter_wpcf7_posted_data' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
}
