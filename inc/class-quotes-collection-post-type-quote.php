<?php
/**
 * Custom Post Type 'quotcoll_quote'
 *
 * @package Quotes Collection
 * @since 3.0
 */

class Quotes_Collection_Post_Type_Quote {

	const POST_TYPE = 'quotcoll_quote';
	const TAXONOMY_TAG = 'quotcoll_quote_tag';
	const POST_META_AUTHOR = 'quotcoll_quote_author';
	const POST_META_SOURCE = 'quotcoll_quote_author';
	const POST_META_QUOTE_ID_OLD = 'quotcoll_quote_old_id';


	function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'save_post', array( $this, 'save_author_metabox' ), 1, 2);
		add_action( 'save_post', array( $this, 'save_source_metabox' ), 1, 2);
	}

	/** Register the post types **/
	public function register_post_type() {
		register_post_type( self::POST_TYPE,
			array(
				'labels' => array(
					'name' 					=> __('Quotes Collection', 'quotes-collection'),
					'singular_name' 		=> __('Quote', 'quotes-collection'),
					'menu_name' 			=> __('QC CPT', 'quotes-collection'),
					'all_items'				=> __('All Quotes', 'quotes-collection'),
					'add_new_item'			=> __('Add New Quote', 'quotes-collection'),
					'edit_item'				=> __('Edit Quote', 'quotes-collection'),
					'new_item'				=> __('New Quote', 'quotes-collection'),
					'view_item'				=> __('View Quote', 'quotes-collection'),
					'search_items'			=> __('Search Quotes', 'quotes-collection'),
					'not_found'				=> __('No quotes found', 'quotes-collection'),
					'not_found_in_trash'	=> __('No quotes found in Trash', 'quotes-collection'),
				),
				'public' => false,
				'has_archive' => false,
				'menu_icon' => 'dashicons-testimonial',
				'supports' => array( 'editor' ),
				'show_ui' => true,
				'show_in_menu' => true,
				'register_meta_box_cb' => array($this, 'register_meta_boxes'),
			)
		);

		register_taxonomy( self::TAXONOMY_TAG, self::POST_TYPE,
			array(
				'hierarchical' => false,
				'labels' => array(
					'name' => __('Quote Tags', 'quotes-collection'),
					'singular_name' => __('Tag', 'quotes-collection'),
				),
				'public' => false,
				'show_ui' => true,
			)
		);
	}

	public function register_meta_boxes() {
		add_meta_box('quotcoll_quote_author', __('Author', 'quotes-collection'), array( $this, 'render_author_metabox' ), 'quotcoll_quote');
		add_meta_box('quotcoll_quote_source', __('Source', 'quotes-collection'), array( $this, 'render_source_metabox' ), 'quotcoll_quote');
	}

	public function render_author_metabox($post) {
		wp_nonce_field( 'quotcoll_author_metabox', 'quotcoll_author_metabox_nonce' );

		$value = get_post_meta($post->ID, self::POST_META_AUTHOR, true);

		echo '<input type="text" id="quotcoll-quote-author" name="quotcoll-quote-author" value="' . esc_attr( $value ) . '" size="25" />';

	}

	public function render_source_metabox($post) {
		wp_nonce_field( 'quotcoll_source_metabox', 'quotcoll_source_metabox_nonce' );

		$value = get_post_meta($post->ID, self::POST_META_SOURCE, true);

		echo '<input type="text" id="quotcoll-quote-source" name="quotcoll-quote-source" value="' . esc_attr( $value ) . '" size="25" />';

	}

	public function save_author_metabox($post_id, $post) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['quotcoll_author_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['quotcoll_author_metabox_nonce'], 'quotcoll_author_metabox' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		// if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		// 	return;
		// }

		// Check the user's permissions.
		// if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		// 	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		// 		return;
		// 	}

		// } else {

			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				return $post->ID;
			}
		// }

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST['quotcoll-quote-author'] ) ) {
			return;
		}

		// Sanitize user input.
		$author = sanitize_text_field( $_POST['quotcoll-quote-author'] );

		// Update the meta field in the database.
		update_post_meta( $post->ID, self::POST_META_AUTHOR, $author );
	}

	public function save_source_metabox($post_id, $post) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['quotcoll_source_metabox_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['quotcoll_source_metabox_nonce'], 'quotcoll_source_metabox' ) ) {
			return;
		}


		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return $post->ID;
		}

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST['quotcoll-quote-source'] ) ) {
			return;
		}

		// Sanitize user input.
		$source = sanitize_text_field( $_POST['quotcoll-quote-source'] );

		// Update the meta field in the database.
		update_post_meta( $post->ID, self::POST_META_SOURCE, $source );
	}



}

?>
