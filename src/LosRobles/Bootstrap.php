<?php

namespace Fragen\LosRobles;

class Bootstrap {
	public function run() {
		register_activation_hook(
			__FILE__,
			function () {
				$this->add_user_roles();
				$this->add_admin_voting();
			}
		);

		$this->init_voting();
		( new Base() )->load_hooks();
	}

	public function add_user_roles() {
		remove_role( 'members' );
		remove_role( 'non_members' );
		add_role(
			'members',
			'Members',
			[
				'read'                 => true,
				'can_vote'             => true,
				'email_single_user'    => true,
				'email_multiple_users' => true,
				'email_user_groups'    => true,
			]
		);
		add_role(
			'non_members',
			'Non-Members',
			[
				'read'     => true,
				'can_vote' => false,
			]
		);
	}

	public function add_admin_voting() {
		$role = get_role( 'administrator' );
		$role->add_cap( 'can_vote' );
		$role->add_cap( 'members' );
	}

	public function init_voting() {
		// add shortcode for [voting]
		add_shortcode( 'voting', 'lrhoa_voting_check_shortcode' );
		function lrhoa_voting_check_shortcode( $attr, $content = null ) {
			$atts = shortcode_atts( [ 'capability' => 'can_vote' ], $attr, 'voting' );
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
