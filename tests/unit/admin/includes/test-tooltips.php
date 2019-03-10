<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bonaire_Tooltips {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	// The "option keys"
	public static $additional_options = array(
		'save_options' => 'save_options',
		'reset_options' => 'reset_options',
		'test_smtp_settings' => 'test_smtp_settings',
		'test_imap_settings' => 'test_imap_settings',
		'send_testmail' => 'send_testmail'
	);
	
	/**
	 * @var object $options_meta
	 */
	private $options_meta;
	
	/**
	 * @var array $plugin_hook_suffixes
	 */
	public static $plugin_hook_suffixes = array(
		'settings_page' => 'settings_page_bonaire',
		'flamingo_inbound' => 'flamingo_page_flamingo_inbound',
		'dashboard' => 'index.php'
	);
	
	/*private function get_extended_options_meta() {
		
		$meta = $this->options_meta;
		
		foreach(self::$additional_options as $option) {
			
			$data = $this->$option();
			
			$meta->{$option} = $data;
		}
		
		return $meta;
	}*/
	
	/**
	 * Bonaire_Tooltips constructor.
	 *
	 * @param string $domain
	 * @param stdClass $options_meta
	 */
	public function __construct( $domain, $options_meta ) {
		
		$this->domain = $domain;
		$this->options_meta = $options_meta;
	}
	
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 40 );
	}
	
	private function compose_tooltip_args( $option_key ) {
		
		$arguments = $this->options_meta;
		$args['option_key'] = $option_key;
		$args['heading'] = $arguments->{$option_key}['name'];
		$args['image'] = $arguments->{$option_key}['tt_image'];
		$args['blockquote'] = $arguments->{$option_key}['tt_blockquote'];
		$args['description'] = $arguments->{$option_key}['tt_description'];
		$args['link_to_codex'] = $arguments->{$option_key}['tt_link_to_codex'];
		$args['link_to_source'] = $arguments->{$option_key}['tt_link_to_source'];
		
		return $args;
	}
	
	private function get_option_keys() {
		
		$list = array();
		foreach ( (array) $this->options_meta as $option => $attributes ) {
			$list[ $attributes['id'] ] = $attributes['id'];
		}
		
		return $list;
	}
	
	private function get_blockquote( $args ) {
		
		if ( $args['blockquote'] !== '' ) {
			
			return '<blockquote><strong>' . $args['option_key'] . '</strong>' . ' - ' . $args['blockquote'] . "</span><a class='bonaire-tooltip-link-to-codex' href='" . $args['link_to_codex'] . " ' target='_blank' >" . __( 'See Codex', $this->domain ) . '</a></blockquote>';
		}
		
		return '';
	}
	
	private function get_link_to_source( $args ) {
		
		if ( '' !== $args['link_to_source'] ) {
			
			return "<a class='bonaire-tooltip-link-to-source' href='" . $args['link_to_source'] . "' target='_blank' >" . __( 'See Codex', $this->domain ) . '</a>';
		}
		
		return '';
	}
	
	private function get_image( $args ) {
		
		if ( '' !== $args['image'] ) {
			
			return "<span class='bonaire-tooltip-image'>" . $args['image'] . '</span>';
		}
		
		return '';
	}
	
	private function get_heading( $args ) {
		
		if ( $args['heading'] !== '' ) {
			
			return "<h5 class='bonaire-tooltip-heading'>" . $args['heading'] . '</h5>';
		}
		
		return '';
	}
	
	private function get_description( $args ) {
		
		if ( $args['description'] !== '' ) {
			
			return "<p class='bonaire-tooltip-heading'>" . $args['description'] . '</p>';
		}
		
		return '';
	}
	
	private function save_options() {
		
		return array(
			'id' => 'save_options',
			'option_key' => 'save_options',
			'heading' => __( 'Save Settings', $this->domain ),
			'image' => '',
			'blockquote' => '',
			'description' => ''/*__( "", $this->domain )*/,
			'link_to_codex' => '',
			'link_to_source' => ''
		);
	}
	
	private function reset_options() {
		
		return array(
			'id' => 'reset_options',
			'option_key' => 'reset_options',
			'heading' => __( 'Reset Settings', $this->domain ),
			'image' => '',
			'blockquote' => '',
			'description' => ''/*__( "", $this->domain )*/,
			'link_to_codex' => '',
			'link_to_source' => ''
		);
	}
	
	private function test_smtp_settings() {
		
		return array(
			'id' => 'test_smtp_settings',
			'option_key' => 'test_smtp_settings',
			'heading' => __( 'Test SMTP Settings', $this->domain ),
			'image' => '',
			'blockquote' => '',
			'description' => ''/*__( "", $this->domain )*/,
			'link_to_codex' => '',
			'link_to_source' => ''
		);
	}
	
	private function test_imap_settings() {
		
		return array(
			'id' => 'test_imap_settings',
			'option_key' => 'test_imap_settings',
			'heading' => __( 'Test IMAP Settings', $this->domain ),
			'image' => '',
			'blockquote' => '',
			'description' => ''/*__( "", $this->domain )*/,
			'link_to_codex' => '',
			'link_to_source' => ''
		);
	}
	
	private function send_testmail() {
		
		return array(
			'id' => 'send_testmail',
			'option_key' => 'send_testmail',
			'heading' => __( 'Send Testmail', $this->domain ),
			'image' => '',
			'blockquote' => '',
			'description' => ''/*__( "", $this->domain )*/,
			'link_to_codex' => '',
			'link_to_source' => ''
		);
	}
	
	private function prepare_tooltip_content( $args ) {
		
		$html = "<div class='bonaire-tooltip-content'>";
		
		$html .= $this->get_blockquote( $args );
		
		$html .= $this->get_heading( $args );
		
		$html .= $this->get_image( $args );
		
		$html .= $this->get_description( $args );
		
		$html .= $this->get_link_to_source( $args );
		
		$html .= '</div>';
		
		return $html;
	}
	
	private function generate_tooltip_content() {
		
		$new_array = array();
		//$options_meta = $this->get_extended_options_meta();
		foreach ( (array) $this->options_meta as $option => $attribute ) {
			$new_array[ $attribute['id'] ] = $this->prepare_tooltip_content( $this->compose_tooltip_args( $attribute['id'] ) );
		}
		
		// Buttons and checkboxes that do not relate to a stored option, but will also be covered by a tooltip.
		$new_array['save_options'] = $this->prepare_tooltip_content( $this->save_options() );
		$new_array['reset_options'] = $this->prepare_tooltip_content( $this->reset_options() );
		$new_array['test_smtp_settings'] = $this->prepare_tooltip_content( $this->test_smtp_settings() );
		$new_array['test_imap_settings'] = $this->prepare_tooltip_content( $this->test_imap_settings() );
		$new_array['send_testmail'] = $this->prepare_tooltip_content( $this->send_testmail() );
		
		return $new_array;
	}
	
	public function localize_script( $hook_suffix ) {
		
		if ( ! in_array( $hook_suffix, self::$plugin_hook_suffixes, true ) ) {
			return;
		}
		
		$arr = array_merge(
			array( 'option_keys' => array_merge( $this->get_option_keys(), self::$additional_options ) ),
			array( 'tooltips' => $this->generate_tooltip_content() )
		);
		
		wp_localize_script(
			'bonaire-tooltips-js',
			'BonaireTooltipsObject',
			$arr
		);
	}
	
}
