<?php
/**
 * Settings Page View
 *
 * @package Bonaire
 * @since 1.0.0
 */

namespace Bonaire\Admin\Views;

use Bonaire\Admin\Services\OptionsService;

/**
 * Renders the settings page.
 *
 * @since 1.0.0
 */
class SettingsPageView {

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
	 * Render the settings page.
	 *
	 * @return void
	 */
	public function render(): void {
		$options = $this->options_service->get( 0 );
		?>
		<div class="wrap bonaire-settings-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form id="bonaire_settings_form" method="post" action="options.php">
				<?php
				settings_fields( 'bonaire_options_group' );
				wp_nonce_field( 'bonaire_admin_nonce', 'bonaire_nonce' );
				?>

				<div id="connection_details" class="bonaire-settings-container">
					<h2><?php esc_html_e( 'Email Account Settings', 'bonaire' ); ?></h2>

					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="username"><?php esc_html_e( 'Username', 'bonaire' ); ?></label>
							</th>
							<td>
								<input type="text" id="username" name="bonaire_options[username]" value="<?php echo esc_attr( $options->username ?? '' ); ?>" class="regular-text">
								<p class="description"><?php esc_html_e( 'Your email address or username.', 'bonaire' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="password"><?php esc_html_e( 'Password', 'bonaire' ); ?></label>
							</th>
							<td>
								<input type="password" id="password" name="bonaire_options[password]" value="<?php echo ! empty( $options->password ) ? '*****' : ''; ?>" class="regular-text">
								<p class="description"><?php esc_html_e( 'Your email password (stored encrypted).', 'bonaire' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtp_host"><?php esc_html_e( 'SMTP Host', 'bonaire' ); ?></label>
							</th>
							<td>
								<input type="text" id="smtp_host" name="bonaire_options[smtp_host]" value="<?php echo esc_attr( $options->smtp_host ?? '' ); ?>" class="regular-text">
								<p class="description"><?php esc_html_e( 'e.g., smtp.gmail.com', 'bonaire' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtp_port"><?php esc_html_e( 'SMTP Port', 'bonaire' ); ?></label>
							</th>
							<td>
								<input type="number" id="smtp_port" name="bonaire_options[smtp_port]" value="<?php echo esc_attr( $options->smtp_port ?? 465 ); ?>" class="small-text">
								<p class="description"><?php esc_html_e( '465 for SSL or 587 for TLS', 'bonaire' ); ?></p>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="smtpsecure"><?php esc_html_e( 'Encryption', 'bonaire' ); ?></label>
							</th>
							<td>
								<select id="smtpsecure" name="bonaire_options[smtpsecure]">
									<option value="ssl" <?php selected( $options->smtpsecure ?? 'ssl', 'ssl' ); ?>>SSL</option>
									<option value="tls" <?php selected( $options->smtpsecure ?? 'ssl', 'tls' ); ?>>TLS</option>
								</select>
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="from"><?php esc_html_e( 'From Email', 'bonaire' ); ?></label>
							</th>
							<td>
								<input type="email" id="from" name="bonaire_options[from]" value="<?php echo esc_attr( $options->from ?? '' ); ?>" class="regular-text">
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="fromname"><?php esc_html_e( 'From Name', 'bonaire' ); ?></label>
							</th>
							<td>
								<input type="text" id="fromname" name="bonaire_options[fromname]" value="<?php echo esc_attr( $options->fromname ?? '' ); ?>" class="regular-text">
							</td>
						</tr>

						<tr>
							<th scope="row">
								<label for="save_reply"><?php esc_html_e( 'Save Replies to IMAP', 'bonaire' ); ?></label>
							</th>
							<td>
								<select id="save_reply" name="bonaire_options[save_reply]">
									<option value="no" <?php selected( $options->save_reply ?? 'no', 'no' ); ?>><?php esc_html_e( 'No', 'bonaire' ); ?></option>
									<option value="yes" <?php selected( $options->save_reply ?? 'no', 'yes' ); ?>><?php esc_html_e( 'Yes', 'bonaire' ); ?></option>
								</select>
							</td>
						</tr>
					</table>

					<?php submit_button( __( 'Save Settings', 'bonaire' ), 'primary bonaire-save-options-button' ); ?>
					<button type="button" class="button bonaire-reset-options-button"><?php esc_html_e( 'Reset to Defaults', 'bonaire' ); ?></button>
					<button type="button" class="button bonaire-test-smtp-settings-button"><?php esc_html_e( 'Test SMTP Connection', 'bonaire' ); ?></button>
				</div>
			</form>
		</div>
		<?php
	}
}
