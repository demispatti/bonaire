<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Bonaire_Post_Views
 */
class Bonaire_Post_Views {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $name The name of this plugin.
	 * @param      string $domain The domain of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $domain ) {
		
		$this->domain = $domain;
	}
	
	public function add_hooks() {
		
		add_action( 'admin_notices', array( $this, 'count_message_views' ) );
	}
	
	public function count_message_views() {
		
		$page = isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : false;
		$post_id = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : false;
		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;
		
		if ( 'flamingo_inbound' === $page && false !== $action ) {
			
			$this->set_post_views( $post_id );
		}
	}
	
	/**
	 * Updates the post view count post meta data
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	private function set_post_views( $post_id ) {
		
		$count_key = 'post_views_count';
		$count = get_post_meta( $post_id, $count_key, true );
		if ( $count === '' ) {
			delete_post_meta( $post_id, $count_key );
			
			return add_post_meta( $post_id, $count_key, 1 );
		}
		
		$count ++;
		
		return update_post_meta( $post_id, $count_key, $count );
		//delete_post_meta( $post_id, $count_key );
	}
	
	/**
	 * @param $post_id
	 *
	 * @return string
	 */
	public function get_post_views( $post_id ) {
		
		$count_key = 'post_views_count';
		$count = get_post_meta( $post_id, $count_key, true );
		if ( $count === '' ) {
			delete_post_meta( $post_id, $count_key );
			add_post_meta( $post_id, $count_key, 0 );
			
			return 0 . __( 'Views', $this->domain );
		}
		
		return $count . __( 'Views', $this->domain );
	}
	
	/**
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function update_post_view( $post_id ) {
		
		return $this->set_post_views( $post_id );
	}
	
}
