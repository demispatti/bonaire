<?php

use Bonaire\Admin\Includes as AdminIncludes;
use Bonaire\Admin\Partials as Partials;

/**
 * Class Bonaire_Meta_Box_IntegrationTest
 */
class Bonaire_Meta_Box_IntegrationTest extends WP_UnitTestCase {
	
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
	
	public function test_add_hooks() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		$stored_options = $Bonaire_Options->get_stored_options( 0 );
		$Bonaire_Reply_Form_Display = new Partials\Bonaire_Reply_Form_Display( $this->domain );
		$Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $stored_options );
		
		$Instance = new AdminIncludes\Bonaire_Meta_Box( $this->domain, $Bonaire_Reply_Form_Display, $Bonaire_Adapter, $Bonaire_Options );
		$Instance->add_hooks();
		
		$this->assertSame(
			10,
			has_action( 'load-flamingo_page_flamingo_inbound', array( $Instance, 'add_meta_box' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_add_meta_box() {
		
		$Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
		$stored_options = $Bonaire_Options->get_stored_options( 0 );
		$Bonaire_Reply_Form_Display = new Partials\Bonaire_Reply_Form_Display( $this->domain );
		$Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $stored_options );
		
		$Instance = new AdminIncludes\Bonaire_Meta_Box( $this->domain, $Bonaire_Reply_Form_Display, $Bonaire_Adapter, $Bonaire_Options );
		$Instance->add_meta_box();
		
		global $wp_meta_boxes;
		$meta_box_id = $wp_meta_boxes['flamingo_page_flamingo_inbound']['advanced']['default']['bonaire-form-meta-box']['id'];
		$this->assertSame( 'bonaire-form-meta-box', $meta_box_id, 'Failed to register meta box with WordPress.' );
	}
	
}
