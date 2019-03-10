<?php

/**
 * Class Bonaire_i18n_FunctionalTest
 */
class Bonaire_i18n_FunctionalTest extends WP_UnitTestCase {
	
	private $domain;
	
	public function setUp() {
		
		global $domain;
		
		$this->domain = $domain;
		
		require_once BONAIRE_ROOT_DIR . 'includes/class-i18n.php';
	}
	
	public function tearDown() {
		
		$this->domain = null;
	}
	
	public function __construct() {
		
		parent::__construct();
	}
	
	public function test_constructor_with_arguments() {
		
		$classname = 'Bonaire\Includes\Bonaire_i18n';
		
		$Class = new $classname( $this->domain );
		
		$this->assertObjectHasAttribute( 'domain', $Class, 'Attribute "domain" does not exist.' );
		$this->assertAttributeEquals( 'bonaire', 'domain', $Class, 'Attribute "domain": value is not as expected.' );
	}
	
	public function test_language_files() {
		
		$pot_file_name = 'bonaire.pot';
		$de_de_mo_name = 'bonaire-de_DE.mo';
		$de_de_po_name = 'bonaire-de_DE.po';
		
		// Test if pot file exists and is readable
		$pot_file = BONAIRE_ROOT_DIR . 'languages/' . $pot_file_name;
		$this->assertFileExists( $pot_file, 'File "' . $pot_file . '" does not exist.' );
		$this->assertFileIsReadable( $pot_file, 'File "' . $pot_file . '" is not readable.' );
		
		// Test if po file exists and is readable
		$de_de_mo = BONAIRE_ROOT_DIR . 'languages/' . $de_de_mo_name;
		$this->assertFileExists( $de_de_mo, 'File "' . $de_de_mo . '" does not exist.' );
		$this->assertFileIsReadable( $de_de_mo, 'File "' . $de_de_mo . '" is not readable.' );
		
		// Test if mo file exists and is readable
		$de_de_po = BONAIRE_ROOT_DIR . 'languages/' . $de_de_po_name;
		$this->assertFileExists( $de_de_po, 'File "' . $de_de_po . '" does not exist.' );
		$this->assertFileIsReadable( $de_de_po, 'File "' . $de_de_po . '" is not readable.' );
	}
	
}
