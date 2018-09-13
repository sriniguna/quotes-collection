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
function quotes_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'quotes/index.js';
	wp_register_script(
		'quotes-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		filemtime( "$dir/$index_js" )
	);

	$editor_css = 'quotes/editor.css';
	wp_register_style(
		'quotes-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	$style_css = 'quotes/style.css';
	wp_register_style(
		'quotes-block',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'quotes-collection/quotes', array(
		'editor_script' => 'quotes-block-editor',
		'editor_style'  => 'quotes-block-editor',
		'style'         => 'quotes-block',
		'render_callback' => 'quotescollection_block_quotes_render',
		'attributes'		=> array(
													'author' => array( 'type' => 'string' ),
													'source' => array( 'type' => 'string' ),
													'tags' => array( 'type' => 'string' ),
													'orderby' => array( 'type' => 'string' ),
													'order' => array( 'type' => 'string' ),
													'paging' => array( 'type' => 'boolean', 'default' => false ),
													'limit_per_page' => array( 'type' => 'number', 'default' => 10 ),
													'limit'=> array( 'type' => 'number' ),
													'backgroundColor' => array( 'type' => 'string' ),
													'textColor' => array( 'type' => 'string' ),
												),
	) );
}
add_action( 'init', 'quotes_block_init' );

function quotescollection_block_quotes_render($atts) {

	$shortcode = '[quotcoll';
	if($atts) {
		$shortcode .= $atts["author"] ? ' author="'.$atts["author"].'"' : '';
		$shortcode .= $atts["source"] ? ' source="'.$atts["source"].'"' : '';
		$shortcode .= $atts["tags"] ? ' tags="'.$atts["tags"].'"' : '';
		$shortcode .= $atts["orderby"] ? ' orderby="'.$atts["orderby"].'"' : '';
		$shortcode .= $atts["order"] ? ' order="'.$atts["order"].'"' : '';
		$shortcode .= $atts["paging"] ? ' paging="'.$atts["paging"].'"' : '';
		$shortcode .= $atts["limit_per_page"] ? ' limit_per_page="'.$atts["limit_per_page"].'"' : '';
		$shortcode .= $atts["limit"] ? ' limit="'.$atts["limit"].'"' : '';
	}
	$shortcode .=']';

	return do_shortcode( $shortcode	);
}
