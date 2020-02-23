<?php

namespace Fragen\LosRobles;

class Admin {
	public function __construct() {
		// Force Strong Password plugin -- all users
		if ( function_exists( 'slt_fsp_init' ) ) {
			// plugin is activated
			add_filter( 'slt_fsp_caps_check', '__return_empty_array' );
		}
	}

	public function load_hooks() {
		// Add additional custom fields to profile page
		// http://pastebin.com/0zhWAtqY
		add_action( 'show_user_profile', [ $this, 'show_extra_profile_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'show_extra_profile_fields' ] );

		// Save data input from custom field on profile page
		add_action( 'personal_options_update', [ $this, 'save_extra_profile_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'save_extra_profile_fields' ] );
		// add_action( 'admin_print_scripts-profile.php', [ $this, 'hide_admin_items' ] );
		// add_action( 'admin_print_styles-user-edit.php', [ $this, 'hide_admin_items' ] );

		add_action( 'admin_menu', [ $this, 'edit_admin_menus' ] );
	}

	public function show_extra_profile_fields( $user ) {
		$username      = $user->get( 'user_login' );
		$street_number = explode( '-', $username );
		$street_number = array_shift( $street_number );

		?>
			<h3><?php _e( 'Los Robles HOA Info' ); ?></h3>
			<table class="form-table">
				<tr>
					<th><label for='street_number' id='street_number'><?php _e( 'Los Robles Street Number' ); ?></label></th>
					<td>
						<?php esc_attr_e( $street_number ); ?>
					</td>
				</tr>
				<tr>
					<th><label for='phone_number' id='phone_number'><?php _e( 'Los Robles Phone Number' ); ?></label></th>
					<td>
						<input class="lrhoa-setting" type="text" id="lrhoa_phone_number" name="lrhoa_phone_number" value="<?php esc_attr_e( $user->get( 'lrhoa_phone_number' ) ); ?>">
						<p class='description'><?php _e( 'Local phone number' ); ?></p>
					</td>
				</tr>
				<tr>
					<th><label for='emergency_number' id='emergency_number'><?php _e( 'Emergency Contact Number' ); ?></label></th>
					<td>
						<input class="lrhoa-setting" type="text" id="lrhoa_emergency_number" name="lrhoa_emergency_number" value="<?php esc_attr_e( $user->get( 'lrhoa_emergency_number' ) ); ?>">
						<p class='description'><?php _e( 'Emergency contact phone number' ); ?></p>
					</td>
				</tr>

			</table>
		<?php
	}

	public function save_extra_profile_fields( $user_id ) {
		if ( ! current_user_can( 'add_users' ) ) {
			return false;
		}

		$phone_number     = preg_replace( '/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $_POST['lrhoa_phone_number'] );
		$emergency_number = preg_replace( '/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2-$3', $_POST['lrhoa_emergency_number'] );

		// copy this line for other fields
		update_user_meta( $user_id, 'lrhoa_street_number', $_POST['lrhoa_street_number'] );
		update_user_meta( $user_id, 'lrhoa_phone_number', $phone_number );
		update_user_meta( $user_id, 'lrhoa_emergency_number', $emergency_number );
	}

	// hide toolbar option in profile - http://digwp.com/2011/04/admin-bar-tricks/
	public function hide_admin_items() {
		if ( ! current_user_can( 'add_users' ) ) {
			?>
			<style type="text/css">
				.show-admin-bar { display: none; }
				input#eddc_user_paypal.regular-text, input#eddc_user_rate.small-text { display: none; }
				input[id*="email_users_accept_"] { display: none; }
				tr.user-nickname-wrap { display: none; }
			</style>
			<?php
		}
	}

	public function edit_admin_menus() {
		remove_menu_page( 'link-manager.php' );
		if ( ! current_user_can( 'add_users' ) ) {
			remove_menu_page( 'tools.php' );
			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'edit.php?post_type=feedback' );
			remove_menu_page( 'options-general.php' );
			// remove_menu_page( 'edit.php?post_type=drmc_voting' );
		}
		// if ( ! current_user_can( 'publish_posts' ) ) {
		// remove_menu_page( 'edit.php?post_type=tribe_events' );
		// }
	}
}
