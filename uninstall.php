<?php
/**
 * @package Quotes Collection
 * @since 2.0
 */

//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$option_name = 'quotescollection';
$widget_option_name = 'widget_quotescollection';

delete_option( $option_name );
delete_option( $widget_option_name );

// For site options in multisite
delete_site_option( $option_name );  
delete_site_option( $widget_option_name );  

//drop the db table
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quotescollection" );


?>