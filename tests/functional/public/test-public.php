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
	
	public function test_bonaire_after_mail_sent( $contact_form_name = 'contact-form' ) {
		
		set_transient( 'bonaire_after_mail_sent', $contact_form_name );
		
		$this->assertSame( 'contact-form', get_transient( 'bonaire_after_mail_sent' ) );
	}
	
}
