<?php
namespace Bonaire\Admin\Partials;

use Bonaire\Admin\Includes as AdminIncludes;
use WPCF7_ContactForm;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating and displaying the settings page.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Settings_Page_Display {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the instance of the class responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.6
	 * @access   public
	 */
	private $Bonaire_Options;
	
	/**
	 * Holds the stored options.
	 *
	 * @var object $stored_options
	 * @since    0.9.6
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Holds the options meta data.
	 *
	 * @var object $options_meta
	 * @since    0.9.6
	 * @access   private
	 */
	private $options_meta;
	
	/**
	 * Bonaire_Settings_Page_Display constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\ $Bonaire_Options
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain, $Bonaire_Options ) {
		
		$this->domain          = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->stored_options  = $this->Bonaire_Options->get_stored_options( 0 );
		$this->options_meta    = $this->Bonaire_Options->get_options_meta();
		$this->add_hooks();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
	}
	
	/**
	 * Returns the settings page content.
	 *
	 * @return string $html
	 * @toBeImplemented
	 * @since 0.9.6
	 */
	public function settings_page_display() {
		
		$form_nonce               = wp_create_nonce( 'bonaire_save_options_nonce' );
		$reset_options_nonce      = wp_create_nonce( 'bonaire_reset_options_nonce' );
		$send_test_message_nonce  = wp_create_nonce( 'bonaire_send_testmail_nonce' );
		$test_smtp_settings_nonce = wp_create_nonce( 'bonaire_test_smtp_settings_nonce' );
		$test_imap_settings_nonce = wp_create_nonce( 'bonaire_test_imap_settings_nonce' );
		$test_contact_form_nonce  = wp_create_nonce( 'bonaire_test_contact_form_nonce' );
		ob_start();
		?>
		<div class="wrap bonaire-settings-shell">
			<header class="bonaire-page-heading">
				<span class="bonaire-page-mark" aria-hidden="true"><span></span></span>
				<div>
					<p class="bonaire-page-eyebrow"><?php esc_html_e( 'Messages, connected', $this->domain ) ?></p>
					<h1 class="settings-page-title"><?php esc_html_e( 'Bonaire Settings', $this->domain ) ?></h1>
					<p class="bonaire-page-description"><?php esc_html_e( 'Keep contact-form messages, replies and mail connections in one calm workspace.', $this->domain ) ?></p>
				</div>
			</header>
        <div id="connection_details">
            <div class="header settings-form-title"><h3><?php esc_html_e( 'Email Account and further Settings', $this->domain ) ?></h3><a
                    class="information show-settings"
                    href="#"><?php esc_html_e( 'Information', $this->domain ) ?></a></div>
            <!-- Options Form -->
            <form id="bonaire_settings_form" data-nonce="<?php echo esc_attr( $form_nonce ); ?>" method="post">
                <div class="content">
                    <!-- Input Fields -->
					<?php
					$string = '';
					foreach ( (array) $this->options_meta as $key => $args ) {
						$value = isset( $this->stored_options->{$key} ) ? $this->stored_options->{$key} : '';
						
						if ( 'form_id' === $key ) {
							$string .= '<input type="hidden" value="' . esc_attr( $value ) . '" data-form-input="bonaire" data-key="form_id" name="bonaire_options[form_id]"/>';
						} else {
							// Settings Section Titles
							$string .= 'channel' === $key ? '<div class="cf7"><h5 class="content-section-title">' . __( 'Contact Form 7 Settings', $this->domain ) . '</h5>' . $this->get_status_display( 'cf7' ) : '';
							$string .= 'number_posts' === $key ? '<div class="dashboard"><h5 class="content-section-title">' . __( 'Dashboard Widget Settings', $this->domain ) . '</h5>' : '';
							$string .= 'username' === $key ? '<div class="smtp"><h5 class="content-section-title">' . __( 'SMTP Settings', $this->domain ) . '</h5>' . $this->get_status_display( 'smtp' ) : '';
							$string .= 'save_reply' === $key ? '<div class="imap"><h5 class="content-section-title">' . __( 'IMAP Settings', $this->domain ) . '</h5>' . $this->get_status_display( 'imap' ) : '';
							
							// Settings
							$string .= $this->get_settings_field( $key, $value, $this->options_meta );
							// Close title div
							$string .= 'channel' === $key || 'your_message' === $key || 'number_posts' === $key || 'from' === $key || 'ssl_certification_validation' === $key ? '</div>' : '';
						}
					}
					echo $string;
					?>
                    <div class="buttons-container">
                        <div class="buttons">
	                        <h5 class="content-section-title"><?php esc_html_e( 'Reset And Save Settings', $this->domain ) ?></h5>
                            <!-- Reset Button -->
                            <div class="button-container reset-button-container">
                                <label for="bonaire_options[reset_options]"></label>
                                <input class="button button-secondary bonaire-reset-options-button" type="submit"
                                    value="<?php esc_html_e( 'Reset Settings', $this->domain ) ?>" name="bonaire_options[reset_options]"
                                    data-nonce="<?php echo $reset_options_nonce ?>"/>
                            </div>
                            <!-- Submit Button -->
                            <div class="button-container submit-button-container">
                                <label for="bonaire_options[save_options]"></label>
                                <input class="button button-primary bonaire-save-options-button" type="submit"
                                    value="<?php esc_html_e( 'Save Settings', $this->domain ) ?>" name="bonaire_options[save_options]"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="footer">
                <h5 class="content-section-title"><?php esc_html_e( 'Test Settings', $this->domain ) ?></h5>
                <div>
                    <!-- Test Contact Form Button -->
                    <div class="button-container test-contact-form-button-container">
                        <label for="bonaire_options[test_contact_form]"></label>
                        <input class="button button-secondary bonaire-test-contact-form-button" type="submit"
                            value="<?php esc_html_e( 'Test Contact Form Tags', $this->domain ) ?>" name="bonaire_options[test_contact_form]"
                            data-nonce="<?php echo $test_contact_form_nonce ?>"/>
                    </div>
                    <!-- Test SMTP Settings Button -->
                    <div class="button-container test-smtp-settings-button-container">
                        <label for="bonaire_options[test_smtp_settings]"></label>
                        <input class="button button-secondary bonaire-test-smtp-settings-button" type="submit"
                            value="<?php esc_html_e( 'Test SMTP Settings', $this->domain ) ?>" name="bonaire_options[test_smtp_settings]"
                            data-nonce="<?php echo $test_smtp_settings_nonce ?>"/>
                    </div>
                    <!-- Test IMAP Settings Button -->
                    <div class="button-container test-imap-settings-button-container">
                        <label for="bonaire_options[test_imap_settings]"></label>
                        <input id="bonaire_options[test_imap_settings]" class="button button-secondary bonaire-test-imap-settings-button"
                            type="submit"
                            value="<?php esc_html_e( 'Test IMAP Settings', $this->domain ) ?>" name="bonaire_options[test_imap_settings]"
                            data-nonce="<?php echo $test_imap_settings_nonce ?>"/>
                    </div>
                    <!-- Send Testmail Button -->
                    <div class="button-container send-testmail-button-container">
                        <label for="bonaire_options[send_testmail]"></label>
                        <input id="bonaire_options[send_testmail]" class="button button-secondary bonaire-send-testmail-button" type="submit"
                            value="<?php esc_html_e( 'Send Testmail', $this->domain ) ?>" name="bonaire_options[send_testmail]"
                            data-nonce="<?php echo $send_test_message_nonce ?>"/>
                    </div>
                </div>
            </div>
        </div>
		</div>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns the strings that describe the three 'settings statuss'.
	 *
	 * @return array
	 * @since 0.9.6
	 */
	private function get_strings() {
		
		return array(
			'cf7' => array(
				'orange' => __( 'Settings check not successful.', $this->domain ),
				'green' => __( 'Settings check successful (since last changes).', $this->domain ),
				'inactive' => __( 'Inactive.', $this->domain )
			),
			'smtp' => array(
				'orange' => __( 'Settings check not successful.', $this->domain ),
				'green' => __( 'Settings check successful (since last changes).', $this->domain ),
				'inactive' => __( 'Inactive.', $this->domain )
			),
			'imap' => array(
				'orange' => __( 'Settings check not successful (since last changes).', $this->domain ),
				'green' => __( 'Settings check successful.', $this->domain ),
				'inactive' => __( 'Inactive.', $this->domain )
			)
		);
	}
	
	/**
	 * Returns the settings field based on it's type.
	 *
	 * @param string $key
	 * @param string $value
	 * @param object $options_meta
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_settings_field( $key, $value, $options_meta ) {
		
		$type = $options_meta->{$key}['type'];
		
		switch ( $type ) {
			case 'text':
				{
					
					return $this->get_settings_field_for_text( $key, $value, $options_meta );
				}
				break;
			case 'dropdown':
				{
					
					return $this->get_settings_field_for_dropdown( $key, $value, $options_meta );
				}
				break;
			case 'checkbox':
				{
					
					return $this->get_settings_field_for_checkbox( $key, $value, $options_meta );
				}
				break;
			
			default:
				return '';
		}
	}
	
	/**
	 * Creates a container holding a text input with associated label.
	 *
	 * @param string $key
	 * @param string $value
	 * @param object $options_meta
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	private function get_settings_field_for_text( $key, $value, $options_meta ) {
		
		$data_form_input = 'bonaire';
		
		if ( 'password' === $key && '' !== $value ) {
			$value = '*****';
		}
		
		$disabled = '';
		$type     = 'password' === $key ? 'password' : 'text';
		if ( 'smtpauth' === $key || 'your_name' === $key || 'your_email' === $key || 'your_subject' === $key || 'your_message' === $key) {
			$disabled = 'disabled';
		}
		if ( 'smtpauth' === $key ) {
			$value    = 'true';
		}
		
		if ( 'smtpauth' === $key || 'test_address' === $key ) {
			$data_form_input = '';
		}
		
		$label         = translate( $options_meta->{$key}['name'], $this->domain );
		$name          = 'bonaire_options[' . $key . ']';
		$default_value = translate( $options_meta->{$key}['default_value'], $this->domain );
		
		ob_start();
		?>
        <div>
            <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
            <input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ); ?>"
                data-form-input="<?php echo esc_attr( $data_form_input ); ?>" data-key="<?php echo esc_attr( $key ); ?>"
                data-default-value="<?php echo esc_attr( $default_value ); ?>" <?php echo esc_attr( $disabled ); ?>/>
        </div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Creates a container holding a select field with associated label.
	 *
	 * @param string $key
	 * @param string $value
	 * @param object $options_meta
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	private function get_settings_field_for_dropdown( $key, $value, $options_meta ) {
		
		
		$label         = translate( $options_meta->{$key}['name'], $this->domain );
		$name          = 'bonaire_options[' . $key . ']';
		$default_value = $options_meta->{$key}['default_value'];
		
		if ( 'channel' === $key ) {
			$select_values = array_merge( $this->get_contact_form_titles_List(), $options_meta->{$key}['values'] );
			$key           = preg_match( '/_/', $key ) ? str_replace( '_', '', $key ) : $key;
		} else {
			$select_values = $options_meta->{$key}['values'];
		}
		
		ob_start();
		?>
        <div>
            <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
            <select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" data-form-input="bonaire"
                data-key="<?php echo esc_attr( $key ); ?>" data-default-value="<?php echo esc_attr( $default_value ); ?>">
				<?php
				foreach ( $select_values as $key => $select_value ) {
					$key   = (string) $key;
					$value = (string) $value;
					?>
                    <option
                        value="<?php echo esc_attr( $key ); ?>" <?php echo selected( $value, $key, false ); ?> ><?php echo esc_html( translate( $select_value, $this->domain ) ); ?></option>
					<?php
				} ?>
            </select>
        </div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Creates a container holding a checkbox field with associated label.
	 *
	 * @param $key
	 * @param $value
	 * @param $options_meta
	 *
	 * @return string $html
	 * @since 0.9.6
	 */
	private function get_settings_field_for_checkbox( $key, $value, $options_meta ) {
		
		$label         = translate( $options_meta->{$key}['name'], $this->domain );
		$name          = 'bonaire_options[' . $key . ']';
		$default_value = translate( $options_meta->{$key}['default_value'], $this->domain );
		
		$data_form_input = 'bonaire';
		
		ob_start();
		?>
        <div>
            <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $label ); ?></label>
            <input id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" type="checkbox" value="<?php echo esc_attr( $value ); ?>"
                data-form-input="<?php echo esc_attr( $data_form_input ); ?>" data-key="<?php echo esc_attr( $key ); ?>" data-default-value="<?php echo esc_attr( $default_value ); ?>"/>
        </div>
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	/**
	 * Returns a string containing the element that indicates the email account 'settings status'.
	 *
	 * @param string $protocol
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_status_display( $protocol ) {
		
		$strings          = $this->get_strings();
		$plugin_options   = $this->Bonaire_Options->get_stored_options( 1 );
		$account_settings = $this->Bonaire_Options->get_stored_options( 0 );
		$settings_status  = isset( $plugin_options->{$protocol . '_status'} ) ? $plugin_options->{$protocol . '_status'} : 'orange';
		$settings_status  = in_array( $settings_status, array( 'orange', 'green', 'inactive' ), true ) ? $settings_status : 'orange';
		
		if ( isset( $protocol ) && 'imap' === $protocol && 'no' === $account_settings->save_reply ) {
			$settings_status = 'inactive';
		}
		
		$status_text = $strings[ $protocol ][ $settings_status ];

		return '<span class="status-indicator ' . esc_attr( $settings_status ) . '" role="status" aria-label="' . esc_attr( $status_text ) . '"><i aria-hidden="true" title="' . esc_attr( $status_text ) . '"></i></span>';
	}
	
	/**
	 * Returns a list of 'channels' which represent a Contact Form 7 contact form.
	 *
	 * @return array $list
	 * @since 0.9.6
	 */
	private function get_contact_form_titles_List() {
		
		$contactforms = WPCF7_ContactForm::find();
		
		$list = array();
		if ( empty( $contactforms ) || false === $contactforms ) {
			return $list;
		}
		
		/**
		 * @var WPCF7_ContactForm $item
		 */
		foreach ( $contactforms as $item ) {
			$list[ '_' . $item->id() ] = $item->title();
		}
		
		return $list;
	}
	
	/**
	 * Localizes the javascript file for the admin part of the plugin.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function localize_script() {
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireSettingsPageDisplay', array( 'strings' => $this->get_strings() ) );
	}
	
}
