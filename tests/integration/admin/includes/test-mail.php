<?php

/**
 * Class Bonaire_Mail_IntegrationTest
 */
class Bonaire_Mail_IntegrationTest extends WP_UnitTestCase {
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_setup_phpmailer() {
		
		$file = ABSPATH . WPINC . '/class-phpmailer.php';
		$classname = 'PHPMailer';
		$Instance = new $classname();
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
	}
	
}
