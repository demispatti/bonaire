<?php

/**
 * Class Bonaire_Ajax_FunctionalTest
 */
class Bonaire_Ajax_FunctionalTest extends WP_UnitTestCase {
	
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
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ajax.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
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
		$stored_options = $Bonaire_Options->get_stored_options( 0 );
		$Bonaire_Post_Views = new Bonaire\Admin\Includes\Bonaire_Post_Views( $this->domain );
		$Bonaire_Mail = new Bonaire\Admin\Includes\Bonaire_Mail( $this->domain, $Bonaire_Options );
		
		$classname = 'Bonaire\Admin\Includes\Bonaire_Ajax';
		
		$Class = new $classname( $this->domain, $Bonaire_Options, $Bonaire_Post_Views, $Bonaire_Mail );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( 'bonaire', 'domain', $Class, 'Attribute "domain": value is not as expected.' );
	}
	
	public function test_bonaire_mark_as_read() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Post_Views';
		$Instance = new $classname( $this->domain );
		$method = 'update_post_view';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_bonaire_save_options() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$Instance = new $classname( $this->domain );
		$method = 'bonaire_save_options';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_bonaire_reset_options() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$Instance = new $classname( $this->domain );
		$method = 'reset_options';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_bonaire_send_test_mail() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Mail';
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Instance = new $classname( $this->domain, $Bonaire_Options );
		$method = 'send_testmail';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_bonaire_submit_reply() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Mail';
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Instance = new $classname( $this->domain, $Bonaire_Options );
		$method = 'send_mail';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
