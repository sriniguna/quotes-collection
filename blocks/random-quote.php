<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package quotes-collection
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type/#enqueuing-block-scripts
 */
function quotescollection_block_random_quote_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'random-quote/index.js';
	wp_register_script(
		'quotescollection-block-random-quote-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$index_js" )
	);

	$editor_css = 'random-quote/editor.css';
	wp_register_style(
		'quotescollection-block-random-quote-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'random-quote/style.css';
	wp_register_style(
		'quotescollection-block-random-quote',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'quotes-collection/random-quote', array(
		'editor_script' => 'quotescollection-block-random-quote-editor',
		'editor_style'  => 'quotescollection-block-random-quote-editor',
		'style'         => 'quotescollection-block-random-quote',
	) );
}
add_action( 'init', 'quotescollection_block_random_quote_init' );
