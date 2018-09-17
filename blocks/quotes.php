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
													'order' => array( 'type' => 'string', 'default' => 'ASC' ),
													'paging' => array( 'type' => 'boolean', 'default' => false ),
													'limit_per_page' => array( 'type' => 'number', 'default' => 10 ),
													'limit'=> array( 'type' => 'number' ),
													'backgroundColor' => array( 'type' => 'string', 'default' => '#f4f4f4' ),
													'textColor' => array( 'type' => 'string', 'default' => '#444' ),
													'textAlign' => array( 'type' => 'string', 'default' => 'left' ),
													'attributionAlign' => array( 'type' => 'string', 'default' => 'right' ),
													'showAuthor' => array( 'type' => 'boolean', 'default' => true ),
													'showSource' => array( 'type' => 'boolean', 'default' => true ),
													'className' => array( 'type' => 'string' ),
												),
	) );
}
add_action( 'init', 'quotes_block_init' );

function quotescollection_block_quotes_render( $atts = array() ) {

	$quotcoll_shortcode = new Quotes_Collection_Shortcode();
	$block_class = 'wp-block-quotes-collection-quotes';
	$block_class .= $atts['className'] ? ' '.$atts['className'] : '';
	$block_style = "";
	$blockquote_style = "";


	if( $atts['backgroundColor']
		&& ( $background_color = sanitize_hex_color( $atts['backgroundColor'] ) )
	) {
		$blockquote_style .= "background-color:".$background_color.';';
	}

	if( $atts['textColor']
		&& ( $text_color = sanitize_hex_color( $atts['textColor'] ) )
	) {
		$blockquote_style .= "color:".$text_color.';';
	}

	if( $atts['textAlign']
		&& ( in_array ( $atts['textAlign'], array( 'left', 'right', 'center' ) ) )
	)
	{
		$block_style .= "text-align:" . $atts['textAlign'] . ';';
	}

	if( $atts['attributionAlign']
		&& ( in_array ( $atts['attributionAlign'], array( 'left', 'right', 'center' ) ) )
	)
	{
		$atts['before_attribution'] = '<footer class="attribution" style="text-align:'.$atts['attributionAlign'].';">&mdash;&nbsp;';
	}

	if( $atts['showAuthor'] == false ) {
		$atts['show_author'] = 0;
	}

	if( $atts['showSource'] == false ) {
		$atts['show_source'] = 0;
	}

	if( $blockquote_style ) {
		$atts['before'] = '<blockquote class="quotescollection-quote" style="'.$blockquote_style.'">';
	}

	unset( $atts['showAuthor'], $atts['showSource'], $atts['backgroundColor'], $atts['textColor'], $atts['textAlign'], $atts['attributionAlign'], $atts['className'] );



	if( $block_style ) {
		$block_style = ' style="'.$block_style.'"';
	}

	return '<div class="' . $block_class . '"' . $block_style.'">' . $quotcoll_shortcode->do_shortcode( $atts ) . '</div>';
}
