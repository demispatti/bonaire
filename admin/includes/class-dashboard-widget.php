<?php
namespace Bonaire\Admin\Includes;

use Bonaire\Admin\Includes as AdminIncludes;
use Bonaire\Admin\Partials as AdminPartials;
use Flamingo_Inbound_Message;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include dependencies.
 */
if ( ! class_exists( 'AdminPartials\Bonaire_Item_Display' ) ) {
	require_once BONAIRE_ROOT_DIR . 'admin/partials/class-item-display.php';
}
if ( ! class_exists( 'Flamingo_Inbound_Message' ) && file_exists( BONAIRE_PLUGINS_ROOT_DIR . 'flamingo/includes/class-inbound-message.php' ) ) {
	include BONAIRE_PLUGINS_ROOT_DIR . 'flamingo/includes/class-inbound-message.php';
}

/**
 * The class responsible for creating and displaying the dashboard widget.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Dashboard_Widget {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Holds the string containing the message that's displayed
	 * when there are no messages to show.
	 *
	 * @var      string $no_messages_message
	 * @since    0.9.6
	 * @access   private
	 */
	private $no_messages_message;
	
	/**
	 * Holds the string containing the message that's displayed
	 * when there is no email account set.
	 *
	 * @var      string $configure_account_message
	 * @since    0.9.6
	 * @access   private
	 */
	private $configure_account_message;
	
	/**
	 * Holds the instance that's responsible for displaying the message excerpts on the dashboard.
	 *
	 * @var AdminPartials\Bonaire_Item_Display $Bonaire_Item_Display
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Item_Display;
	
	/**
	 * Holds the instance that's responsible for handling the user options.
	 *
	 * @var AdminIncludes\Bonaire_Options $Bonaire_Options
	 * @since    0.9.6
	 * @access   private
	 */
	private $Bonaire_Options;
	
	/**
	 * Holds the email address that's related to the account settings.
	 *
	 * @var string $recipient
	 */
	private $recipient;
	
	/**
	 * Sets the string for when there are no messages to display.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function set_no_messages_message() {
		
		$this->no_messages_message = '<div class="no-activity"><p class="smiley" aria-hidden="true"></p><p class="message-text">' . __( "No new messages.", $this->domain ) . '</p></div>';
	}
	
	/**
	 * Sets the string for when there is no propperly configured email account set.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function set_configure_account_message() {
		
		$this->configure_account_message = '<div class="no-activity"><span class="email"><a title="' . __( 'Go to settings page', $this->domain ) . '" href="/wp-admin/options-general.php?page=bonaire.php" aria-hidden="true"></a></span><a class="message-text" href="/wp-admin/options-general.php?page=bonaire.php">' . __( "Configure email account.", $this->domain ) . '</a></div>';
	}
	
	/**
	 * Sets the instance responsible for displaying the messages on the dashboard.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	private function set_item_display_instance() {
		
		$this->Bonaire_Item_Display = new AdminPartials\Bonaire_Item_Display( $this->domain );
	}
	
	/**
	 * Bonaire_Dashboard_Widget constructor.
	 *
	 * @param string $domain
	 * @param AdminIncludes\Bonaire_Options $Bonaire_Options
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function __construct( $domain, $Bonaire_Options ) {
		
		$this->domain          = $domain;
		$this->Bonaire_Options = $Bonaire_Options;
		$account_settings      = $Bonaire_Options->get_stored_options( 0 );
		$this->recipient       = $account_settings->from;
		$this->set_no_messages_message();
		$this->set_configure_account_message();
		$this->set_item_display_instance();
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_script' ), 20 );
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ), 10 );
	}
	
	/**
	 * Registers the widget with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_dashboard_widget() {
		
		wp_add_dashboard_widget(
			'bonaire_dashboard_widget',
			__( 'Messages', $this->domain ),
			array( $this, 'dashboard_widget_display' )
		);
	}
	
	/**
	 * Displays either a list of messages,
	 * a message for when there are no messages to display or
	 * a message to prompt the user to configure the email account.
	 *
	 * @since 0.9.6
	 * @echo  string
	 */
	public function dashboard_widget_display() {
		
		$posts        = array();
		$stored_posts = $this->retrieve_flamingo_inbound_messages();
		foreach ( $stored_posts as $i => $stored_post ) {
			$post_meta = $stored_post->meta;
			
			// Assemble unviewed posts
			if ( ! isset( $post_meta['post_views_count'][0] ) || ( isset( $post_meta['post_views_count'][0] ) && (int) $post_meta['post_views_count'][0] < 1 ) ) {
				if ( isset( $post_meta['recipient'] ) && $this->recipient === $post_meta['recipient'] ) {
					$posts[] = $stored_post;
				}
			}
		}
		
		$Bonaire_Account_Settings_Status = new AdminIncludes\Bonaire_Settings_Status( $this->domain );
		// If there are no posts to show
		if ( empty( $posts ) ) {
			
			echo '<p class="message no-message">' . $this->no_messages_message . '</p>';
			echo $this->get_widget_footer();
		} // if there is no propperly configured account
		elseif ( true !== $Bonaire_Account_Settings_Status->get_settings_status( 'smtp', true ) ) {
			
			echo '<p class="message no-account">' . $this->configure_account_message . '</p>';
		} else {
			
			echo $this->display_widget_content( $posts );
		}
	}
	
	/**
	 * Retrieves an array containing the stored 'Flamingo Inbound Messages'.
	 *
	 * @return array
	 * @since 0.9.6
	 * @uses  Flamingo_Inbound_Message::find()
	 */
	private function retrieve_flamingo_inbound_messages() {
		
		return Flamingo_Inbound_Message::find();
	}
	
	/**
	 * Displays a list of message excerpts on the dashboard widget.
	 *
	 * @param array $posts
	 *
	 * @return string
	 * @uses  get_item( $post )
	 * @since 0.9.6
	 * @uses  get_post_meta( $post->id() )
	 */
	private function display_widget_content( $posts ) {
		
		if ( false !== $posts && ! empty( $posts ) ) {
			
			$number_posts = $this->Bonaire_Options->get_stored_options( 0 )->number_posts;
			$count        = 1;
			
			$string = '<ul>';
			foreach ( $posts as $i => $post ) {
				$post_meta  = get_post_meta( $post->id() );
				$post_views = isset( $post_meta['post_views_count'] ) ? $post_meta['post_views_count'][0] : '';
				if ( '' === $post_views && $count <= $number_posts ) {
					$string .= AdminPartials\Bonaire_Item_Display::item_display( $post );
				}
				$count ++;
			}
			$string .= '</ul>';
			
			$string .= $this->get_widget_footer();
			
			return $string;
		}
		
		return '';
	}
	
	/**
	 * Creates the footer for the dashboard widget.
	 *
	 * @return string
	 * @since 0.9.6
	 */
	private function get_widget_footer() {
		
		$count_posts = wp_count_posts( 'flamingo_inbound' );
		$all_count   = isset( $count_posts->publish ) ? (int) $count_posts->publish : 0;
		$spam_count  = isset( $count_posts->{'flamingo-spam'} ) ? (int) $count_posts->{'flamingo-spam'} : 0;
		$trash_count = isset( $count_posts->trash ) ? (int) $count_posts->trash : 0;
		$all         = 0 !== $all_count ? '<a href="/wp-admin/admin.php?page=flamingo_inbound">' . __( 'Inbox', $this->domain ) . '</a>' : '<span class="empty-link">' . __( 'All', $this->domain ) . '</span>';
		$spam        = 0 !== $spam_count ? '<a href="/wp-admin/admin.php?page=flamingo_inbound&post_status=spam">' . __( 'Spam', $this->domain ) . '</a>' : '<span class="empty-link">' . __( 'Spam', $this->domain ) . '</span>';
		$trash       = 0 !== $trash_count ? '<a href="/wp-admin/admin.php?page=flamingo_inbound&post_status=trash">' . __( 'Trash', $this->domain ) . '</a>' : '<span class="empty-link">' . __( 'Trash', $this->domain ) . '</span>';
		
		return '
			<ul class="subsub">
				<li class="all">' . $all . '<span class="count"> (<span class="all-count">' . $all_count . '</span>)</span> |</li>
				<li class="spam">' . $spam . '<span class="count"> (<span class="spam-count">' . $spam_count . '</span>)</span> |</li>
				<li class="trash">' . $trash . '<span class="count"> (<span class="trash-count">' . $trash_count . '</span>)</span></li>
			</ul>';
	}
	
	/**
	 * Localizes the script.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function localize_script() {
		
		wp_localize_script( 'bonaire-admin-js', 'BonaireWidget', array( 'no_messages_message' => $this->no_messages_message ) );
	}
	
}
