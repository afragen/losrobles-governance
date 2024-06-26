<?php

namespace Fragen\LosRobles;

class Bootstrap {

	public function run() {
		$this->add_user_roles();
		$this->add_extra_admin_caps();
		$this->add_caps_board_member();
		$this->add_caps_member_noprivs();
		$this->init_voting();
		( new Base() )->load_hooks();
	}

	public function add_user_roles() {
		$roles = new \WP_Roles();
		$roles->remove_role( 'members' );
		$roles->remove_role( 'non_members' );
		$roles->remove_role( 'board_member' );
		$roles->remove_role( 'member_noprivs' );
		$roles->add_role(
			'members',
			'Members',
			array(
				'read'                 => true,
				'can_vote'             => true,
				'edit_lrhoa_fields'    => true,
				'email_single_user'    => true,
				'email_multiple_users' => true,
				'email_user_groups'    => true,
			)
		);
		$roles->add_role(
			'board_member',
			'Board Member',
			array()
		);
		$roles->add_role(
			'non_members',
			'Non-Members',
			array(
				'read'     => true,
				'can_vote' => false,
			)
		);
		$roles->add_role(
			'member_noprivs',
			'Member No Privileges',
			array()
		);
	}

	private function add_extra_admin_caps() {
		$role = get_role( 'administrator' );
		if ( $role instanceof \WP_Role ) {
			$role->add_cap( 'edit_lrhoa_fields' );
			$role->add_cap( 'members' );
			$role->add_cap( 'board_member' );
		}
	}

	private function add_caps_board_member() {
		$role = get_role( 'board_member' );
		if ( $role instanceof \WP_Role ) {
			$role->add_cap( 'members' );
		}
	}

	private function add_caps_member_noprivs() {
		$role = get_role( 'member_noprivs' );
		if ( $role instanceof \WP_Role ) {
			$role->add_cap( 'non_members' );
		}
	}

	private function init_voting() {
		// add shortcode for [voting]
		add_shortcode( 'voting', 'lrhoa_voting_check_shortcode' );
		function lrhoa_voting_check_shortcode( $attr, $content = null ) {
			$atts = shortcode_atts( array( 'capability' => 'can_vote' ), $attr, 'voting' );
			if ( current_user_can( $atts['capability'] ) && ! is_null( $content ) && ! is_feed() ) {
				return do_shortcode( $content );
			}

			return 'You do not have sufficient privileges to vote for this matter.';
		}

		// secret ballots in wp-polls
		add_filter( 'poll_log_show_log_filter', '__return_false' );
		add_filter( 'poll_log_secret_ballot', '__return_empty_string' );
	}
}
