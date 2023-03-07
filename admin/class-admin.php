<?php
namespace Bonaire\Admin;

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'AdminIncludes\Bonaire_Settings_Page' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-page.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Dashboard_Widget' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-dashboard-widget.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Post_Views' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-post-views.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Contextual_Help' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-contextual-help.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Meta_Box' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-meta-box.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Mail' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-mail.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Account_Evaluator' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-evaluator.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Settings_Status' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-settings-status.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Ajax' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-ajax.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Tooltips' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-tooltips.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Options' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-options.php';
}
if ( ! class_exists( 'AdminIncludes\Bonaire_Adapter' ) && file_exists( BONAIRE_PLUGINS_ROOT_DIR . 'flamingo/includes/class-inbound-message.php' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/includes/class-adapter.php';
}

/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @since      0.9.6
 * @package    Bonaire
 * @subpackage Bonaire/admin
 * @author     Demis Patti <demispatti@gmail.com>
 */
class Bonaire_Admin {
	
	/**
	 * The name of the plugin.
	 *
	 * @var      string $name
	 * @since    0.9.6
	 * @access   public
	 */
	public $name;
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   public
	 */
	public $domain;
	
	/**
	 * The version of the plugin.
	 *
	 * @var      string $version
	 * @since    0.9.6
	 * @access   public
	 */
	public $version;
	
	/**
	 * Holds the whitelisted hook suffixes which determine
	 * if the Contextual Help will be displayed or not
	 * on the requested page.
	 *
	 * @var      array $plugin_hook_suffixes
	 * @since    0.9.6
	 * @access   public static
	 */
	public static $plugin_hook_suffixes = array(
		'settings_page' => 'settings_page_bonaire',
		'flamingo_inbound' => 'flamingo_page_flamingo_inbound',
		'dashboard' => 'index.php',
		'doing_ajax' => 'admin-ajax.php'
	);
	
	/**
	 * Holds the names (slugs) of the pages
	 * the plugin will load its related classes (or not).
	 *
	 * @var      array $plugin_pages
	 * @since    0.9.6
	 * @access   public static
	 */
	public static $plugin_pages = array(
		'dashboard' => 'index.php',
		'flamingo_inbound' => 'flamingo_inbound',
		'settings_page' => 'bonaire.php'
	);
	
	/**
	 * Holds the instance of the class responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Holds the instance of the class responsible for keeping track of the message views.
	 *
	 * @var AdminIncludes\Bonaire_Post_Views $Bonaire_Post_Views
	 * @since    0.9.6
	 * @access   public
	 */
	public $Bonaire_Post_Views;
	
	/**
	 * Holds the instance of the class responsible for connecting to Contact Form 7 and Flamingo.
	 *
	 * @var AdminIncludes\Bonaire_Adapter Bonaire_Adapter
	 * @since    0.9.6
	 * @access   public
	 */
	public $Bonaire_Adapter;
	
	/**
	 * Holds the instance of the class responsible for sending messages.
	 *
	 * @var AdminIncludes\Bonaire_Mail $Bonaire_Mail
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Mail;
	
	/**
	 * Holds the instance of the class responsible for evaluating the email account settings.
	 *
	 * @var AdminIncludes\Bonaire_Settings_Evaluator $Bonaire_Account_Evaluator
	 * @since    1.0.0
	 * @access   private
	 */
	private $Bonaire_Account_Evaluator;
	
	/**
	 * Holds the instance of the class responsible for handling the email account settings status.
	 *
	 * @var AdminIncludes\Bonaire_Settings_Status $Bonaire_Settings_Status
	 * @since    1.0.0
	 * @access   private
	 */
	private $Bonaire_Settings_Status;
	
	/**
	 * Set the options instance.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function set_options_instance() {
		
		$this->Bonaire_Options = new AdminIncludes\Bonaire_Options( $this->domain );
	}
	
	/**
	 * Set the post views instance.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function set_post_views_instance() {
		
		$this->Bonaire_Post_Views = new AdminIncludes\Bonaire_Post_Views( $this->domain );
	}
	
	/**
	 * Bonaire_Admin constructor.
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
		
		$this->set_options_instance();
		$this->set_post_views_instance();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'init', array( $this, 'init_dependencies' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_update_post' ), 11 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}
	
	/**
	 * Adds the recipient email address as post meta data to the newly recieved message(s).
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function maybe_update_post() {
		
		if ( false !== get_transient( 'bonaire_wpcf7_queue' ) ) {
			$Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $this->Bonaire_Options->get_stored_options( 1 ) );
			$Bonaire_Adapter->update_post();
		}
	}
	
	/**
	 * Loads the dependencies this plugin relies on.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function init_dependencies() {
		
		if ( defined( 'FLAMINGO_PLUGIN' ) && defined( 'WPCF7_PLUGIN' ) && current_user_can( 'administrator' ) ) {
			
			// Display on Dashboard, Message Edit Screen or on the Plugin Settings Page
			if ( is_admin() ) {
				$this->include_options();
				$this->include_bonaire_mail();
				$this->include_account_evaluator();
				$this->include_settings_status();
				$this->include_ajax();
				$this->include_contextual_help();
				$this->include_tooltips();
			}
			$this->include_settings_page();
			$this->include_post_views();
			
			// Dashboard
			if ( is_admin() ) {
				$this->include_dashboard_widget();
			}
			
			// Flamingo Inbound
			if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'edit' ) && ( isset( $_REQUEST['page'] ) && self::$plugin_pages['flamingo_inbound'] === $_REQUEST['page'] ) ) {
				$this->include_required_plugins_adapter();
				$this->include_meta_box();
			}
		}
	}
	
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @param string $hook_suffix
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function enqueue_styles( $hook_suffix ) {
		
		$prefix = ( defined( 'BONAIRE_SCRIPT_DEBUG' ) ) ? '' : '.min';
		
		if ( in_array( $hook_suffix, self::$plugin_hook_suffixes, true ) ) {
			
			wp_enqueue_style( 'dashicons' );
			
			wp_enqueue_style( 'jquery-ui-smoothness',
				BONAIRE_ROOT_URL . "vendor/jquery-ui/jquery-ui.min.css",
				array(),
				'all',
				'all'
			);
			
			// Tooltipster
			wp_enqueue_style( 'bonaire-inc-tooltipster-core-css',
				BONAIRE_ROOT_URL . 'vendor/tooltipster/css/tooltipster.core' . $prefix . '.css',
				array(),
				'all',
				'all'
			);
			wp_enqueue_style( 'bonaire-inc-tooltipster-bundle-css',
				BONAIRE_ROOT_URL . 'vendor/tooltipster/css/tooltipster.bundle' . $prefix . '.css',
				array(),
				'all',
				'all'
			);
			wp_enqueue_style( 'bonaire-inc-tooltipster-theme-shadow-css',
				BONAIRE_ROOT_URL . 'vendor/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css',
				array(),
				'all',
				'all'
			);
			
			// Alertify
			wp_enqueue_style( 'bonaire-inc-alertify-min-css',
				BONAIRE_ROOT_URL . 'vendor/alertify/css/alertify' . $prefix . '.css',
				array(),
				'all',
				'all'
			);
			wp_enqueue_style( 'bonaire-inc-alertify-theme-bootstrap-min-css',
				BONAIRE_ROOT_URL . 'vendor/alertify/css/themes/bootstrap' . $prefix . '.css',
				array(),
				'all',
				'all'
			);
		}
		
		// Admin
		wp_enqueue_style( 'bonaire-admin-css',
			BONAIRE_ROOT_URL . 'admin/css/bonaire-admin' . $prefix . '.css',
			array(),
			'all',
			'all'
		);
	}
	
	/**
	 * Registers the JavaScript for the admin area.
	 *
	 * @param string $hook_suffix
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function enqueue_scripts( $hook_suffix ) {
		
		$prefix = ( defined( 'BONAIRE_SCRIPT_DEBUG' ) ) ? '' : '.min';
		
		if ( in_array( $hook_suffix, self::$plugin_hook_suffixes, true ) ) {
			
			// Tooltipster
			wp_enqueue_script( 'bonaire-inc-tooltipster-core-min-js',
				BONAIRE_ROOT_URL . 'vendor/tooltipster/js/tooltipster.core' . $prefix . '.js',
				array( 'jquery' ),
				'all',
				true
			);
			wp_enqueue_script( 'bonaire-inc-tooltipster-svg-min-js',
				BONAIRE_ROOT_URL . 'vendor/tooltipster/js/plugins/tooltipster/SVG/tooltipster-SVG' . $prefix . '.js',
				array( 'jquery', 'bonaire-inc-tooltipster-core-min-js' ),
				'all',
				true
			);
			wp_enqueue_script( 'bonaire-inc-tooltipster-bundle-min-js',
				BONAIRE_ROOT_URL . 'vendor/tooltipster/js/tooltipster.bundle' . $prefix . '.js',
				array( 'jquery', 'bonaire-inc-tooltipster-svg-min-js' ),
				'all',
				true
			);
			
			// Tooltips
			wp_enqueue_script( 'bonaire-tooltips-js',
				BONAIRE_ROOT_URL . 'admin/js/tooltips' . $prefix . '.js',
				array( 'jquery', 'bonaire-inc-tooltipster-bundle-min-js' ),
				'all',
				true
			);
			
			// Alertify
			wp_enqueue_script( 'bonaire-inc-alertify-min-js',
				BONAIRE_ROOT_URL . 'vendor/alertify/alertify' . $prefix . '.js',
				array( 'jquery' ),
				'all',
				true
			);
			
			// jQuery UI Libs
			wp_enqueue_script( 'jquery-ui' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-widget' );
			wp_enqueue_script( 'jquery-effects-core' );
			
			// Admin
			wp_enqueue_script( 'bonaire-admin-js',
				BONAIRE_ROOT_URL . 'admin/js/bonaire-admin' . $prefix . '.js',
				array(
					'jquery',
					'media-views',
					'jquery-ui-core',
					'jquery-ui-tabs',
					'jquery-ui-widget',
					'jquery-effects-core',
					'bonaire-inc-alertify-min-js'
				),
				'all',
				true
			);
		}
	}
	
	/**
	 * Includes the class that connects to Contact Form 7 and Flamingo.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_required_plugins_adapter() {
		
		if ( file_exists( BONAIRE_PLUGINS_ROOT_DIR . 'flamingo/includes/class-inbound-message.php' ) ) {
			/**
			 * The class responsible for interacting with 'Contact Form 7' and 'Flamingo'.
			 */
			$this->Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $this->Bonaire_Options->get_stored_options( 0 ) );
		}
	}
	
	/**
	 * Includes the dashboard widget.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_dashboard_widget() {
		
		/**
		 * The class responsible for the dashboard widget.
		 */
		$Bonaire_Dashboard_Widget = new AdminIncludes\Bonaire_Dashboard_Widget( $this->domain, $this->Bonaire_Options );
		$Bonaire_Dashboard_Widget->add_hooks();
	}
	
	/**
	 * Includes the settings page.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_settings_page() {
		
		/**
		 * The class responsible for the settings page.
		 */
		$Bonaire_Settings_Page = new AdminIncludes\Bonaire_Settings_Page( $this->domain, $this->Bonaire_Options );
		$Bonaire_Settings_Page->add_hooks();
	}
	
	/**
	 * Includes the class responsible for sending emails.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_bonaire_mail() {
		
		/**
		 * The class responsible for the mailing functionality.
		 */
		$this->Bonaire_Mail = new AdminIncludes\Bonaire_Mail( $this->domain, $this->Bonaire_Options );
	}
	
	/**
	 * Includes the class responsible for sending emails.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function include_account_evaluator() {
		
		/**
		 * The class responsible for the mailing functionality.
		 */
		$this->Bonaire_Account_Evaluator = new AdminIncludes\Bonaire_Settings_Evaluator( $this->domain, $this->Bonaire_Options );
	}
	
	/**
	 * The class responsible for handling the email account settings status.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	private function include_settings_status() {
		
		$this->Bonaire_Settings_Status = new AdminIncludes\Bonaire_Settings_Status( $this->domain );
	}
	
	/**
	 * Includes the meta box.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_meta_box() {
		
		/**
		 * The class responsible for the meta box containing the reply form.
		 */
		$Bonaire_Meta_Box = new AdminIncludes\Bonaire_Meta_Box( $this->domain, $this->Bonaire_Adapter, $this->Bonaire_Options );
		$Bonaire_Meta_Box->add_hooks();
	}
	
	/**
	 * includes the help tab.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_contextual_help() {
		
		/**
		 * The class responsible for the help tab.
		 */
		$Bonaire_Contextual_Help = new AdminIncludes\Bonaire_Contextual_Help( $this->domain );
		$Bonaire_Contextual_Help->add_hooks();
	}
	
	/**
	 * Tracks the post views in order to
	 * display or hide the message excerpt in the dashboard widget.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_post_views() {
		
		/**
		 * The class responsible for tracking read messages.
		 */
		$this->Bonaire_Post_Views->add_hooks();
	}
	
	/**
	 * Includes the class responsible for handling the stored options.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_options() {
		
		/**
		 * The class that manages the options.
		 */
		$this->Bonaire_Options->add_hooks();
	}
	
	/**
	 * Includes the ajax functionality.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_ajax() {
		
		/**
		 * The class responsible for this plugin's ajax functionality.
		 */
		$Bonaire_Ajax = new AdminIncludes\Bonaire_Ajax( $this->domain, $this->Bonaire_Options, $this->Bonaire_Post_Views, $this->Bonaire_Mail, $this->Bonaire_Account_Evaluator, $this->Bonaire_Settings_Status );
		$Bonaire_Ajax->add_hooks();
	}
	
	/**
	 * Includes the tooltips displayed on the settings page.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function include_tooltips() {
		
		/**
		 * The class responsible for this plugin's tooltips.
		 */
		$Bonaire_Tooltips = new AdminIncludes\Bonaire_Tooltips( $this->domain, $this->Bonaire_Options->get_options_meta() );
		$Bonaire_Tooltips->add_hooks();
	}
	
	/**
	 * Adds support, rating, and donation links to the plugin row meta to the plugins admin screen.
	 *
	 * @param array $meta
	 * @param string $file
	 *
	 * @return array  $meta
	 * @since  1.0.0
	 */
	public function plugin_row_meta( $meta, $file ) {
		
		$plugin = plugin_basename( 'bonaire/bonaire.php' );
		
		if ( $file === $plugin ) {
			$meta[] = '<a href="https://wordpress.org/support/plugin/bonaire" target="_blank">' . __( 'Plugin Support', $this->domain ) . '</a>';
			$meta[] = '<a href="https://wordpress.org/support/view/plugin-reviews/bonaire" target="_blank">' . __( 'Rate Plugin', $this->domain ) . '</a>';
			$meta[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XLMMS7C62S76Q" target="_blank">' . __( 'Donate', $this->domain ) . '</a>';
		}
		
		return $meta;
	}
	
}
