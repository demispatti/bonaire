<?php

class Bonaire_Settings_Evaluator_FunctionalTest extends WP_UnitTestCase {
	
	private $domain;
	
	public function setUp() {
		
		global $domain;
		
		$this->domain = $domain;
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ping.php';
		require_once ABSPATH . WPINC . '/class-phpmailer.php';
	}
	
	public function tearDown() {
		
		$this->domain = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_include_options() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$Instance = new $classname( $this->domain );
		$method = 'bonaire_set_evaluation_state';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_ping() {
		
		$file = ABSPATH . WPINC . '/class-phpmailer.php';
		$classname = 'JJG\Ping';
		$Instance = new $classname( 'smtp.gmail.com' );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
	}
	
	public function test_include_phpmailer() {
		
		$file = ABSPATH . WPINC . '/class-phpmailer.php';
		$classname = 'PHPMailer';
		$Instance = new $classname();
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
	}
	
}
