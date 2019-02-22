<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package quotes-collection
 * @since 2.5
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

	$index_js = 'index.js';
	wp_register_script(
		'quotescollection-block-random-quote-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-components',
			'wp-editor',
		),
		filemtime( "$dir/$index_js" )
	);
	wp_set_script_translations('quotescollection-block-random-quote-editor', 'quotes-collection');

	$style_css = 'style.css';
	wp_register_style(
		'quotescollection-block-random-quote',
		plugins_url( $style_css, __FILE__ ),
		array(),
		filemtime( "$dir/$style_css" )
	);

	register_block_type( 'quotes-collection/random-quote', array(
		'editor_script' => 'quotescollection-block-random-quote-editor',
		'style'         => 'quotescollection-block-random-quote',
		'render_callback' => 'quotescollection_block_random_quote_render',
		'attributes' =>
			array(
				'showAuthor' => array( 'type' => 'boolean', 'default' => true ),
				'showSource' => array( 'type' => 'boolean', 'default' => true ),
				'randomRefresh' => array( 'type' => 'boolean', 'default' => true ),
				'autoRefresh' => array( 'type' => 'boolean', 'default' => true ),
				'refreshInterval' => array( 'type' => 'number', 'default' => 5 ),
				'charLimit' => array( 'type' => 'number', 'default' => 500 ),
				'tags' => array( 'type' => 'string' ),
				'backgroundColor' => array( 'type' => 'string', 'default' => '#f4f4f4' ),
				'textColor' => array( 'type' => 'string', 'default' => '#444' ),
				'textAlign' => array( 'type' => 'string', 'default' => 'left' ),
				'attributionAlign' => array( 'type' => 'string', 'default' => 'right' ),
				'fixedHeight' => array( 'type' => 'boolean', 'default' => false),
				'height' => array( 'type' => 'number', 'default' => 200 ),
				'className' => array( 'type' => 'string' ),
				'editor_render' => array( 'type' => 'boolean', 'default' => is_admin())
			),
	) );
}
add_action( 'init', 'quotescollection_block_random_quote_init' );

/**
 * Function to render the block in the editor as well as the front end.
 *
 * @param array $atts The attributes that were set on the block.
 */
function quotescollection_block_random_quote_render( $atts = array() ) {
	$block_class = 'wp-block-quotes-collection-random-quote';
	$block_class .= isset( $atts['className'] ) && $atts['className'] ? ' '.$atts['className'] : '';
	$block_style = "";
	$blockquote_style = "";
	$attribution_style = "";
	$nav_next = "";

	if( $atts['showAuthor'] == false ) {
		$atts['show_author'] = 0;
	}

	if( $atts['showSource'] == false ) {
		$atts['show_source'] = 0;
	}

	if( $atts['randomRefresh'] == false ) {
		$atts['random'] = 0;
	}

	if( isset($atts['autoRefresh']) && $atts['autoRefresh'] ){
		if( !isset($atts['refreshInterval'] )
			|| !is_numeric($atts['refreshInterval'])
			|| !$atts['refreshInterval']
		) {
			$atts['auto_refresh'] = 5;
		} else {
			$atts['auto_refresh'] = $atts['refreshInterval'];
		}
	} else {
		if( $atts['editor_render'] ) {
			$refresh_link_text = __("Next quote &raquo;", 'quotes-collection');
			if( $options = get_option( 'quotescollection' ) ) {
				if( isset($options['refresh_link_text']) && $options['refresh_link_text'] ) {
					$refresh_link_text = html_entity_decode( $options['refresh_link_text'] );
				}
			}
			$nav_next = '<div class="navigation"><div class="nav-next"><a class="next-quote-link" style="cursor:pointer;">' . $refresh_link_text . '</a></div></div>';
		}
	}

	if( is_numeric( $atts['charLimit'] ) && $atts['charLimit'] > 0 ) {
		$atts['char_limit'] = $atts['charLimit'];
	}


	if( $atts['backgroundColor']
		&& ( $background_color = sanitize_hex_color( $atts['backgroundColor'] ) )
	) {
		$block_style .= 'background-color:'.$background_color.';';
		$blockquote_style .= 'background-color:'.$background_color.';';
	}

	if( $atts['textColor']
		&& ( $text_color = sanitize_hex_color( $atts['textColor'] ) )
	) {
		$block_style .= 'color:'.$text_color.';';
		$blockquote_style .= 'color:'.$text_color.';';
	}

	if( $atts['textAlign']
		&& ( in_array ( $atts['textAlign'], array( 'left', 'right', 'center' ) ) )
	)
	{
		$block_style .= 'text-align:' . $atts['textAlign'] . ';';
	}

	if( $atts['attributionAlign']
		&& ( in_array ( $atts['attributionAlign'], array( 'left', 'right', 'center' ) ) )
	)
	{
		$attribution_style .= 'text-align:' . $atts['attributionAlign'] . ';';
	}

	if( $atts['fixedHeight'] && is_numeric( $atts['height'] ) ) {
		$block_style .= 'height:' . $atts['height'] . 'px; overflow: hidden;';
	}


	$atts['echo'] = 0;

	unset(
		$atts['showAuthor'],
		$atts['showSource'],
		$atts['randomRefresh'],
		$atts['autoRefresh'],
		$atts['refreshInterval'],
		$atts['charLimit'],
		$atts['backgroundColor'],
		$atts['textColor'],
		$atts['textAlign'],
		$atts['attributionAlign'],
		$atts['className']
	);

	if( $blockquote_style ) {
		$blockquote_style = ' style="'.$blockquote_style.'"';
	}
	if( $attribution_style ) {
		$attribution_style = ' style="'.$attribution_style.'"';
	}
	$atts['before'] = '<blockquote class="quotescollection-quote"' . $blockquote_style.'>';
	$atts['after'] = '</blockquote>';
	$atts['before_attribution'] = '<footer class="attribution"' . $attribution_style . '>&mdash;&nbsp;';
	$atts['after_attribution'] = '</footer>';

	if( $block_style ) {
		$block_style = ' style="'.$block_style.'"';
	}


	return
		'<div class="' . $block_class . '"' . $block_style.'">'.
		quotescollection_quote( $atts ).$nav_next.
		'</div>';

}
