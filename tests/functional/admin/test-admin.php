<?php

use Bonaire\Admin\Includes as AdminIncludes;
use Bonaire\Admin\Partials as Partials;

/**
 * Class Bonaire_Admin_FunctionalTest
 */
class Bonaire_Admin_FunctionalTest extends WP_UnitTestCase {
	
	private $name;
	
	private $domain;
	
	private $version;
	
	public static $plugin_hook_suffixes = array(
		'settings_page' => 'settings_page_bonaire',
		'flamingo_inbound' => 'flamingo_page_flamingo_inbound',
		'dashboard' => 'index.php'
	);
	
	public static $plugin_pages = array(
		'dashboard' => 'index.php',
		'flamingo_inbound' => 'flamingo_inbound',
		'settings_page' => 'bonaire.php'
	);
	
	public $Bonaire_Options;
	
	public $Bonaire_Post_Views;
	
	public $Bonaire_Adapter;
	
	private $Bonaire_Mail;
	
	private $Bonaire_Settings_Evaluator;
	
	public function setUp() {
		
		global $name;
		global $domain;
		global $version;
		
		$this->name = $name;
		$this->domain = $domain;
		$this->version = $version;
		
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-evaluator.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-page.php';
		require_once BONAIRE_ROOT_DIR . 'admin/partials/class-settings-page-display.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-dashboard-widget.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-contextual-help.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-meta-box.php';
		require_once BONAIRE_ROOT_DIR . '/admin/partials/class-reply-form-display.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ajax.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-tooltips.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-adapter.php';
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
	}
	
	public function test_set_options_instance() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$this->Bonaire_Options = new $classname( $this->domain );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->Bonaire_Options, 'Falied asserting Instance of.' );
	}
	
	public function test_set_post_views_instance() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Post_Views';
		$this->Bonaire_Post_Views = new $classname( $this->domain );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->Bonaire_Post_Views, 'Falied asserting Instance of.' );
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_include_flamingo_inbound_adapter() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-adapter.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Adapter';
		$Instance = new $classname( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
	}
	
	public function test_include_dashboard_widget() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-dashboard-widget.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Dashboard_Widget';
		$Instance = new $classname( $this->domain, $Bonaire_Options );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_settings_evaluator() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		$Bonaire_Mail = new AdminIncludes\Bonaire_Mail( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-settings-evaluator.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Settings_Evaluator';
		$this->Bonaire_Settings_Evaluator = new $classname( $this->domain, $Bonaire_Options, $Bonaire_Mail );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->Bonaire_Settings_Evaluator, 'Falied asserting Instance of.' );
	}
	
	public function test_include_settings_page() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-settings-page.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Settings_Page';
		$Instance = new $classname( $this->domain, $Bonaire_Options );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_bonaire_mail() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Mail';
		$this->Bonaire_Mail = new $classname( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->Bonaire_Mail, 'Falied asserting Instance of.' );
	}
	
	public function test_include_meta_box() {
		
		$Bonaire_Reply_Form_Display = new Partials\Bonaire_Reply_Form_Display( $this->domain );
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		$Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-settings-page.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Meta_Box';
		$Instance = new $classname( $this->domain, $Bonaire_Reply_Form_Display, $Bonaire_Adapter, $Bonaire_Options );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_help_tab() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-contextual-help.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Contextual_Help';
		$Instance = new $classname( $this->domain );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_post_views() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Post_Views';
		$this->Bonaire_Post_Views = new $classname( $this->domain );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->Bonaire_Post_Views, 'Falied asserting Instance of.' );
	}
	
	public function test_include_options() {
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Options';
		$this->Bonaire_Options = new $classname( $this->domain );
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $this->Bonaire_Options, 'Falied asserting Instance of.' );
	}
	
	public function test_include_ajax() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		$Bonaire_Post_Views = new AdminIncludes\Bonaire_Post_Views( $this->domain );
		$Bonaire_Mail = new AdminIncludes\Bonaire_Mail( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		$Bonaire_Settings_Evaluator = new AdminIncludes\Bonaire_Settings_Evaluator( $this->domain, $Bonaire_Options, $Bonaire_Mail );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-ajax.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Ajax';
		$Instance = new $classname( $this->domain, $Bonaire_Options, $Bonaire_Post_Views, $Bonaire_Mail, $Bonaire_Settings_Evaluator );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
	public function test_include_tooltips() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		
		$file = BONAIRE_ROOT_DIR . 'admin/includes/class-tooltips.php';
		$classname = 'Bonaire\Admin\Includes\Bonaire_Tooltips';
		$Instance = new $classname( $this->domain, $Bonaire_Options->get_options_meta() );
		$method = 'add_hooks';
		
		$this->assertFileExists( $file, 'Failed to find file.' );
		$this->assertFileIsReadable( $file, 'Failed to read file.' );
		$this->assertInstanceOf( $classname, $Instance, 'Falied asserting Instance of.' );
		$this->assertTrue( method_exists( $classname, $method ), 'Failed to find method.' );
		$this->assertInternalType( 'callable', array( $Instance, $method ), 'Failed to call method.' );
	}
	
}
