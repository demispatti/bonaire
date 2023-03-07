<?php
namespace Bonaire\Includes;

use Bonaire\Admin as BonaireAdmin;
use Bonaire\Pub as BonairePublic;
use Bonaire\Includes as BonaireIncludes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The file that defines the core plugin class
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://demispatti.ch
 * @since      0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/includes
 */

/**
 * The core plugin class.
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/includes
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire {
	
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.9.6
	 * @access   protected
	 * @var      string $name The string used to uniquely identify this plugin.
	 */
	private $name;
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * The current version of the plugin.
	 *
	 * @since    0.9.6
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	private $version;
	
	/**
	 * Defines the core functionality of the plugin.
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @param string $name
	 * @param string $domain
	 * @param string $version
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $name, $domain, $version ) {
		
		$this->name    = $name;
		$this->domain  = $domain;
		$this->version = $version;
	}
	
	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function init() {
		
		$this->include_locale();
		$this->include_admin();
		$this->include_public();
	}
	
	/**
	 * Defines the locale for this plugin for internationalization.
	 * Uses the Bonaire_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function include_locale() {
		
		require_once BONAIRE_ROOT_DIR . 'includes/class-i18n.php';
		
		$Bonaire_i18n = new BonaireIncludes\Bonaire_i18n( $this->domain );
		$Bonaire_i18n->add_hooks();
	}
	
	/**
	 * Registers all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_admin() {
		
		if ( ! is_admin() ) {
			return;
		}
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once BONAIRE_ROOT_DIR . 'admin/class-admin.php';
		
		$Bonaire_Admin = new BonaireAdmin\Bonaire_Admin( $this->name, $this->domain, $this->version );
		$Bonaire_Admin->add_hooks();
	}
	
	/**
	 * Registers all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_public() {
		
		if ( is_admin() ) {
			return;
		}
		
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once BONAIRE_ROOT_DIR . 'public/class-public.php';
		
		$Bonaire_Public = new BonairePublic\Bonaire_Public();
		$Bonaire_Public->add_hooks();
	}
	
}
