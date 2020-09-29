<?php

namespace PageFor404;

add_filter( 'allowed_options', __NAMESPACE__ . '\filter_allowed_options' );
add_action( 'admin_init', __NAMESPACE__ . '\add_settings_fields' );
add_filter( 'template_include', __NAMESPACE__ . '\template_include' );

/**
 * Retrieve the page ID set for the page_for_404 option.
 *
 * @return int The page ID for page_for_404.
 */
function get_page_id() {
	return absint( get_option( 'page_for_404', 0 ) );
}

/**
 * Setup the global $post variable with information from the
 * configured page for 404 views.
 *
 * This feels ugly, but we did it anyway!
 */
function setup_post_global() {
	global $post;

	$page_id = get_page_id();

	if ( 0 === $page_id ) {
		return;
	}

	// I feel horrible for doing this, but it's okay.
	$post = get_post( $page_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
}

/**
 * Filter the allowed options handled by default in wp-admin/options.php
 * so that ours is automatically saved.
 *
 * @param array $options A list of allowed options.
 * @return array A modified list of allowed options.
 */
function filter_allowed_options( $options ) {
	$options['reading'][] = 'page_for_404';

	return $options;
}

/**
 * Add a settings field to capture the page used for 404 content.
 */
function add_settings_fields() {
	add_settings_field( 'pf4-option', 'Page for 404', __NAMESPACE__ . '\view_settings_field', 'reading', 'default' );
}

/**
 * Output the markup used to capture the page for 404 settings field.
 */
function view_settings_field() {
	?>
	<label for="page_for_404">
	<?php
	wp_dropdown_pages(
		array(
			'name'              => 'page_for_404',
			'echo'              => true,
			'show_option_none'  => esc_attr__( '&mdash; Select &mdash;' ),
			'option_none_value' => 0,
			'selected'          => absint( get_option( 'page_for_404', 0 ) ),
		)
	);
	?>
	<p class="description">The page that should be used to display content on the site's 404 page.</p>
	<?php
}

/**
 * Filter the template used for loading 404 pages when a page
 * has been configured to provide 404 content.
 *
 * @param string $template The template being included.
 * @return string A modified template string.
 */
function template_include( $template ) {
	$template_parts = explode( '/', $template );
	$template_name  = array_pop( $template_parts );

	if ( '404.php' === $template_name ) {
		$page_for_404          = get_page_id();
		$page_for_404_template = false;

		// No page for 404 is configured, leave the template untouched.
		if ( 0 === $page_for_404 ) {
			return $template;
		}

		$page_data = get_post( $page_for_404 );

		// No page data exists for the configured page for 404, leave the template untouched.
		if ( ! $page_data ) {
			return $template;
		}

		// Setup a list of possible custom templates that could be included as part
		// of the theme. This purposely excludes things like singular.php and page.php
		// as they are unlikely to be uhhh... configured with the right hacks to work.
		$templates = array(
			'page-for-404.php',
			'page-' . sanitize_key( $page_data->post_name ) . '.php',
			'page-' . $page_for_404 . '.php',
		);

		$page_for_404_template = get_query_template( 'page', $templates );

		// A page for 404 template exists in the theme, use that!
		if ( $page_for_404_template ) {
			return $page_for_404_template;
		}

		// Load the template included with this plugin, which is loosely based off of
		// the markup used for page templates in Twenty Twenty.
		return dirname( __DIR__ ) . '/templates/404.php';
	}

	return $template;
}
