<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query published entries, optionally filtered by search text, topic and source.
 */
function rl_get_entries( $search = '', $topic = '', $source = '' ) {
	$args = array(
		'post_type'      => 'reference_entry',
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( '' !== $search ) {
		$args['s'] = $search;
	}

	$tax_query = array();

	if ( '' !== $topic ) {
		$tax_query[] = array(
			'taxonomy' => 'rl_topic',
			'field'    => 'slug',
			'terms'    => $topic,
		);
	}

	if ( '' !== $source ) {
		$tax_query[] = array(
			'taxonomy' => 'rl_source',
			'field'    => 'slug',
			'terms'    => $source,
		);
	}

	if ( $tax_query ) {
		$tax_query['relation'] = 'AND';
		$args['tax_query']     = $tax_query;
	}

	return new WP_Query( $args );
}

/**
 * Build the HTML for a list of results (reused later by the AJAX handler).
 */
function rl_render_results( $query ) {
	ob_start();

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<article class="rl-entry">
				<h3 class="rl-entry-title">
					<a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
				</h3>
				<div class="rl-entry-excerpt"><?php echo wp_kses_post( get_the_excerpt() ); ?></div>
				<?php
				$labels = array();
				foreach ( array( 'rl_topic', 'rl_source' ) as $taxonomy ) {
					$terms = get_the_terms( get_the_ID(), $taxonomy );
					if ( $terms && ! is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							$labels[] = $term->name;
						}
					}
				}
				?>
				<?php if ( $labels ) : ?>
					<p class="rl-entry-tags">
						<?php foreach ( $labels as $label ) : ?>
							<span class="rl-tag"><?php echo esc_html( $label ); ?></span>
						<?php endforeach; ?>
					</p>
				<?php endif; ?>
			</article>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<p class="rl-no-results">' . esc_html__( 'No entries found.', 'reference-library' ) . '</p>';
	}

	return ob_get_clean();
}

/**
 * Output <option> tags for a taxonomy dropdown.
 */
function rl_term_options( $taxonomy, $selected ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return '';
	}

	$html = '';
	foreach ( $terms as $term ) {
		$html .= sprintf(
			'<option value="%s"%s>%s</option>',
			esc_attr( $term->slug ),
			selected( $selected, $term->slug, false ),
			esc_html( $term->name )
		);
	}

	return $html;
}

/**
 * [reference_library] shortcode: search box, topic/source filters and results.
 * Works without JavaScript (plain GET form); AJAX will enhance it later.
 */
function rl_shortcode() {
	// Read-only GET filters, sanitized; no nonce needed for a search form.
	$search = isset( $_GET['rl_search'] ) ? sanitize_text_field( wp_unslash( $_GET['rl_search'] ) ) : '';
	$topic  = isset( $_GET['rl_topic'] ) ? sanitize_title( wp_unslash( $_GET['rl_topic'] ) ) : '';
	$source = isset( $_GET['rl_source'] ) ? sanitize_title( wp_unslash( $_GET['rl_source'] ) ) : '';

	$query = rl_get_entries( $search, $topic, $source );

	ob_start();
	?>
	<div class="rl-wrap">
		<form class="rl-form" method="get">
			<input type="search" name="rl_search" class="rl-search" placeholder="<?php esc_attr_e( 'Search the library...', 'reference-library' ); ?>" value="<?php echo esc_attr( $search ); ?>">

			<select name="rl_topic" class="rl-topic">
				<option value=""><?php esc_html_e( 'All topics', 'reference-library' ); ?></option>
				<?php echo rl_term_options( 'rl_topic', $topic ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in rl_term_options(). ?>
			</select>

			<select name="rl_source" class="rl-source">
				<option value=""><?php esc_html_e( 'All sources', 'reference-library' ); ?></option>
				<?php echo rl_term_options( 'rl_source', $source ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in rl_term_options(). ?>
			</select>

			<button type="submit" class="rl-submit"><?php esc_html_e( 'Search', 'reference-library' ); ?></button>
		</form>

		<div class="rl-results">
			<?php echo rl_render_results( $query ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in rl_render_results(). ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'reference_library', 'rl_shortcode' );