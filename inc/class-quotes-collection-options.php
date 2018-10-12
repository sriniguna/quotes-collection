<?php
/**
 * Options class
 *
 * @package Quotes Collection
 * @since 3.0
 */

class Quotes_Collection_Options {

  private $options;

  function __construct() {

    /* Default values */
    $this->options = array (
      'plugin_version' => Quotes_Collection::PLUGIN_VERSION,
      'refresh_link_text' => __( 'Next quote &raquo;', 'quotes-collection' ),
      'auto_refresh_max' => 20,
      'dynamic_fetch' => 'off',
      'user_level_manage_quotes' => 'edit_posts',
    );

    if( $options = get_option( 'quotescollection' ) ) {
      $this->options = array_merge( $this->options, $options );
    }

    else {
      add_option('quotescollection', $this->options);
    }
  }

  public function get_options() {
    return $this->options;
  }

  public function update_options($options) {

    if( !$options || !is_array($options) )
      return false;

    if( !empty($options['refresh_link_text']) ) {
      $this->options['refresh_link_text'] = htmlentities( $options['refresh_link_text'] );
    }

    if( is_numeric($options['auto_refresh_max'])
      && intval($options['auto_refresh_max']) >= 5
      && intval($options['auto_refresh_max']) <= 40 ) {
      $this->options['auto_refresh_max'] = $options['auto_refresh_max'];
    }

    if( isset($options['dynamic_fetch']) && $options['dynamic_fetch'] == 'on' ) {
      $this->options['dynamic_fetch'] = 'on';
    }
    else {
      $this->options['dynamic_fetch'] = 'off';
    }

    if( isset($options['user_level_manage_quotes'])
      && in_array(
        $options['user_level_manage_quotes'],
        array( 'edit_posts', 'publish_posts', 'edit_others_posts', 'manage_options')
      )
    ) {
      $this->options['user_level_manage_quotes'] = $options['user_level_manage_quotes'];
    }

    update_option('quotescollection', $this->options);

    return true;
  }

  public function get_option($option) {
    if( isset($this->options[$option]) ) {
      return $this->options[$option];
    }
    return null;
  }

  public function update_option($option, $value) {
    $this->options[$option] = $value;
    update_option('quotescollection', $this->options);
  }

}
