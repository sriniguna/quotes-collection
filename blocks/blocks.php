<?php
/**
 * @package quotes-collection
 * @since 2.5
 */


function quotescollection_block_category( $categories, $post ) {
  return array_merge(
    $categories,
    array(
      array(
        'slug' => 'quotes-collection',
        'title' => __('Quotes Collection', 'quotes-collection'),
      ),
    )
  );
}

add_filter( 'block_categories', 'quotescollection_block_category', 5, 2 );

include_once( 'quotes/quotes.php' );
include_once( 'random-quote/random-quote.php' );


?>
