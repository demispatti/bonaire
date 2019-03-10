<?php

/**
 * Class Bonaire_Launcher_IntegrationTest
 */
class Bonaire_Launcher_IntegrationTest extends WP_UnitTestCase {
	
	public function test_licence_file_included() {
		
		$license_file = BONAIRE_ROOT_DIR . 'license.txt';
		$this->assertFileExists( $license_file, 'File "' . $license_file . '" does not exist.' );
		$this->assertFileIsReadable( $license_file, 'File "' . $license_file . '" is not readable.' );
	}
	
	public function test_readme_file_included() {
		
		$readme_file = BONAIRE_ROOT_DIR . 'readme.txt';
		$this->assertFileExists( $readme_file, 'File "' . $readme_file . '" does not exist.' );
		$this->assertFileIsReadable( $readme_file, 'File "' . $readme_file . '" is not readable.' );
	}
	
	public function test_defined_constants() {
		
		$this->assertTrue( defined( 'BONAIRE_ROOT_DIR' ), 'Constant is not defined.' );
		$this->assertTrue( defined( 'BONAIRE_ROOT_URL' ), 'Constant is not defined.' );
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_add_hooks() {
		
		// Since we run the hooks we test from it's own file,
		// we need to add the path to that file for the test.
		// If we'd call the hooks from here this would be obsolete.
		$plugin_dir_path = substr( BONAIRE_ROOT_DIR, 1 );
		
		$Instance = new Bonaire\Bonaire_Launcher();
		$Instance->add_hooks();
		
		self::assertSame(
			10,
			has_action( 'activate_' . $plugin_dir_path . 'bonaire.php', array( $Instance, 'activate_bonaire' ) ),
			'Failed to register method with WordPress.'
		);
		self::assertSame(
			10,
			has_action( 'deactivate_' . $plugin_dir_path . 'bonaire.php', array( $Instance, 'deactivate_bonaire' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
	public function test_add_initial_action() {
		
		$Instance = new Bonaire\Bonaire_Launcher();
		add_action( 'plugins_loaded', array( $Instance, 'run_bonaire' ), 10 );
		
		$this->assertSame( 10, has_action( 'plugins_loaded', array( $Instance, 'run_bonaire' ) ),
			'Failed to register method with WordPress.'
		);
	}
	
}
