<?php

namespace Bonaire\Admin\Includes;

use Flamingo_Inbound_Message, WPCF7_ContactForm;
use Bonaire\Admin\Includes as AdminIncludes;
use WP_Error;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'WPCF7_ContactForm' ) && file_exists( BONAIRE_PLUGINS_ROOT_DIR . 'contact-form-7/includes/contact-form.php' ) ) {
	include BONAIRE_PLUGINS_ROOT_DIR . 'contact-form-7/includes/contact-form.php';
}
if ( ! class_exists( 'Flamingo_Inbound_Message' ) && file_exists( BONAIRE_PLUGINS_ROOT_DIR . 'flamingo/includes/class-inbound-message.php' ) ) {
	include BONAIRE_PLUGINS_ROOT_DIR . 'flamingo/includes/class-inbound-message.php';
}

/**
 * Gets in touch with Contact Form 7 and Flamingo.
 *
 * @since            0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Adapter extends Flamingo_Inbound_Message {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since   0.9.6
	 * @access   protected
	 */
	protected $domain;
	
	/**
	 * Holds the stored options.
	 *
	 * @var      object $stored_options
	 * @since   0.9.6
	 * @access   private
	 */
	private $stored_options;
	
	/**
	 * Holds the stored messages.
	 *
	 * @var      array $posts
	 * @since   0.9.6
	 * @access   private
	 */
	private $posts;
	
	/**
	 * Sets an array containing Flamingo post objects.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	private function set_posts() {
		
		$args = array(
			'posts_per_page' => -1,
			'offset' => 0,
			'orderby' => 'ID',
			'order' => 'ASC',
			'meta_key' => '',
			'meta_value' => '',
			'post_status' => 'any',
		);
		
		$this->posts = self::find( $args );
	}
	
	/**
	 * Bonaire_Adapter constructor.
	 *
	 * @param string $domain
	 * @param object $stored_options
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function __construct( $domain, $stored_options ) {
		
		parent::__construct();
		$this->set_posts();
		
		$this->domain = $domain;
		$this->stored_options = $stored_options;
	}
	
	/**
	 * Initiates the probable postprocessing of newly received messages.
	 *
	 * @since 0.9.6
	 * @return void
	 */
	public function update_post() {
		
		$this->postprocess_messages();
	}
	
	/**
	 * Add the recipient email as post meta data to filter messages
	 * by recipient (aka registered account settings email address).
	 *
	 * @since 0.9.6
	 * @return void
	 */
	private function postprocess_messages() {

		$bonaire_wpcf7_has_mail = get_transient( 'bonaire_wpcf7_mail_meta' );
		if(false === is_array($bonaire_wpcf7_has_mail) || empty($bonaire_wpcf7_has_mail)){
			
			return;
		}
		
		// If there are new messages to extend the data
		foreach ( $bonaire_wpcf7_has_mail as $index => $message ) {
			
			foreach ( $this->posts as $i => $flamingo_post ) {
				
				$fields = $flamingo_post->fields;
				if ( isset( $fields['posted_data_uniqid'] ) && $message['posted_data_uniqid'] === $fields['posted_data_uniqid'] ) {
					// Extend the meta data
					$meta                       = $flamingo_post->meta;
					$meta['channel']            = $message['channel'];
					$meta['form_id']            = $message['form_id'];
					$meta['posted_data_uniqid'] = $message['posted_data_uniqid'];
					$meta['recipient']          = $message['recipient'];
					// remove the uniqid from the fields data,
					// since it has done it's job
					unset( $fields['posted_data_uniqid'] );
					// update post meta
					$post = get_post( $flamingo_post->id );
					update_post_meta( $post->ID, '_fields', $fields );
					update_post_meta( $post->ID, '_meta', $meta );
				}
			}
		}
		
		delete_transient( 'bonaire_wpcf7_has_mail' );
		delete_transient( 'bonaire_wpcf7_mail_meta' );
	}
	
	/**
	 * Returns a Flamingo post object or false.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return Flamingo_Inbound_Message|bool
	 */
	private function post( $post_id ) {
		
		foreach ( $this->posts as $i => $post ) {
			if ( $post_id === $post->id ) {
				return $post;
			}
		}
		
		return false;
	}
	
	/**
	 * Returns a string containing a message's attribute value.
	 *
	 * @param int $post_id
	 * @param string $attribute
	 *
	 * @since 0.9.6
	 * @return string|bool
	 */
	private function post_attribute( $post_id, $attribute ) {
		
		foreach ( $this->posts as $i => $post ) {
			
			if ( $post_id === $post->id ) {
				return $post[ $attribute ];
			}
		}
		
		return false;
	}
	
	/**
	 * Returns a string containing a message's field value.
	 *
	 * @param int $post_id
	 * @param string $field_name
	 *
	 * @since 0.9.6
	 * @return string|bool
	 */
	private function field( $post_id, $field_name ) {
		
		foreach ( $this->posts as $i => $post ) {
			foreach ( $post->meta as $field => $value ) {
				if ( $post_id === $post->id && $field_name === $field ) {
					return $value;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Returns a Flamingo post object or false.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return Flamingo_Inbound_Message|bool
	 */
	public function get_post( $post_id ) {
		
		return $this->post( $post_id );
	}
	
	/**
	 * Returns a string containing a message attribute's value.
	 *
	 * @param int $post_id
	 * @param string $attribute
	 *
	 * @since 0.9.6
	 * @return string|bool
	 */
	public function get_post_attribute( $post_id, $attribute ) {
		
		return $this->post_attribute( $post_id, $attribute );
	}
	
	/**
	 * Returns a string containing a message's field value.
	 *
	 * @param int $post_id
	 * @param string $field_name
	 *
	 * @since 0.9.6
	 * @return string
	 */
	public function get_field( $post_id, $field_name ) {
		
		return $this->field( $post_id, $field_name );
	}
	
	/**
	 * Returns true if the recipient email address and the stores account email address match,
	 * otherwise false.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return bool
	 */
	private function check_same_email_address( $post_id ) {
		
		$account_from_address = $this->stored_options->from;
		
		return $account_from_address === $this->recipient_email_address( $post_id );
	}
	
	/**
	 * Returns a string containing the recipient email address or false.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return string|bool
	 */
	private function recipient_email_address( $post_id ) {
		
		$channel = $this->get_inbound_channel_from_current_message( $post_id );
		
		$args = array(
			'name' => $channel,
			'post_status' => 'any',
			'posts_per_page' => 1,
			'offset' => 0,
			'orderby' => 'name',
			'order' => 'ASC',
		);
		
		$contact_form_by_channel = WPCF7_ContactForm::find( $args );
		/**
		 * @var WPCF7_ContactForm $contact_form
		 */
		$contact_form = $contact_form_by_channel[0];
		$properties = $contact_form->get_properties();
		$contact_form = $contact_form->name() === $channel ? $contact_form : false;
		if ( is_a( $contact_form, 'WPCF7_ContactForm') ) {
			
			return isset( $properties['mail']['recipient'] ) && '' !== $properties['mail']['recipient'] ? $properties['mail']['recipient'] : false;
		}
		
		return false;
	}
	
	/**
	 * Returns a string containing the 'channel' related to the requested Flamingo_Inbound message.
	 * This is used to identify the contact form from which the requested message was originally received from.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return string|bool
	 */
	private function get_inbound_channel_from_current_message( $post_id ) {
		
		foreach ( $this->posts as $i => $post ) {
			if ( (int) $post->id === (int) $post_id ) {
				
				return isset( $post->channel ) ? $post->channel : false;
			}
		}
		
		return false;
	}
	
	/**
	 * Compares the email address of the currently stored account settings with
	 * the email address the message was sent to.
	 * Replies can only be sent if these addresses are the same.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return bool
	 */
	public function is_same_email_address( $post_id ) {
		
		return $this->check_same_email_address( $post_id );
	}
	
	/**
	 * Returns a string containing the email address of the recipient or false.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return string|bool
	 */
	public function get_recipient_email_address( $post_id ) {
		
		return $this->recipient_email_address( $post_id );
	}
	
	/**
	 * Updates the post status of the message related to the given post id to 'spam'.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return bool|\WP_Error
	 */
	public function mark_as_spam( $post_id ) {
		
		$args = array(
			'fields' => array(),
			'meta' => array(),
			'akismet' => array(),
			'spam' => true,
			'consent' => array(),
			'id' => (int) $post_id,
		);
		
		try {
			$result = Flamingo_Inbound_Message::add( $args );
		} catch( Exception $e ) {
			
			return new WP_Error( 1, __( 'Internal Error: Unable to mark message as spam.', $this->domain ) . ' ' . __( 'Please try again later.', $this->domain ) . '(1)' );
		}
		
		if ( false === $result ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to mark message as spam.', $this->domain ) . ' ' . __( 'Please try again later.', $this->domain ) . '(2)' );
		}
		
		return true;
	}
	
	/**
	 * Updates the post status of the message related to the given post id to 'trash'.
	 *
	 * @param int $post_id
	 *
	 * @since 0.9.6
	 * @return bool|\WP_Error
	 */
	public function move_to_trash( $post_id ) {
		
		$args = array(
			'fields' => array(),
			'meta' => array(),
			'akismet' => array(),
			'spam' => true,
			'consent' => array(),
			'id' => (int) $post_id,
			'post_status' => 'trash'
		);
		
		try {
			$result = Flamingo_Inbound_Message::add( $args );
		} catch( Exception $e ) {
			
			return new WP_Error( 1, __( 'Internal Error: Unable to mark message as spam.', $this->domain ) . ' ' . __( 'Please try again later.', $this->domain ) . '(1)' );
		}
		
		if ( false === $result ) {
			
			return new WP_Error( 2, __( 'Internal Error: Unable to mark message as spam.', $this->domain ) . ' ' . __( 'Please try again later.', $this->domain ) . '(2)' );
		}
		
		return true;
	}
	
}