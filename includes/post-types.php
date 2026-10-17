<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function rl_register_post_types() {
	register_post_type( 'reference_entry', array(
		'labels'       => array(
			'name'          => 'Reference Entries',
			'singular_name' => 'Reference Entry',
			'add_new_item'  => 'Add New Entry',
			'edit_item'     => 'Edit Entry',
			'menu_name'     => 'Reference Library',
		),
		'public'       => true,
		'has_archive'  => true,
		'menu_icon'    => 'dashicons-book-alt',
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'reference' ),
	) );

	register_taxonomy( 'rl_topic', 'reference_entry', array(
		'labels'       => array(
			'name'          => 'Topics',
			'singular_name' => 'Topic',
		),
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'topic' ),
	) );

	register_taxonomy( 'rl_source', 'reference_entry', array(
		'labels'       => array(
			'name'          => 'Sources',
			'singular_name' => 'Source',
		),
		'hierarchical' => false,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'source' ),
	) );
}
add_action( 'init', 'rl_register_post_types' );