<?php

/**
 * Class Bonaire_FunctionalTest
 */
class Bonaire_FunctionalTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'includes/class-i18n.php';
		require_once BONAIRE_ROOT_DIR . 'admin/class-admin.php';
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
	
	public function test_constructor_with_arguments() {
		
		$classname = 'Bonaire\Includes\Bonaire';
		
		$Class = new $classname( $this->name, $this->domain, $this->version );
		
		$this->assertObjectHasAttribute( 'name', $Class, 'Attribute "name" does not exist.' );
		$this->assertAttributeEquals( 'bonaire', 'name', $Class, 'Attribute "name": value is not as expected.' );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( 'bonaire', 'domain', $Class, 'Attribute "domain": value is not as expected.' );
		
		$this->assertObjectHasAttribute( 'version', $Class, 'Attribute "version" does not exist.' );
		$this->assertAttributeEquals( '0.9.6 ', 'version', $Class, 'Attribute "version": value is not as expected.' );
	}
	
	public function test_include_locale() {
		
		$file = BONAIRE_ROOT_DIR . 'includes/class-i18n.php';
		$classname = 'Bonaire\Includes\Bonaire_i18n';
		$Instance = new $classname( $this->domain );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_admin() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/class-admin.php';
		$classname = 'Bonaire\Admin\Bonaire_Admin';
		$Instance = new $classname( $this->name, $this->domain, $this->version );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_public() {
		
		$file = BONAIRE_ROOT_DIR . 'public/class-public.php';
		$classname = 'Bonaire\Pub\Bonaire_Public';
		$Instance = new $classname( $this->name, $this->domain, $this->version );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
