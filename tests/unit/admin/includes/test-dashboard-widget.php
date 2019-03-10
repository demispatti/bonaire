<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Flamingo_Inbound_Message' ) ) {
	include BONAIRE_ROOT_DIR . '../../flamingo/includes/class-inbound-message.php';
}

/**
 * Class Bonaire_Dashboard_Widget
 */
class Bonaire_Dashboard_Widget {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	/**
	 * Holds the string containing the message that's displayed
	 * when there are no messages to show.
	 *
	 * @var string $no_messages_message
	 */
	private $no_messages_message;
	
	/**
	 * @var Bonaire_Item_Display $Bonaire_Item_Display
	 */
	private $Bonaire_Item_Display;
	
	/**
	 * Holds the email address that's related to the account settings.
	 *
	 * @var string $recipient
	 */
	private $recipient;
	
	/**
	 * Sets the string for when there are no messages to display.
	 */
	private function set_no_messages_message() {
		
		$this->no_messages_message = __( 'No new messages.', $this->domain );
	}
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $name The name of this plugin.
	 * @param      string $domain The domain of this plugin.
	 * @param      string $version The version of this plugin.
	 * @param      Bonaire_Options $Bonaire_Options
	 */
	public function __construct( $domain, $Bonaire_Item_Display, $Bonaire_Options ) {
		
		$this->domain = $domain;
		$this->Bonaire_Item_Display = $Bonaire_Item_Display;
		$account_settings = $Bonaire_Options->get_stored_options( 0 );
		$this->recipient = $account_settings->from;
		$this->set_no_messages_message();
	}
	
	/**
	 * Hooks the action that registers the widget with WordPress.
	 */
	public function add_hooks() {
		
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ), 10 );
	}
	
	/**
	 * Registers the widget with WordPress.
	 */
	public function add_dashboard_widget() {
		
		wp_add_dashboard_widget(
			'bonaire_dashboard_widget',
			__( 'Messages', $this->domain ),
			array( $this, 'dashboard_widget_display' )
		);
	}
	
	/**
	 * Displays either a list of messages or
	 * the message for no found items.
	 */
	public function dashboard_widget_display() {
		
		$posts = null;
		$stored_posts = $this->retrieve_flamingo_inbound_messages();
		foreach ( $stored_posts as $i => $post ) {
			$post_meta = get_post_meta( $post->id );
			
			if ( isset( $post_meta['post_views_count'][0] ) && (int) $post_meta['post_views_count'][0] < 1 ) {
				if ( $this->recipient === $post_meta['recipient_email'][0] ) {
					$posts[] = $post;
				}
			}
		}
		
		if ( empty( $posts ) ) {
			
			echo '<p class="message no-message">' . $this->no_messages_message . '</p>';
		} else {
			
			echo $this->display_items( $posts );
		}
	}
	
	/**
	 * Retrieves an array containing the stored inbound messages from Flamingo
	 *
	 * @uses Flamingo_Inbound_Message::find()
	 * @return mixed
	 */
	private function retrieve_flamingo_inbound_messages() {
		
		return Flamingo_Inbound_Message::find();
	}
	
	/**
	 * Displays a list recent messages
	 *
	 * @param $posts
	 *
	 * @uses get_post_meta( $post->id )
	 * @uses get_item( $post )
	 * @return string $string
	 */
	private function display_items( $posts ) {
		
		if ( false !== $posts && ! empty( (array) $posts ) ) {
			
			$string = '<ul>';
			foreach ( $posts as $i => $post ) {
				$post_meta = get_post_meta( $post->id );
				$post_views = isset( $post_meta['post_views_count'] ) ? $post_meta['post_views_count'][0] : '';
				if ( '' === $post_views ) {
					$string .= $this->Bonaire_Item_Display->get_item( $post );
				}
			}
			$string .= '</ul>';
			$string .= $this->get_subsub();
			
			return $string;
		}
	}
	
	private function get_subsub() {
		
		$count_posts = wp_count_posts( 'flamingo_inbound' );
		$all_count = isset( $count_posts->publish ) ? (int) $count_posts->publish : 0;
		$spam_count = isset( $count_posts->spam ) ? (int) $count_posts->spam : 0;
		$trash_count = isset( $count_posts->trash ) ? (int) $count_posts->trash : 0;
		$all = 0 !== $all_count ? '<a href="/wp-admin/admin.php?page=flamingo_inbound">' . __( 'All', $this->domain ) . '</a>' : '<span class="empty-link">' . __( 'All', $this->domain ) . '</span>';
		$spam = 0 !== $spam_count ? '<a href="/wp-admin/admin.php?page=flamingo_inbound&post_status=spam">' . __( 'Spam', $this->domain ) . '</a>' : '<span class="empty-link">' . __( 'Spam', $this->domain ) . '</span>';
		$trash = 0 !== $trash_count ? '<a href="/wp-admin/admin.php?page=flamingo_inbound&post_status=trash">' . __( 'Trash', $this->domain ) . '</a>' : '<span class="empty-link">' . __( 'Trash', $this->domain ) . '</span>';
		
		return '
			<ul class="subsub">
				<li class="all">' . $all . '<span class="count"> (<span class="all-count">' . $all_count . '</span>)</span> |</li>
				<li class="spam">' . $spam . '<span class="count"> (<span class="spam-count">' . $spam_count . '</span>)</span> |</li>
				<li class="trash">' . $trash . '<span class="count"> (<span class="trash-count">' . $trash_count . '</span>)</span></li>
			</ul>';
		/*return '
			<ul class="subsub">
				<li class="all"><a href="/wp-admin/admin.php?page=flamingo_inbound">' . __( 'All', $this->domain ) . '</a><span class="count"> (<span class="all-count">' . $all_count . '</span>)</span> |</li>
				<li class="spam"><a href="/wp-admin/admin.php?page=flamingo_inbound&post_status=spam">' . __( 'Spam', $this->domain ) . '</a><span class="count"> (<span class="spam-count">' . $spam_count . '</span>)</span> |</li>
				<li class="trash"><a href="/wp-admin/admin.php?page=flamingo_inbound&post_status=trash">' . __('Trash', $this->domain) . '</a><span class="count"> (<span class="trash-count">' . $trash_count . '</span>)</span></li>
			</ul>';*/
	}
	
}
