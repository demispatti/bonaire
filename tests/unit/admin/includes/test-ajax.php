<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Bonaire_Ajax {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	protected $domain;
	
	/**
	 * @var Bonaire_Options $Bonaire_Options
	 */
	protected $Bonaire_Options;
	
	/**
	 * @var Bonaire_Post_Views $Bonaire_Post_Views
	 */
	protected $Bonaire_Post_Views;
	
	/**
	 * @var Bonaire_Mail $Bonaire_Mail
	 */
	protected $Bonaire_Mail;
	
	/**
	 * @var stdClass $stored_options
	 */
	protected $stored_options;
	
	/**
	 * Holds the error text for failed nonce checks
	 *
	 * @var string $nonce_error_text
	 */
	protected $nonce_error_text;
	
	/**
	 * @var Bonaire_Settings_Evaluator $Bonaire_Settings_Evaluator
	 */
	private $Bonaire_Settings_Evaluator;
	
	/**
	 * Bonaire_Ajax constructor.
	 *
	 * @param string $domain
	 * @param Bonaire_Options $Bonaire_Options
	 * @param Bonaire_Post_Views $Bonaire_Post_Views
	 * @param Bonaire_Mail $Bonaire_Mail
	 * @param Bonaire_Settings_Evaluator $Bonaire_Settings_Evaluator
	 */
	public function __construct( $domain, $Bonaire_Options, $Bonaire_Post_Views, $Bonaire_Mail, $Bonaire_Settings_Evaluator ) {
		
		$this->domain = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$this->Bonaire_Post_Views = $Bonaire_Post_Views;
		$this->Bonaire_Mail = $Bonaire_Mail;
		$this->stored_options = $Bonaire_Options->get_stored_options( '0' );
		$this->Bonaire_Settings_Evaluator = $Bonaire_Settings_Evaluator;
		$this->nonce_error_text = __( 'That won\'t do.', $this->domain );
	}
	
	public function add_hooks() {
		
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
		add_action( 'wp_ajax_bonaire_test_smtp_settings', array( $this, 'bonaire_test_smtp_settings' ) );
		add_action( 'wp_ajax_bonaire_test_imap_settings', array( $this, 'bonaire_test_imap_settings' ) );
	}
	
	/**
	 * Instanciates \Bonaire_Post_Views and marks the message as read via
	 * a post view count stored in the post's post meta data
	 */
	public function bonaire_mark_as_read() {
		
		$post_id = $_REQUEST['post_id'];
		$nonce = $_REQUEST['nonce'];
		
		if ( false === wp_verify_nonce( $nonce, 'bonaire_mark_as_read_nonce_' . $post_id ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$marked_as_read = $this->Bonaire_Post_Views->update_post_view( $post_id );
		if ( $marked_as_read ) {
			
			$response = array(
				'success' => true,
				'message' => __( 'Message marked as read.', $this->domain )
			);
			wp_send_json_success( $response );
		} else {
			
			$response = array(
				'success' => false,
				'message' => __( 'Failed to mark message as read.', $this->domain ) . ' ' . __( 'Please try again later.', $this->domain )
			);
			wp_send_json_error( $response );
		}
	}
	
	public function bonaire_mark_as_spam() {
		
		$post_id = $_REQUEST['post_id'];
		$nonce = $_REQUEST['nonce'];
		
		if ( false === wp_verify_nonce( $nonce, 'bonaire_mark_as_spam_nonce_' . $post_id ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$Bonaire_Adapter = new Bonaire_Adapter( $this->Bonaire_Options->get_stored_options( 0 ) );
		$result = $Bonaire_Adapter->mark_as_spam( $post_id );
		if ( ! is_wp_error( $result ) ) {
			
			$this->bonaire_mark_as_read();
			
			$response = array(
				'success' => true,
				'message' => __( 'Message marked as spam.', $this->domain )
			);
			wp_send_json_success( $response );
		} else {
			/**
			 * @var WP_Error $result
			 */
			$message = $result->get_error_message();
			
			$response = array(
				'success' => false,
				'message' => __( 'Failed to mark message as spam.', $this->domain ) . ' (' . $message . '). ' . __( 'Please try again later.', $this->domain )
			);
			wp_send_json_error( $response );
		}
	}
	
	public function bonaire_move_to_trash() {
		
		$post_id = $_REQUEST['post_id'];
		$nonce = $_REQUEST['nonce'];
		
		if ( false === wp_verify_nonce( $nonce, 'bonaire_move_to_trash_nonce_' . $post_id ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$Bonaire_Adapter = new Bonaire_Adapter( $this->Bonaire_Options->get_stored_options( 0 ) );
		$result = $Bonaire_Adapter->move_to_trash( $post_id );
		if ( ! is_wp_error( $result ) ) {
			
			$this->bonaire_mark_as_read();
			
			$response = array(
				'success' => true,
				'message' => __( 'Message moved to trash.', $this->domain )
			);
			wp_send_json_success( $response );
		} else {
			/**
			 * @var WP_Error $result
			 */
			$message = $result->get_error_message();
			$response = array(
				'success' => false,
				'message' => __( 'Failed to move message to trash.', $this->domain ) . ' (' . $message . '). ' . __( 'Please try again later.', $this->domain )
			);
			wp_send_json_error( $response );
		}
	}
	
	/**
	 * Instanciates \Bonaire_Options and stores the options
	 */
	public function bonaire_save_options() {
		
		$nonce = $_REQUEST['nonce'];
		
		if ( false === wp_verify_nonce( $nonce, 'bonaire_save_options_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			
			wp_send_json_error( $response );
		}
		
		$data = array();
		// retrieve the options
		foreach ( (array) $this->Bonaire_Options->get_options_meta() as $key => $list ) {
			if ( isset( $_POST[ $key ] ) ) {
				$data[ $key ] = $_POST[ $key ];
			}
		}
		
		// Save options
		$result = $this->Bonaire_Options->bonaire_save_options( $data );
		if ( is_wp_error( $result ) ) {
			
			$code = $result->get_error_code();
			$msg = $result->get_error_message();
			
			if ( - 1 === $code ) {
				
				$response = array(
					'success' => true,
					'message' => $msg
				);
				
				wp_send_json_success( $response );
			}
			
			$response = array(
				'success' => false,
				'message' => $msg . ' ' . __( 'Please try again later.', $this->domain ) . ' (' . $code . ')'
			);
			
			wp_send_json_error( $response );
		} else {
			/**
			 * @var array $result
			 */
			$response = array(
				'success' => true,
				'message' => __( 'Settings saved.', $this->domain ),
				'smtp_state' => $result['smtp_state'],
				'imap_state' => $result['imap_state']
			);
			
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Instanciates \Bonaire_Options and resets the stored options
	 */
	public function bonaire_reset_options() {
		
		$nonce = $_REQUEST['nonce'];
		
		if ( false === wp_verify_nonce( $nonce, 'bonaire_reset_options_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Options->reset_options();
		if ( is_wp_error( $result ) ) {
			
			$code = $result->get_error_code();
			$msg = $result->get_error_message();
			
			$response = array(
				'success' => false,
				'message' => $msg . ' ' . __( 'Please try again later.', $this->domain ) . '(' . $code . ')'
			);
			wp_send_json_error( $response );
		} else {
			/**
			 * @var array $result
			 */
			$response = array(
				'success' => true,
				'message' => __( 'Settings restored to default.', $this->domain ),
				'smtp_state' => $result['smtp_state'],
				'imap_state' => $result['imap_state']
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Tests the stored connection details.
	 */
	public function bonaire_test_smtp_settings() {
		
		$nonce = $_REQUEST['nonce'];
		
		if ( true === wp_verify_nonce( $nonce, 'bonaire_test_smtp_settings_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Settings_Evaluator->bonaire_test_smtp_settings();
		if ( is_wp_error( $result ) ) {
			
			$response = array(
				'success' => false,
				'message' => $result->get_error_message(),
				'state' => 'red'
			);
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => $result['message'],
				'state' => $result['state']
			);
			wp_send_json_success( $response );
		}
	}
	
	public function bonaire_test_imap_settings() {
		
		$nonce = $_REQUEST['nonce'];
		
		if ( true === wp_verify_nonce( $nonce, 'bonaire_test_imap_settings_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			wp_send_json_error( $response );
		}
		
		$result = $this->Bonaire_Settings_Evaluator->bonaire_test_imap_settings();
		if ( is_wp_error( $result ) ) {
			
			$response = array(
				'success' => false,
				'message' => $result->get_error_message(),
				'state' => 'red'
			);
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => $result['message'],
				'state' => $result['state']
			);
			wp_send_json_success( $response );
		}
	}
	
	/**
	 * Instanciates \Bonaire_Mail and sends a test message
	 */
	public function bonaire_send_testmail() {
		
		$nonce = $_REQUEST['nonce'];
		if ( false === wp_verify_nonce( $nonce, 'bonaire_send_testmail_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			
			wp_send_json_error( $response );
		}
		
		/**
		 * @var object \Bonaire_Mail
		 */
		$result = $this->Bonaire_Mail->send_testmail();
		if ( is_wp_error( $result ) ) {
			
			$debug = $GLOBALS['debug'];
			$last_debug_message = array_values( array_slice( $debug, - 1 ) )[0];
			
			$code = $result->get_error_code();
			$msg = $result->get_error_message();
			if ( '' === $code ) {
				$msg = $last_debug_message;
			}
			
			$response = array(
				'success' => false,
				'message' => $msg
			);
			
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => __( 'Test message sent successfully!', $this->domain )
			);
			
			wp_send_json_success( $response );
		}
	}
	
	/**,
	 * checks the email address, sanitizes the user input, instantiates \Bonaire_Mail and submits the data to said class.
	 */
	public function bonaire_submit_reply() {
		
		$nonce = $_REQUEST['nonce'];
		
		if ( false === wp_verify_nonce( $nonce, 'bonaire_reply_form_nonce' ) ) {
			
			$response = array(
				'success' => false,
				'message' => $this->nonce_error_text
			);
			
			wp_send_json_error( $response );
		}
		
		$data = new stdClass();
		$data->fromname = strip_tags( stripslashes( $_REQUEST['name'] ) );
		$data->to = filter_var( strip_tags( stripslashes( $_REQUEST['email'] ) ), FILTER_VALIDATE_EMAIL );
		$data->subject = strip_tags( stripslashes( $_REQUEST['subject'] ) );
		$data->message = strip_tags( stripslashes( $_REQUEST['message'] ) );
		
		$result = $this->Bonaire_Mail->send_mail( $data );
		if ( is_wp_error( $result ) ) {
			
			$code = $result->get_error_code();
			$msg = $result->get_error_message();
			
			$response = array(
				'success' => false,
				'message' => $msg . __( 'Please check/update the necessary connection data on this plugin\'s settings page.', $this->domain ) . '(' . $code . ')'
			);
			
			wp_send_json_error( $response );
		} else {
			
			$response = array(
				'success' => true,
				'message' => __( 'Message sent!', $this->domain )
			);
			
			wp_send_json_success( $response );
		}
	}
	
}
