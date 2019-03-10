<?php

/**
 * Class Bonaire_Dashboard_Widget_FunctionalTest
 */
class Bonaire_Dashboard_Widget_FunctionalTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-dashboard-widget.php';
		//require_once BONAIRE_ROOT_DIR . 'admin/partials/class-item-display.php';
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
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		//$Bonaire_Item_Display = new Bonaire\Admin\Partials\Bonaire_Item_Display( $this->domain );
		
		$classname = 'Bonaire\Admin\Includes\Bonaire_Dashboard_Widget';
		
		$Class = new $classname( $this->domain/*, $Bonaire_Item_Display*/, $Bonaire_Options );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( $this->domain, 'domain', $Class, 'Attribute "domain": value is not as expected.' );
	}
	
	public function test_display_items() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/partials/class-item-display.php';
		$classname = 'Bonaire\Admin\Partials\Bonaire_Item_Display';
		$Instance = new $classname( $this->domain );
		$method = 'item_display';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
