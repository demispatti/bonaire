<?php

namespace Bonaire\Admin\Partials;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The class responsible for creating the messages that are being displayed by the dashboard widget.
 *
 * @since            0.9.6
 * @package           bonaire
 * @subpackage        bonaire/admin/partials
 * @author            Demis Patti <demis@demispatti.ch>
 */
class Bonaire_Item_Display {
	
	/**
	 * The domain of the plugin.
	 *
	 * @var      string $domain
	 * @since   0.9.6
	 * @access   public static
	 */
	public static $domain;
	
	/**
	 * Bonaire_Item_Display constructor.
	 *
	 * @param $domain
	 *
	 * @since0.9.6
	 * @return void
	 */
	public function __construct( $domain ) {
		
		self::$domain = $domain;
	}
	
	/**
	 * Returns a string containing the excerpt from the message body only.
	 *
	 * @param string $message
	 * @param int $charlength
	 *
	 * @since0.9.6
	 * @return string $html
	 */
	public static function get_excerpt( $message, $charlength = 54 ) {
		
		$html = $message;
		$html = preg_replace( " ([.*?])", '', $html );
		$html = strip_shortcodes( $html );
		$html = strip_tags( $html );
		$html = substr( $html, 0, $charlength );
		$html = substr( $html, 0, strripos( $html, " " ) );
		
		return $html . '...';
	}
	
	/**
	 * Returns a string containing the message excerpt for the dashboard widget.
	 *
	 * @param object $post
	 *
	 * @since0.9.6
	 * @return string $html
	 */
	public static function item_display( $post ) {
		
		$post_id = $post->id;
		$email = $post->fields['your-email'];
		$name = $post->fields['your-name'];
		$subject = $post->fields['your-subject'];
		$message = $post->fields['your-message'];
		$excerpt = self::get_excerpt( $message );
		$edit_post_link = site_url() . '/wp-admin/admin.php?page=flamingo_inbound&post=' . $post_id . '&action=edit';
		
		$read_nonce = wp_create_nonce( 'bonaire_mark_as_read_nonce_' . $post_id );
		$spam_nonce = wp_create_nonce( 'bonaire_mark_as_spam_nonce_' . $post_id );
		$trash_nonce = wp_create_nonce( 'bonaire_move_to_trash_nonce_' . $post_id );
		
		ob_start();
		?>

        <li id="message_<?php echo $post_id ?>" class="message comment-item">

            <a class="message-icon" href="<?php echo $edit_post_link ?>" style="font-style=normal;"
                title="<?php echo __( ' View Message', self::$domain ) ?>"><span class="dashicons dashicons-email"></span></a>

            <div class="dashboard-message-wrap has-row-actions">
                <p class="comment-meta">
                    <cite class="comment-author">
                        <span class="meta"><?php echo __( 'From', self::$domain ) ?>
                            <a target="_blank"
                                href="/wp-admin/admin.php?page=flamingo&s=<?php echo $email ?>"><?php echo $name ?></a> <?php echo __( 'regarding', self::$domain ) ?>
                            <a
                                href="<?php echo $edit_post_link ?>" style="font-style=normal;"><?php echo $subject ?></a>
                        </span>
                    </cite>
                </p>
                <blockquote><?php echo $excerpt ?></blockquote>
                <p class="row-actions">
					<span class="reply">
						<a class="bonaire-dashboard-reply-button vim-r hide-if-no-js"
                            aria-label="<?php echo __( 'Reply to this message', self::$domain ) ?>"
                            href="<?php echo $edit_post_link ?>#bonaire-form-meta-box"
                            title="<?php echo __( ' Reply', self::$domain ) ?>"><?php echo __( 'Reply', self::$domain ) ?></a>
					</span>
                    <span class="spam"> |
					<a onclick="return false;" data-nonce="<?php echo $spam_nonce ?>" class="bonaire-mark-as-spam-button vim-r hide-if-no-js"
                        aria-label="<?php echo __( 'Mark as spam', self::$domain ) ?>" data-postid="<?php echo $post_id ?>" href="#"
                        title="<?php echo __( ' Spam', self::$domain ) ?>"><?php echo __( 'Spam', self::$domain ) ?></a>
					</span>
                    <span class="trash"> |
						<a onclick="return false;" data-nonce="<?php echo $trash_nonce ?>" class="bonaire-move-to-trash-button vim-r hide-if-no-js"
                            aria-label="<?php echo __( 'Move to trash', self::$domain ) ?>" data-postid="<?php echo $post_id ?>" href="#"
                            title="<?php echo __( ' Trash', self::$domain ) ?>"><?php echo __( 'Trash', self::$domain ) ?></a>
					</span>
                    <span class="mark-as-read-local"> |
						<a onclick="return false;" data-nonce="<?php echo $read_nonce ?>" data-postid="<?php echo $post->id ?>"
                            class="bonaire-mark-as-read-button vim-r hide-if-no-js" aria-label="<?php echo __( 'Mark as read', self::$domain ) ?>"
                            href="#"
                            title="<?php echo __( 'Mark as read', self::$domain ) ?>"><?php echo __( 'Mark as read', self::$domain ) ?></a>
					</span>
                    <span class="view"> |
						<a class="bonaire-view-button vim-r hide-if-no-js"
                            aria-label="<?php echo __( 'View message', self::$domain ) ?>"
                            href="<?php echo $edit_post_link ?>"
                            title="<?php echo __( ' View', self::$domain ) ?>"><?php echo __( 'View', self::$domain ) ?></a>
					</span>
                </p>
            </div>
        </li>
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
}
