<?php
/**
 * Meta Box Controller
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Controllers;

/**
 * Handles meta box functionality for Flamingo messages.
 *
 * @since 1.0.0
 */
class MetaBoxController {

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
	}

	/**
	 * Add reply meta box to Flamingo messages.
	 *
	 * @return void
	 */
	public function add_meta_box(): void {
		$screen = get_current_screen();

		if ( ! $screen || 'flamingo_inbound' !== $screen->post_type ) {
			return;
		}

		add_meta_box(
			'bonaire_reply_meta_box',
			__( 'Send Reply', 'bonaire' ),
			[ $this, 'render_meta_box' ],
			'flamingo_inbound',
			'normal',
			'high'
		);
	}

	/**
	 * Render reply meta box.
	 *
	 * @param \WP_Post $post Current post object.
	 * @return void
	 */
	public function render_meta_box( $post ): void {
		wp_nonce_field( 'bonaire_reply_nonce', 'bonaire_reply_nonce' );

		// Get message details from Flamingo.
		$meta = get_post_meta( $post->ID );
		$email = $meta['_field_email'][0] ?? '';
		$name = $meta['_field_your-name'][0] ?? '';

		?>
		<div class="bonaire-reply-form">
			<p>
				<label for="bonaire_reply_to"><?php esc_html_e( 'To:', 'bonaire' ); ?></label>
				<input type="text" id="bonaire_reply_to" value="<?php echo esc_attr( $email ); ?>" readonly class="widefat">
			</p>
			<p>
				<label for="bonaire_reply_subject"><?php esc_html_e( 'Subject:', 'bonaire' ); ?></label>
				<input type="text" id="bonaire_reply_subject" name="bonaire_reply_subject" class="widefat" placeholder="<?php esc_attr_e( 'Re: Your message', 'bonaire' ); ?>">
			</p>
			<p>
				<label for="bonaire_reply_message"><?php esc_html_e( 'Message:', 'bonaire' ); ?></label>
				<textarea id="bonaire_reply_message" name="bonaire_reply_message" rows="10" class="widefat"></textarea>
			</p>
			<p>
				<button type="button" class="button button-primary bonaire-submit-reply-button" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
					<?php esc_html_e( 'Send Reply', 'bonaire' ); ?>
				</button>
			</p>
		</div>
		<?php
	}
}
