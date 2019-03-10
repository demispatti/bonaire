<?php

/**
 * Class Bonaire_Launcher_FunctionalTest
 */
class Bonaire_Launcher_FunctionalTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'includes/class-activator.php';
		require_once BONAIRE_ROOT_DIR . 'includes/class-deactivator.php';
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_activate_bonaire() {
		
		$file = BONAIRE_ROOT_DIR . 'includes/class-activator.php';
		$classname = 'Bonaire\Includes\Bonaire_Activator';
		$Instance = new $classname();
		$method = 'activate';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_deactivate_bonaire() {
		
		$file = BONAIRE_ROOT_DIR . 'includes/class-deactivator.php';
		$classname = 'Bonaire\Includes\Bonaire_Deactivator';
		$Instance = new $classname();
		$method = 'deactivate';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_run_bonaire() {
		
		$file = BONAIRE_ROOT_DIR . 'includes/class-bonaire.php';
		$classname = 'Bonaire\Includes\Bonaire';
		$Instance = new $classname( $this->name, $this->domain, $this->version );
		$method = 'init';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
