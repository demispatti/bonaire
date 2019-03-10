<?php

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * Class Bonaire_Ajax_IntegrationTest
 */
class Bonaire_Ajax_IntegrationTest extends WP_UnitTestCase {
	
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
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-evaluator.php';
		require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ajax.php';
	}
	
	public function tearDown() {
		
		$this->name = null;
		$this->domain = null;
		$this->version = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_add_hooks() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		$Bonaire_Post_Views = new AdminIncludes\Bonaire_Post_Views( $this->domain );
		$Bonaire_Mail = new AdminIncludes\Bonaire_Mail( $this->domain, $Bonaire_Options->get_stored_options( 0 ) );
		$Bonaire_Settings_Evaluator = new AdminIncludes\Bonaire_Settings_Evaluator( $this->domain, $Bonaire_Options, $Bonaire_Mail );
		
		$Instance = new AdminIncludes\Bonaire_Ajax( $this->domain, $Bonaire_Options, $Bonaire_Post_Views, $Bonaire_Mail, $Bonaire_Settings_Evaluator );
		$Instance->add_hooks();
		
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_mark_as_read', array( $Instance, 'bonaire_mark_as_read' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_mark_as_spam', array( $Instance, 'bonaire_mark_as_spam' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_move_to_trash', array( $Instance, 'bonaire_move_to_trash' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_submit_reply', array( $Instance, 'bonaire_submit_reply' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_save_options', array( $Instance, 'bonaire_save_options' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_reset_options', array( $Instance, 'bonaire_reset_options' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_send_testmail', array( $Instance, 'bonaire_send_testmail' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_test_smtp_settings', array( $Instance, 'bonaire_test_smtp_settings' ) ),
			'Failed to register method with WordPress.'
		);
		$this->assertSame(
			10,
			has_action( 'wp_ajax_bonaire_test_imap_settings', array( $Instance, 'bonaire_test_imap_settings' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
}
