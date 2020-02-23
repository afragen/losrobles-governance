<?php

namespace Fragen\LosRobles;

class Base {
	public static $depts;
	private static $object = false;

	public static function instance() {
		if ( false === self::$object ) {
			self::$object = new self();
		}

		return self::$object;
	}

	public function __construct() {
	}

	public function load_hooks() {
		add_filter( 'login_redirect', [ $this, 'change_login_redirect' ], 10, 3 );
		// add_action( 'init', array( $this, 'add_custom_taxonomies' ), 0 );
		add_action( 'init', [ $this, 'create_post_type' ] );
		add_action( 'plugins_loaded', [ $this, 'hide_toolbar' ] );

		if ( is_admin() ) {
			( new Admin( $this ) )->load_hooks();
		}
	}

	// http://nathany.com/redirecting-wordpress-subscribers
	public function change_login_redirect( $redirect_to, $request_redirect_to, $user ) {
		if ( $user instanceof \WP_User && false === $user->has_cap( 'add_users' ) ) {
			return get_bloginfo( 'siteurl' );
		}

		return $redirect_to;
	}

	// http://digwp.com/2011/04/admin-bar-tricks/
	public function hide_toolbar() {
		// show admin bar only for admins
		if ( ! current_user_can( 'manage_options' ) ) {
			// add_filter( 'show_admin_bar', '__return_false' );
		}

		// show admin bar only for admins and editors
		// if( ! current_user_can( 'edit_posts' ) ) { add_filter( 'show_admin_bar', '__return_false' ); }
	}

	/**
	 * Add custom taxonomies
	 *
	 * Additional custom taxonomies can be defined here
	 * http://codex.wordpress.org/Function_Reference/register_taxonomy
	 * http://wp.smashingmagazine.com/2012/01/04/create-custom-taxonomies-wordpress/
	 */
	public function add_custom_taxonomies() {
		// Add new "Departments" taxonomy to Posts
		register_taxonomy(
			'department',
			'lrhoa_voting',
			[
				// Hierarchical taxonomy (like categories)
				'hierarchical' => false,
				// This array of options controls the labels displayed in the WordPress Admin UI
				'labels'       => [
					'name'              => _x( 'Departments', 'taxonomy general name' ),
					'singular_name'     => _x( 'Department', 'taxonomy singular name' ),
					'search_items'      => __( 'Search Departments' ),
					'all_items'         => __( 'All Departments' ),
					'parent_item'       => __( 'Parent Department' ),
					'parent_item_colon' => __( 'Parent Department:' ),
					'edit_item'         => __( 'Edit Department' ),
					'update_item'       => __( 'Update Department' ),
					'add_new_item'      => __( 'Add New Department' ),
					'new_item_name'     => __( 'New Department Name' ),
					'menu_name'         => __( 'Departments' ),
				],
				'query_var'    => 'department',
				// Control the slugs used for this taxonomy
				'rewrite'      => [
					'slug'         => 'department', // This controls the base slug that will display before each term
					'with_front'   => false, // Don't display the category base before "/locations/"
					'hierarchical' => true, // This will allow URL's like "/locations/boston/cambridge/"
				],
			]
		);
	}

	public function create_post_type() {
		register_post_type(
			'lrhoa_voting',
			[
				'labels'        => [
					'name'          => __( 'Elections' ),
					'singular_name' => __( 'Election' ),
				],
				'public'        => true,
				'menu_position' => 5,
				'menu_icon'     => 'dashicons-chart-bar',
				'rewrite'       => [ 'slug' => 'elections' ],
				// 'taxonomies'    => [ 'department' ],
				'supports'      => [ 'title', 'editor', 'comments', 'post-formats' ],
			]
		);
	}
}
