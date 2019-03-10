<?php

/**
 * Class Bonaire_i18n_IntegrationTest
 */
class Bonaire_i18n_IntegrationTest extends WP_UnitTestCase {
	
	private $domain;
	
	public function setUp() {
		
		global $domain;
		
		$this->domain = $domain;
	}
	
	public function tearDown() {
		
		$this->domain = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_load_plugin_textdomain() {
		
		// Retrieve the plugin text domain which is registered with WordPress.
		$plugin_data = get_plugin_data( BONAIRE_ROOT_DIR . 'bonaire.php', true, true );
		$text_domain = $plugin_data['TextDomain'];
		
		$this->assertSame( $this->domain, $text_domain, 'Failed to register plugin text domain with WordPress.' );
	}
	
}
