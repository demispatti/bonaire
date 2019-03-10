<?php

class Bonaire_Adapter_FunctionalTest extends WP_UnitTestCase {
	
	protected $domain;
	
	public function setUp() {
		
		global $domain;
		
		$this->domain = $domain;
		
		require_once BONAIRE_ROOT_DIR . '../../contact-form-7/includes/contact-form.php';
		require_once BONAIRE_ROOT_DIR . '../../flamingo/includes/class-inbound-message.php';
	}
	
	public function tearDown() {
		
		$this->domain = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_wpcf7_find() {
		
		$file = BONAIRE_ROOT_DIR . '../../contact-form-7/includes/contact-form.php';
		$classname = 'WPCF7_ContactForm';
		$Instance = $classname;
		$method = 'find';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_flamingo_inbound_message_add() {
		
		$file = BONAIRE_ROOT_DIR . '../../flamingo/includes/class-inbound-message.php';
		$classname = 'Flamingo_Inbound_Message';
		$Instance = $classname;
		$method = 'add';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
