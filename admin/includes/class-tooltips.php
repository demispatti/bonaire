<?php
namespace Bonaire\Admin\Includes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the tooltips for the settings page.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Tooltips {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the options meta data.
	 *
	 * @var object $options_meta
	 * @since    0.9.6
	 * @access   private
	 */
	private $options_meta;
	
	/**
	 * The option keys that identify the tooltips for the additional buttons.
	 *
	 * @var array $additional_option_keys
	 * @since    0.9.6
	 * @access   public static
	 */
	public static $additional_option_keys = array(
		'save_options' => 'save_options',
		'reset_options' => 'reset_options',
		'test_contact_form' => 'test_contact_form',
		'test_smtp_settings' => 'test_smtp_settings',
		'test_imap_settings' => 'test_imap_settings',
		'send_testmail' => 'send_testmail'
	);
	
	/**
	 * The whitelisted hook suffixes which determine
	 * if the Contextual Help will be displayed or not
	 * on the requested page.
	 *
	 * @var array $plugin_hook_suffixes
	 * @since    0.9.6
	 * @access   public static
	 */
	public static $plugin_hook_suffixes = array(
		'settings_page' => 'settings_page_bonaire',
		'flamingo_inbound' => 'flamingo_page_flamingo_inbound',
		'dashboard' => 'index.php'
	);
	
	/**
	 * Bonaire_Tooltips constructor.
	 *
	 * @param string $domain
	 * @param object $options_meta
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain, $options_meta ) {
		
		$this->domain       = $domain;
		$this->options_meta = $options_meta;
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 40 );
	}
	
	/**
	 * Assembles and returns the strings the tooltip is made out of.
	 *
	 * @param string $option_key
	 *
	 * @return array $args
	 * @since 0.9.6
	 */
	private function get_setting_args( $option_key ) {
		
		$arguments           = $this->options_meta;
		$args['option_key']  = $option_key;
		$args['image']       = $arguments->{$option_key}['tt_image'];
		$args['example']     = translate( $arguments->{$option_key}['example'], $this->domain );
		$args['heading']     = translate( $arguments->{$option_key}['name'], $this->domain );
		$args['description'] = translate( $arguments->{$option_key}['tt_description'], $this->domain );
		
		return $args;
	}
	
	/**
	 * Returns an array containing the option keys.
	 *
	 * @return array $list
	 * @since 0.9.6
	 */
	private function get_option_keys() {
		
		$list = array();
		foreach ( (array) $this->options_meta as $option => $attributes ) {
			// Exclude hidden field
			if ( 'form_id' !== $option ) {
				$list[ $attributes['id'] ] = $attributes['id'];
			}
		}
		
		return $list;
	}
	
	/**
	 * Returns the tooltip image.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_image_output( $args ) {
		
		$image = $args['image'];
		
		if ( '' !== $args['image'] ) {
			
			return "<div class='bonaire-tooltip-image'><img src='" . $image . "' alt='Tooltip Image' /></div>";
		}
		
		return '';
	}
	
	/**
	 * Returns the example part of the tooltip.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_example_output( $args ) {
		
		if ( $args['example'] !== '' ) {
			
			return '<div class="bonaire-tooltip-example"><h6>' . __( 'Example for Gmail', $this->domain ) . ':</h6><p>' . $args['example'] . '</p></div>';
		}
		
		return '';
	}
	
	/**
	 * Returns the heading part of the tooltip.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_heading_output( $args ) {
		
		if ( $args['heading'] !== '' ) {
			
			return "<h5 class='bonaire-tooltip-heading'>" . $args['heading'] . '</h5>';
		}
		
		return '';
	}
	
	/**
	 * Returns the description part of the tooltip.
	 *
	 * @param array $args
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_description_output( $args ) {
		
		if ( $args['description'] !== '' ) {
			
			return "<p class='bonaire-tooltip-description'>" . translate( $args['description'], $this->domain ) . '</p>';
		}
		
		return '';
	}
	
	/**
	 * Returns the argumants the 'save options' button consists of.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function save_options_button_args() {
		
		return array(
			'id' => 'save_options',
			'option_key' => 'save_options',
			'heading' => __( 'Save Settings', $this->domain ),
			'image' => '',
			'example' => '',
			'description' => __( 'Stores your settings in the database.', $this->domain )
		);
	}
	
	/**
	 * Returns the argumants the 'reset options' button consists of.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function reset_options_button_args() {
		
		return array(
			'id' => 'reset_options',
			'option_key' => 'reset_options',
			'heading' => __( 'Reset Settings', $this->domain ),
			'image' => '',
			'example' => '',
			'description' => __( 'Reset the settings.', $this->domain )
		);
	}
	
	
	private function test_contact_form_button_args() {
		
		return array(
			'id' => 'test_contact_form',
			'option_key' => 'test_contact_form',
			'heading' => __( 'Test Contact Form Tags', $this->domain ),
			'image' => '',
			'example' => '',
			'description' => __( 'Check the contact form for the required form tags.', $this->domain )
		);
	}
	
	/**
	 * Returns the argumants the 'test SMTP settings' button consists of.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function test_smtp_settings_button_args() {
		
		return array(
			'id' => 'test_smtp_settings',
			'option_key' => 'test_smtp_settings',
			'heading' => __( 'Test SMTP Settings', $this->domain ),
			'image' => '',
			'example' => '',
			'description' => __( 'Check the SMTP settings after saving your changes.', $this->domain )
		);
	}
	
	/**
	 * Returns the argumants the 'test IMAP settings' button consists of.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function test_imap_settings_button_args() {
		
		return array(
			'id' => 'test_imap_settings',
			'option_key' => 'test_imap_settings',
			'heading' => __( 'Test IMAP Settings', $this->domain ),
			'image' => '',
			'example' => '',
			'description' => __( 'Check the IMAP settings after saving your changes.', $this->domain )
		);
	}
	
	/**
	 * Returns the argumants the 'send testmail' button consists of.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function send_testmail_button_args() {
		
		return array(
			'id' => 'send_testmail',
			'option_key' => 'send_testmail',
			'heading' => __( 'Send Testmail', $this->domain ),
			'image' => '',
			'example' => '',
			'description' => __( 'Send a test message to this email account.', $this->domain )
		);
	}
	
	/**
	 * Assembles the parts a tooltip consists of and returns it as a string.
	 *
	 * @param array $args
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	private function compose_tooltip( $args ) {
		
		$html = "<div class='bonaire-tooltip'>";
		
		$html .= $this->get_heading_output( $args );
		
		$html .= $this->get_image_output( $args );
		
		$html .= $this->get_description_output( $args );
		
		$html .= $this->get_example_output( $args );
		
		$html .= '</div>';
		
		return $html;
	}
	
	/**
	 * Assembles and returns the contents for the tooltips
	 * that are available on the settings page.
	 *
	 * @return array $tooltips
	 * @since 0.9.6
	 */
	private function assemble_tooltip_content() {
		
		$tooltips = array();
		foreach ( (array) $this->options_meta as $option => $attr ) {
			$tooltips[ $attr['id'] ] = $this->compose_tooltip( $this->get_setting_args( $attr['id'] ) );
		}
		
		// Buttons and checkboxes that do not relate to a stored option, but will also be covered by a tooltip.
		$tooltips['save_options']       = $this->compose_tooltip( $this->save_options_button_args() );
		$tooltips['reset_options']      = $this->compose_tooltip( $this->reset_options_button_args() );
		$tooltips['test_contact_form']  = $this->compose_tooltip( $this->test_contact_form_button_args() );
		$tooltips['test_smtp_settings'] = $this->compose_tooltip( $this->test_smtp_settings_button_args() );
		$tooltips['test_imap_settings'] = $this->compose_tooltip( $this->test_imap_settings_button_args() );
		$tooltips['send_testmail']      = $this->compose_tooltip( $this->send_testmail_button_args() );
		
		return $tooltips;
	}
	
	/**
	 * Localizes the admin javascript file.
	 *
	 * @param string $hook_suffix
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function localize_script( $hook_suffix ) {
		
		if ( ! in_array( $hook_suffix, self::$plugin_hook_suffixes, true ) ) {
			return;
		}
		
		$data = array_merge(
			array( 'option_keys' => array_merge( $this->get_option_keys(), self::$additional_option_keys ) ),
			array( 'tooltips' => $this->assemble_tooltip_content() )
		);
		
		wp_localize_script( 'bonaire-tooltips-js', 'BonaireTooltipsObject', $data );
	}
	
}
