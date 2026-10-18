<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load the script on pages that use the shortcode, and hand it rlData.
 */
function rl_enqueue_assets() {
	global $post;

	if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'reference_library' ) ) {
		return;
	}

	wp_enqueue_script(
		'rl-search',
		RL_URL . 'assets/js/reference-library.js',
		array( 'jquery' ),
		RL_VERSION,
		true
	);

	// This is where rlData comes from: PHP builds it, WordPress prints it as a JS object.
	wp_localize_script(
		'rl-search',
		'rlData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'rl_search_nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'rl_enqueue_assets' );

/**
 * Handle the AJAX request: verify the nonce, run the query, return the HTML.
 */
function rl_ajax_search() {
	// Dies with an error if the nonce is missing or wrong.
	check_ajax_referer( 'rl_search_nonce', 'nonce' );

	$search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
	$topic  = isset( $_POST['topic'] ) ? sanitize_title( wp_unslash( $_POST['topic'] ) ) : '';
	$source = isset( $_POST['source'] ) ? sanitize_title( wp_unslash( $_POST['source'] ) ) : '';

	$query = rl_get_entries( $search, $topic, $source );

	// Sends { success: true, data: { html: ... } } as JSON and stops.
	wp_send_json_success( array( 'html' => rl_render_results( $query ) ) );
}
// Logged-in users and logged-out visitors hit different hooks, so register both.
add_action( 'wp_ajax_rl_search', 'rl_ajax_search' );
add_action( 'wp_ajax_nopriv_rl_search', 'rl_ajax_search' );