<?php

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Defines and displays the meta box.
 *
 * @link
 * @since             0.1.0
 * @package           cb_parallax
 * @subpackage        cb_parallax/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class Bonaire_Meta_Box {
	
	/**
	 * The domain of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $domain The domain of the plugin.
	 */
	private $domain;
	
	/**
	 * @var Bonaire_Reply_Form_Display $Bonaire_Reply_Form_Display
	 */
	private $Bonaire_Reply_Form_Display;
	
	/**
	 * @var Bonaire_Adapter $Bonaire_Adapter
	 */
	private $Bonaire_Adapter;
	
	/**
	 * Bonaire_Meta_Box constructor.
	 *
	 * @param string $domain
	 * @param Bonaire_Reply_Form_Display $Bonaire_Reply_Form_Display
	 * @param Bonaire_Adapter $Bonaire_Adapter
	 */
	public function __construct( $domain, $Bonaire_Reply_Form_Display, $Bonaire_Adapter ) {
		
		$this->domain = $domain;
		$this->Bonaire_Reply_Form_Display = $Bonaire_Reply_Form_Display;
		$this->Bonaire_Adapter = $Bonaire_Adapter;
	}
	
	public function add_hooks() {
		
		add_action( 'load-flamingo_page_flamingo_inbound', array( $this, 'add_meta_box' ) );
	}
	
	public function add_meta_box() {
		
		add_meta_box(
			'bonaire-form-meta-box',
			__( 'Reply', $this->domain ),
			array( $this, 'display_reply_form_meta_box' ),
			'flamingo_page_flamingo_inbound'
		);
	}
	
	/**
	 * Creates the reply form and echoes it out
	 *
	 * @echo string $string
	 */
	public function display_reply_form_meta_box() {
		
		$post_id = (int) $_REQUEST['post'];
		
		$your_subject = $this->Bonaire_Adapter->get_field( $post_id, 'your-subject' );
		$string = $this->Bonaire_Reply_Form_Display->get_form( $your_subject, $this->Bonaire_Adapter );
		
		echo $string;
	}
	
}
