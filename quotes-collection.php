<?php
/** 
 * Plugin Name: Quotes Collection
 * Plugin URI: http://srinig.com/wordpress/plugins/quotes-collection/
 * Description: Quotes Collection plugin with Ajax powered Random Quote sidebar widget helps you collect and display your favourite quotes in your WordPress blog/website.
 * Version: 2.0.10
 * Author: Srini G
 * Author URI: http://srinig.com/
 * Text Domain: quotes-collection
 * Domain Path: /languages/
 * License: GPL2
 */

/*  Copyright 2007-2018 Srini G (email : s@srinig.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/** Prevent direct access to the file **/
defined( 'ABSPATH' ) or die( 'Access denied' );


require_once( 'inc/class-quotes-collection.php' );
require_once( 'inc/class-quotes-collection-db.php' );
require_once( 'inc/class-quotes-collection-quote.php' );
include_once( 'inc/class-quotes-collection-widget.php' );
require_once( 'inc/class-quotes-collection-shortcode.php');
if( is_admin() ) {
	require_once( 'inc/class-quotes-collection-admin-list-table.php' );
	require_once( 'inc/class-quotes-collection-admin.php' );
}

register_activation_hook( __FILE__, array( 'Quotes_Collection', 'activate' ) );
add_action( 'plugins_loaded', array( 'Quotes_Collection', 'load' ) );
add_action('widgets_init', array( 'Quotes_Collection_Widget', 'register' ) );


/**
 * The template function that generates a random quote
 *
 * @param array $args {
 *     'show_author'    => true,
 *     'show_source'    => false,
 *     'ajax_refresh'   => true,
 *     'random'         => true,
 *     'auto_refresh'   => false,
 *     'tags'           => '',
 *     'char_limit'     => 500,
 *     'echo'           => true,
 * }
 *
 * @return string containing the quote block, if 'echo' is passed in as false
 * @return bool true when quote is already echoed, ie., when echo is true
 * @return bool false on error
 *
 * @link http://wordpress.org/plugins/quotes-collection/other_notes
 */
function quotescollection_quote( $args = NULL ) {

	global $quotescollection;
	global $quotescollection_instances;


	if( NULL === $args || ( !is_string($args) && !is_array($args) ) ) {
		$args = array();
	}
	else if( is_string($args) ) { // If args are passed as a string
		// Covert the string into array
		$key_value = explode('&', $args);
		$args = array();
		foreach($key_value as $value) {
			$x = explode('=', $value);
			$args[$x[0]] = $x[1]; // $options['key'] = 'value';
		}
	}

	if( NULL === $quotescollection_instances ) {
		$quotescollection_instances = 0;
	}
	
	$quotescollection_instances++;
	$args['instance'] = "tf_quotescollection_".$quotescollection_instances;

	return $quotescollection->quote( $args );

}

/** Returns the plugin's url. If $path is passed, it's appended. */
function quotescollection_url( $path = "" ) {
	// If $path comes with a slash, remove it as the function be adding
	if( $path && '/' == $path[0]) {
		$path = substr( $path, 1 );
	}
	return plugins_url( $path, __FILE__ );
}

/** Returns the plugin's home directory. If $path is passed, it's appended. */
function quotescollection_rel_path( $path = "" ) {
	// If $path comes with a slash, remove it as we'll be adding
	if( $path && '/' == $path[0]) {
		$path = substr( $path, 1 );
	}
	return dirname( plugin_basename( __FILE__) ) . '/'. $path;
}


?>
