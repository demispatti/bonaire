<?php

/**
 * Class Bonaire_Tooltips_FunctionalTest
 */
class Bonaire_Tooltips_FunctionalTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-tooltips.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_constructor_with_arguments() {
		
		$classname = 'Bonaire\Admin\Includes\Bonaire_Tooltips';
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Class = new $classname( $this->domain, $Bonaire_Options->get_options_meta() );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( 'bonaire', 'domain', $Class, 'Attribute "domain": value is not as expected.' );
		
		$this->assertObjectHasAttribute( 'options_meta', $Class, 'Attribute "options_meta" does not exist.' );
		$this->assertAttributeEquals( $Bonaire_Options->get_options_meta(), 'options_meta', $Class, 'Attribute "options_meta": value is not as expected.' );
	}
	
	public function test_load_dependencies() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$Instance = new $classname( $this->domain );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
	}
	
	public function test_get_options_meta() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$Instance = new $classname( $this->domain );
		$method = 'get_options_meta';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
}
