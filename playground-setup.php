<?php
if (!defined('ABSPATH')) {
	exit;
}

// Make sure the post type exists even if the plugin hasn't loaded yet.
if (!post_type_exists('reference_entry')) {
	require_once __DIR__ . '/includes/post-types.php';
	rl_register_post_types();
}

// Skip setup if entries already exist.
if (get_posts(array('post_type' => 'reference_entry', 'numberposts' => 1, 'post_status' => 'any'))) {
	return;
}

update_option('blogname', 'Reference Library Demo');

$entries = array(
	array(
		'title' => 'HTTP Status Codes',
		'excerpt' => 'What the common 2xx, 3xx, 4xx and 5xx responses mean.',
		'content' => 'Status codes tell a client how a request went. 2xx means success, 3xx means redirect, 4xx means the client made a mistake, and 5xx means the server failed.',
		'topic' => 'Web Basics',
		'source' => 'MDN Web Docs',
	),
	array(
		'title' => 'CORS Explained',
		'excerpt' => 'Why browsers block some cross-site requests and how servers allow them.',
		'content' => 'Cross-Origin Resource Sharing lets a server declare which other origins may read its responses. The browser enforces it, so the fix always lives in the server headers.',
		'topic' => 'Web Basics',
		'source' => 'MDN Web Docs',
	),
	array(
		'title' => 'Prepared Statements',
		'excerpt' => 'Separating SQL from data to prevent injection.',
		'content' => 'A prepared statement sends the query shape first and the values separately, so user input can never change the structure of the query.',
		'topic' => 'Databases',
		'source' => 'PHP Manual',
	),
	array(
		'title' => 'PDO Basics',
		'excerpt' => 'Connecting to a database and running queries with PDO.',
		'content' => 'PDO gives PHP one consistent interface across database engines. Create a connection, prepare a statement, execute it with values, then fetch the rows.',
		'topic' => 'PHP',
		'source' => 'PHP Manual',
	),
	array(
		'title' => 'Database Indexes',
		'excerpt' => 'How an index speeds up lookups and what it costs.',
		'content' => 'An index lets the database find rows without scanning the whole table. It speeds up reads but adds work to every write, so index the columns you search on.',
		'topic' => 'Databases',
		'source' => 'SQLite Documentation',
	),
	array(
		'title' => 'Array Functions',
		'excerpt' => 'The PHP array helpers worth knowing by heart.',
		'content' => 'Functions like array_map, array_filter and array_column cover most day-to-day data shaping without writing manual loops.',
		'topic' => 'PHP',
		'source' => 'PHP Manual',
	),
);

foreach ($entries as $entry) {
	$post_id = wp_insert_post(array(
		'post_type' => 'reference_entry',
		'post_status' => 'publish',
		'post_title' => $entry['title'],
		'post_excerpt' => $entry['excerpt'],
		'post_content' => $entry['content'],
	));

	wp_set_object_terms($post_id, $entry['topic'], 'rl_topic');
	wp_set_object_terms($post_id, $entry['source'], 'rl_source');
}

wp_insert_post(
	array(
		'post_title' => 'Reference Library',
		'post_name' => 'library',
		'post_content' => '[reference_library]',
		'post_status' => 'publish',
		'post_type' => 'page',
	)
);

flush_rewrite_rules();