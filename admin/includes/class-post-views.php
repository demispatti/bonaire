<?php
namespace Bonaire\Admin\Includes;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for keeping track of the post views
 * in order to show / hide messages on the dashboard widget.
 *
 * @since             0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/includes
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Post_Views {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since    0.9.6
	 * @access   private
	 */
	private $domain;
	
	/**
	 * Bonaire_Post_Views constructor.
	 *
	 * @param string $domain
	 *
	 * @return void
	 * @dev_helper
	 * @since 0.9.6
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
	}
	
	/**
	 * Registers the methods that need to be hooked with WordPress.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function add_hooks() {
		
		add_action( 'admin_notices', array( $this, 'count_message_views' ) );
	}
	
	/**
	 * Sets the post view count if conditions are met.
	 *
	 * @return void
	 * @since 0.9.6
	 */
	public function count_message_views() {
		
		$page    = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : false;
		$post_id = isset( $_REQUEST['post'] ) ? sanitize_text_field( $_REQUEST['post'] ) : false;
		$action  = isset( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : false;
		
		if ( 'flamingo_inbound' === $page && false !== $action && (int) $post_id ) {
			$this->set_post_views( (int) $post_id );
		}
	}
	
	/**
	 * Returns or sets and returns the post view count.
	 *
	 * @param int $post_id
	 *
	 * @return string
	 * @since 0.9.6
	 */
	public function get_post_views( $post_id ) {
		
		$count_key = 'post_views_count';
		$count     = get_post_meta( $post_id, $count_key, true );
		if ( $count === '' ) {
			delete_post_meta( $post_id, $count_key );
			add_post_meta( $post_id, $count_key, 0 );
			
			return 0 . __( 'Views', $this->domain );
		}
		
		return $count . __( 'Views', $this->domain );
	}
	
	/**
	 * Calls the method that updates the post views.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 * @since 0.9.6
	 */
	public function update_post_view( $post_id ) {
		
		return $this->set_post_views( $post_id );
	}
	
	/**
	 * Updates the post view count post meta data,
	 * whenever a message was viewed, marked as spam, trashed or marked as read.
	 *
	 * @param int $post_id
	 *
	 * @return bool
	 * @since 0.9.6
	 */
	private function set_post_views( $post_id ) {
		
		$count_key = 'post_views_count';
		$count     = get_post_meta( $post_id, $count_key, true );
		if ( $count === '' ) {
			delete_post_meta( $post_id, $count_key );
			
			return add_post_meta( $post_id, $count_key, 1 );
		}
		
		$count ++;
		
		return update_post_meta( $post_id, $count_key, $count );
	}
	
}
