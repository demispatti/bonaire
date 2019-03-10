<?php

class Bonaire_Imap_FunctionalTest extends WP_UnitTestCase {
	
	private $domain;
	
	private $PHPMailer;
	
	public function setUp() {
		
		global $domain;
		
		$this->domain = $domain;
		
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
	}
	
	public function tearDown() {
		
		$this->domain = null;
	}
	
	public function test_set_phpmailer() {
		
		$file = ABSPATH . WPINC . '/class-phpmailer.php';
		$classname = 'PHPMailer';
		$this->PHPMailer = new $classname();
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->PHPMailer, 'Falied asserting Instance of.' );
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
}
