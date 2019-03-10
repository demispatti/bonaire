<?php /** @noinspection MultiAssignmentUsageInspection */

/**
 * Class Bonaire_Settings_Page_FunctionalTest
 */
class Bonaire_Settings_Page_FunctionalTest extends WP_UnitTestCase {
	
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
		require_once BONAIRE_ROOT_DIR . 'admin/partials/class-settings-page-display.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-page.php';
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
		
		$classname = 'Bonaire\Admin\Includes\Bonaire_Settings_Page';
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Class = new $classname( $this->domain, $Bonaire_Options );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( $this->domain, 'domain', $Class, 'Attribute "domain": value is not as expected.' );
	}
	
	public function test_settings_page_display() {
		
		// Settings Page Display Instance and its methods we need here.
		$display_file = BONAIRE_ROOT_DIR . 'admin/partials/class-settings-page-display.php';
		$classname = 'Bonaire\Admin\Partials\Bonaire_Settings_Page_Display';
		
		$Bonaire_Options = new Bonaire\Admin\Includes\Bonaire_Options( $this->domain );
		$Bonaire_Settings_Page_Display = new Bonaire\Admin\Partials\Bonaire_Settings_Page_Display( $this->domain, $Bonaire_Options );
		$Instance = new $classname( $this->domain, $Bonaire_Options, $Bonaire_Settings_Page_Display );
		$method = 'get_form';
		
		$this->assertFileExists( $display_file, 'Failed to find file.' );
		$this->assertFileIsReadable( $display_file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
		
		// Options instance and its methods we need here.
		$options_file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		
		$Bonaire_Options = new $classname( $this->domain );
		$method_get_meta = 'get_stored_options';
		$method_get_options = 'get_options_meta';
		
		// Instance
		$this->assertFileExists( $options_file, 'Failed to find file.' );
		$this->assertFileIsReadable( $options_file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Bonaire_Options, 'Falied asserting Instance of.' );
		
		// Method 1
		$this->assertTrue( method_exists( $classname, $method_get_meta ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Bonaire_Options, $method_get_meta ), 'Failed to call method.' );
		
		// Method 2
		$this->assertTrue( method_exists( $classname, $method_get_options ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Bonaire_Options, $method_get_options ), 'Failed to call method.' );
	}
	
}
