<?php
/**
 * Dashboard Widget Controller
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Controllers;

use Bonaire\Admin\Services\OptionsService;

/**
 * Handles dashboard widget functionality.
 *
 * @since 1.0.0
 */
class DashboardWidgetController {

	/**
	 * Options service.
	 *
	 * @var OptionsService
	 */
	private $options_service;

	/**
	 * Constructor.
	 *
	 * @param OptionsService $options_service Options service.
	 */
	public function __construct( OptionsService $options_service ) {
		$this->options_service = $options_service;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widget' ] );
	}

	/**
	 * Add dashboard widget.
	 *
	 * @return void
	 */
	public function add_dashboard_widget(): void {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'bonaire_dashboard_widget',
			__( 'Bonaire - Recent Messages', 'bonaire' ),
			[ $this, 'render_dashboard_widget' ]
		);
	}

	/**
	 * Render dashboard widget.
	 *
	 * @return void
	 */
	public function render_dashboard_widget(): void {
		// Query recent Flamingo messages.
		$args = [
			'post_type'      => 'flamingo_inbound',
			'posts_per_page' => 5,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		];

		$messages = get_posts( $args );

		if ( empty( $messages ) ) {
			echo '<p>' . esc_html__( 'No messages found.', 'bonaire' ) . '</p>';
			return;
		}

		echo '<ul class="bonaire-messages-list">';
		foreach ( $messages as $message ) {
			printf(
				'<li><a href="%s">%s</a> - %s</li>',
				esc_url( get_edit_post_link( $message->ID ) ),
				esc_html( $message->post_title ),
				esc_html( get_the_date( '', $message->ID ) )
			);
		}
		echo '</ul>';
	}
}
