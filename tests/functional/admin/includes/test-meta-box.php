<?php

/**
 * Class Bonaire_Meta_Box_FunctionalTest
 */
class Bonaire_Meta_Box_FunctionalTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-meta-box.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		require_once BONAIRE_ROOT_DIR . 'admin/partials/class-reply-form-display.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-adapter.php';
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
		
		$classname = 'Bonaire\Admin\Includes\Bonaire_Meta_Box';
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Bonaire_Reply_Form_Display = new Bonaire\Admin\Partials\Bonaire_Reply_Form_Display( $this->domain );
		$Bonaire_Adapter = new Bonaire\Admin\Includes\Bonaire_Adapter( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		$Class = new $classname( $this->domain, $Bonaire_Reply_Form_Display, $Bonaire_Adapter, $Bonaire_Options );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( $this->domain, 'domain', $Class, 'Attribute "domain": value is not as expected.' );
		
		//$this->assertObjectHasAttribute( 'Bonaire\Admin\Partials\Bonaire_Reply_Form_Display', $Class, 'Attribute "Bonaire_Reply_Form_Display" does not exist.' );
		//$this->assertAttributeEquals( $Bonaire_Reply_Form_Display, 'Bonaire\Admin\Partials\Bonaire_Reply_Form_Display', $Class, 'Attribute "Bonaire\Admin\Partials\Bonaire_Reply_Form_Display": value is not as expected.' );
		
		//$this->assertObjectHasAttribute( 'Bonaire\Admin\Includes\Bonaire_Adapter', $Class, 'Attribute "Bonaire_Adapter" does not exist.' );
		//$this->assertAttributeEquals( $Bonaire_Adapter, 'Bonaire\Admin\Includes\Bonaire_Adapter', $Class, 'Attribute "Bonaire_Adapter": value is not as expected.' );
	}
	
	public function test_display_reply_form_meta_box() {
		
		$display_file = BONAIRE_ROOT_DIR . 'admin/partials/class-reply-form-display.php';
		$classname = 'Bonaire\Admin\Partials\Bonaire_Reply_Form_Display';
		$Instance = new $classname( $this->domain );
		$method = 'reply_form_display';
		
		$this->assertFileExists( $display_file, 'Failed to find file.' );
		$this->assertFileIsReadable( $display_file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
