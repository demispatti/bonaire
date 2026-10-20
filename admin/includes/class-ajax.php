<?php
namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Includes as AdminIncludes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for the ajax functionality.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Ajax {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the instance responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Holds the instance responsible for keeping track of the message views.
	 *
	 * @var AdminIncludes\Bonaire_Post_Views $Bonaire_Post_Views
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Post_Views;
	
	/**
	 * Holds the instance responsible for sending messages.
	 *
	 * @var AdminIncludes\Bonaire_Mail $Bonaire_Mail
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Mail;
	
	/**
	 * Holds the instance responsible for evaluating the email account settings.
	 *
	 * @var AdminIncludes\Bonaire_Settings_Evaluator $Bonaire_Account_Settings_Evaluator
	 * @since    1.0.0
	 * @access   private
	 */
	private $Bonaire_Account_Settings_Evaluator;
	
	/**
	 * Holds the instance responsible for evaluating the email account settings.
	 *
	 * @var AdminIncludes\Bonaire_Settings_Status $Bonaire_Account_Settings_Status
	 * @since    1.0.0
	 * @access   private
	 */
	private $Bonaire_Account_Settings_Status;
	
	/**
	 * Holds the stored options.
	 *
	 * @var object $stored_options
	 * @since    0.9.6
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Holds the error text for failed nonce checks
	 *
	 * @var string $nonce_error_text
	 * @since    0.9.6
	 * @access   private
	 */
	private $nonce_error_text;
	
	/**
	 * Bonaire_Ajax constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @param AdminIncludes\Bonaire_Post_Views $Bonaire_Post_Views
	 * @param AdminIncludes\Bonaire_Mail $Bonaire_Mail
	 * @param AdminIncludes\Bonaire_Settings_Evaluator $Bonaire_Account_Evaluator
	 * @param AdminIncludes\Bonaire_Settings_Status $Bonaire_Account_Settings_Status
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain, $Bonaire_Options, $Bonaire_Post_Views, $Bonaire_Mail, $Bonaire_Account_Evaluator, $Bonaire_Account_Settings_Status ) {
		
		$this->domain                             = $domain;
		$this->Bonaire_Options                    = $Bonaire_Options;
		$this->Bonaire_Post_Views                 = $Bonaire_Post_Views;
		$this->Bonaire_Mail                       = $Bonaire_Mail;
		$this->Bonaire_Account_Settings_Evaluator = $Bonaire_Account_Evaluator;
		$this->Bonaire_Account_Settings_Status    = $Bonaire_Account_Settings_Status;
		$this->stored_options                     = $Bonaire_Options->get_stored_options( 0 );
		$this->nonce_error_text                   = __( 'That won\'t do.', "bonaire" );
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		$actions = array(
			'bonaire_mark_as_read',
			'bonaire_mark_as_spam',
			'bonaire_move_to_trash',
			'bonaire_submit_reply',
			'bonaire_save_options',
			'bonaire_reset_options',
			'bonaire_send_testmail',
			'bonaire_test_contact_form',
			'bonaire_test_smtp_settings',
			'bonaire_test_imap_settings',
			'bonaire_get_settings_status',
		);

		foreach ( $actions as $action ) {
			add_action( 'wp_ajax_' . $action, array( $this, 'authorize_ajax_request' ), 1 );
		}

		// Dashboard
		add_action( 'wp_ajax_bonaire_mark_as_read', array( $this, 'bonaire_mark_as_read' ) );
		add_action( 'wp_ajax_bonaire_mark_as_spam', array( $this, 'bonaire_mark_as_spam' ) );
		add_action( 'wp_ajax_bonaire_move_to_trash', array( $this, 'bonaire_move_to_trash' ) );
		// Flamingo Inbound
		add_action( 'wp_ajax_bonaire_submit_reply', array( $this, 'bonaire_submit_reply' ) );
		// Settings Page
		add_action( 'wp_ajax_bonaire_save_options', array( $this, 'bonaire_save_options' ) );
		add_action( 'wp_ajax_bonaire_reset_options', array( $this, 'bonaire_reset_options' ) );
		add_action( 'wp_ajax_bonaire_send_testmail', array( $this, 'bonaire_send_testmail' ) );
		add_action( 'wp_ajax_bonaire_test_contact_form', array( $this, 'bonaire_test_contact_form' ) );
		add_action( 'wp_ajax_bonaire_test_smtp_settings', array( $this, 'bonaire_test_smtp_settings' ) );
		add_action( 'wp_ajax_bonaire_test_imap_settings', array( $this, 'bonaire_test_imap_settings' ) );
		add_action( 'wp_ajax_bonaire_get_settings_status', array( $this, 'bonaire_get_settings_status' ) );
	}

	/**
	 * Blocks every Bonaire AJAX action unless the current user may manage settings.
	 */
	public function authorize_ajax_request() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'You do not have permission to perform this action.', "bonaire" ) ),
				403
			);
		}
	}

	/**
	 * Returns a normalized request nonce.
	 */
	private function request_nonce() {
		return isset( $_POST['nonce'] )
			? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
			: '';
	}
	
	/**
	 * Instanciates \Bonaire_Post_Views and marks the message as read via
	 * a post view count stored in the post's post meta data
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function bonaire_mark_as_read() {
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( 0 === $post_id || false === wp_verify_nonce( $this->request_nonce(), 'bonaire_mark_as_read_nonce_' . $post_id ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Post_Views->update_post_view( $post_id );
		if ( $result ) {
			
			$response = array(
				'success' => true,
				'message' => __( 'Message marked as read.', "bonaire" )
			);
			wp_send_json_success( $response );
		} else {
			
			$response = array(
				'success' => false,
				'message' => __( 'Failed to mark message as read.', "bonaire" ) . ' ' . __( 'Please try again later.', "bonaire" )
			);
			wp_send_json_error( $response );
		}
	}
	
	/**
	 * Marks the selected item as 'spam'.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function bonaire_mark_as_spam() {
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( 0 === $post_id || false === wp_verify_nonce( $this->request_nonce(), 'bonaire_mark_as_spam_nonce_' . $post_id ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $this->Bonaire_Options->get_stored_options( 0 ) );
		$result          = $Bonaire_Adapter->mark_as_spam( $post_id );
		if ( ! is_wp_error( $result ) ) {
			
			// Mark as read (internally)
			$this->Bonaire_Post_Views->update_post_view( $post_id );
			
			$response = array(
				'success' => true,
				'message' => __( 'Message marked as spam.', "bonaire" )
			);
			wp_send_json_success( $response );
		} else {
			/**
			 * @var \WP_Error $result
			 */
			$message = $result->get_error_message();
			
			$response = array(
				'success' => false,
				'message' => $message
			);
			wp_send_json_error( $response );
		}
	}
	
	/**
	 * Moves the selected item to 'trash'.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function bonaire_move_to_trash() {
		
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( 0 === $post_id || false === wp_verify_nonce( $this->request_nonce(), 'bonaire_move_to_trash_nonce_' . $post_id ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$Bonaire_Adapter = new AdminIncludes\Bonaire_Adapter( $this->domain, $this->Bonaire_Options->get_stored_options( 0 ) );
		$result          = $Bonaire_Adapter->move_to_trash( $post_id );
		if ( ! is_wp_error( $result ) ) {
			
			// Mark as read (in backend, not for mail server)
			$this->Bonaire_Post_Views->update_post_view( $post_id );
			
			$response = array(
				'success' => true,
				'message' => __( 'Message moved to trash.', "bonaire" )
			);
			wp_send_json_success( $response );
		} else {
			/**
			 * @var \WP_Error $result
			 */
			$message  = $result->get_error_message();
			$response = array(
				'success' => false,
				'message' => $message
			);
			wp_send_json_error( $response );
		}
	}
	
	/**
	 * Saves the options.
	 *
	 * @return void
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_save_options() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_save_options_nonce' ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		// retrieve the options
		$data = array();
		foreach ( (array) $this->Bonaire_Options->get_options_meta() as $key => $list ) {
			if ( isset( $_POST[ $key ] ) && ! is_array( $_POST[ $key ] ) ) {
				$postedData = wp_unslash( $_POST[ $key ] );
				if ( 'password' === $key ) {
					// Passwords may legitimately contain characters stripped by text sanitizers.
					$postedData = (string) $postedData;
				} elseif ( in_array( $key, array( 'from', 'username' ), true ) ) {
					if ( 'username' === $key && false === strpos( $postedData, '@' ) ) {
						$postedData = sanitize_text_field( $postedData );
					} else {
						$postedData = sanitize_email( $postedData );
					}
				} else {
					$postedData = sanitize_text_field( $postedData );
				}
				
				$data[ $key ] = $postedData;
			}
		}
		
		// Save options
		$result = $this->Bonaire_Options->bonaire_save_options( $data );
		if ( is_wp_error( $result ) ) {
			
			$error_code = $result->get_error_code();
			$message    = $result->get_error_message();
			
			if ( - 1 === $error_code ) {
				
				$response = array(
					'success' => true,
					'message' => $message
				);
				wp_send_json_success( $response );
			}
			
			$response = array(
				'success' => true,
				'message' => $result->get_error_message()
			);
			wp_send_json_success( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => __( 'Settings saved.', "bonaire" ),
				'cf7_status' => isset( $result['cf7_status'] ) ? $result['cf7_status'] : '',
				'smtp_status' => isset( $result['smtp_status'] ) ? $result['smtp_status'] : '',
				'imap_status' => isset( $result['imap_status'] ) ? $result['imap_status'] : ''
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Resets the stored options to the default values.
	 *
	 * @return void
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_reset_options() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_reset_options_nonce' ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Options->reset_options();
		if ( is_wp_error( $result ) ) {
			$error_code = $result->get_error_code();
			$message    = $result->get_error_message();
			
			$response = array(
				'success' => false,
				'message' => $message . ' ' . __( 'Please try again later.', "bonaire" ) . '(' . $error_code . ')'
			);
			wp_send_json_error( $response );
		} else {
			$response = array(
				'success' => true,
				'message' => __( 'Settings restored to default.', "bonaire" ),
				'smtp_status' => $result['smtp_status'],
				'imap_status' => $result['imap_status']
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Tests if the contact form configuration meets our requirements.
	 *
	 * @throws \Exception
	 * @since 1.0.0
	 */
	public function bonaire_test_contact_form() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_test_contact_form_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Account_Settings_Evaluator->bonaire_test_contact_form();
		if ( is_wp_error( $result ) ) {
			
			$response = array(
				'success' => false,
				'message' => $result->get_error_message(),
				'status' => 'orange'
			);
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => $result['message'],
				'status' => $result['status']
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Tests the SMTP settings based on the stored user options.
	 *
	 * @return void
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_test_smtp_settings() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_test_smtp_settings_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Account_Settings_Evaluator->bonaire_test_smtp_settings();
		if ( is_wp_error( $result ) ) {
			
			$response = array(
				'success' => false,
				'message' => $result->get_error_message(),
				'status' => 'orange'
			);
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => $result['message'],
				'status' => 'green'
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Tests the IMAP settings based on the stored user options.
	 *
	 * @return void
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_test_imap_settings() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_test_imap_settings_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Account_Settings_Evaluator->bonaire_test_imap_settings();
		if ( is_wp_error( $result ) ) {
			
			$response = array(
				'success' => false,
				'message' => $result->get_error_message(),
				'status' => 'orange'
			);
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => $result['message'],
				'status' => 'green'
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Retrieves the email account settings status for smtp and imap functionality.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function bonaire_get_settings_status() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_save_options_nonce' ) ) {
			
			$response = array(
				'success' => false
			);
			wp_send_json_error( $response );
		}
		
		$result   = $this->Bonaire_Account_Settings_Status->get_settings_status();
		$response = array(
			'success' => true,
			'cf7_status' => isset( $result['cf7'] ) ? $result['cf7'] : 'orange',
			'smtp_status' => isset( $result['smtp'] ) ? $result['smtp'] : 'inactive',
			'imap_status' => isset( $result['imap'] ) ? $result['imap'] : 'inactive'
		);
		wp_send_json_success( $response );
	}
	
	/**
	 * Sends a test message
	 *
	 * @return void
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_send_testmail() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_send_testmail_nonce' ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$recipient_email_address = isset( $_POST['value'] ) ? sanitize_email( wp_unslash( $_POST['value'] ) ) : '';
		
		if ( ! is_email( $recipient_email_address ) ) {
			$response = array(
				'success' => false,
				'message' => __( 'Please review the email address you entered.', "bonaire" ),
				'error' => 'email_not_valid'
			);
			wp_send_json_error( $response );
		}
		
		/**
		 * @var object AdminIncludes\Bonaire_Mail
		 */
		$result = $this->Bonaire_Account_Settings_Evaluator->send_testmail( $recipient_email_address );
		if ( is_wp_error( $result ) ) {
			
			$debug              = isset( $GLOBALS['debug'] ) && is_array( $GLOBALS['debug'] ) ? $GLOBALS['debug'] : array();
			$last_debug_message = empty( $debug ) ? '' : (string) end( $debug );
			
			$code = $result->get_error_code();
			$msg  = $result->get_error_message();
			if ( '' === $code ) {
				$msg = $last_debug_message;
			}
			
			$response = array(
				'success' => false,
				'message' => $msg . ' (' . __( 'Error code', "bonaire" ) . ': ' . $code . ')'
			);
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => __( 'Test message sent successfully!', "bonaire" )
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Checks the email address, sanitizes the user input, instantiates \Bonaire_Mail and submits the data to said class.
	 *
	 * @return void
	 * @throws \Exception
	 * @since 0.9.6
	 */
	public function bonaire_submit_reply() {
		
		if ( false === wp_verify_nonce( $this->request_nonce(), 'bonaire_reply_form_nonce' ) ) {
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$data           = (object) array();
		$data->fromname = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$data->to       = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$data->subject  = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$data->message  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		if ( ! is_email( $data->to ) || '' === $data->subject || '' === $data->message ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => __( 'Please provide a valid recipient, subject, and message.', "bonaire" ),
				),
				400
			);
		}
		
		$result = $this->Bonaire_Mail->send_mail( $data );
		if ( is_wp_error( $result ) ) {
			
			$code = $result->get_error_code();
			$msg  = $result->get_error_message();
			
			$response = array(
				'success' => false,
				'message' => $msg . ' (' . __( 'Error code', "bonaire" ) . ': ' . $code . ')'
			);
			
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => __( 'Message sent!', "bonaire" )
			);
			
			wp_send_json_success( $response );
		}
	}
	
}
