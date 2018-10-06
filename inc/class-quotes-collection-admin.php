<?php
/**
 * Quotes Collection Admin Area
 *
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection_Admin {

	/**
	 * User levels for access to different admin pages
	 * @link http://codex.wordpress.org/Roles_and_Capabilities
	 */
	const USER_LEVEL_IMPORT_EXPORT = 'import';
	const USER_LEVEL_MANAGE_OPTIONS = 'manage_options';
	public $user_level_manage_quotes;

	/** The URLs of different admin pages **/
	public $admin_url;
	public $admin_add_new_url;
	public $admin_tags_url;
	public $admin_import_url;
	public $admin_export_url;
	public $admin_options_url;

	/** Screen IDs of admin pages. Used internally **/
	private $main_page_id;
	private $add_new_quote_page_id;
	private $quote_tags_page_id;
	private $import_page_id;
	private $export_page_id;
	private $options_page_id;
	private $notices;

	/** Flags **/
	private $quote_added = false;
	private $quote_updated = false;


	/** Constructor **/
	public function __construct() {
		$this->user_level_manage_quotes = 'edit_posts';
		if( $options = get_option( 'quotescollection' ) ) {
			if ( isset( $options['user_level_manage_quotes'] )
				&& in_array(
					$options['user_level_manage_quotes'],
					array( 'publish_posts', 'edit_others_posts', 'manage_options')
				)
			) {
				$this->user_level_manage_quotes = $options['user_level_manage_quotes'];
			}
		}

		// add_filter( 'set-screen-option', array($this, 'set_screen_options'), 10, 3 );
		add_action( 'current_screen', array($this, 'process_requests') );
		add_action( 'admin_menu', array($this, 'admin_menus') );
		add_action( 'admin_notices', array($this, 'display_notices'));
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts_and_styles' ) );
		add_filter( 'manage_quotcoll_quote_posts_columns', array( $this, 'quotes_table_head' ) );
		add_action( 'manage_quotcoll_quote_posts_custom_column', array( $this, 'quotes_table_content' ), 10, 2 );
		add_filter( 'manage_edit-quotcoll_quote_sortable_columns', array( $this, 'quotes_table_sorting') );
}


public function quotes_table_head($defaults) {
	unset($defaults['title']);
	unset($defaults['date']);

	$defaults['quotcoll_quote'] = __( 'Quote', 'quotes-collection' );
	$defaults['quotcoll_quote_author'] = __( 'Author', 'quotes-collection' );
	$defaults['quotcoll_quote_source'] = __( 'Source', 'quotes-collection' );
	$defaults['quotcoll_quote_tags'] = __( 'Tags', 'quotes-collection' );
	$defaults['date'] = __( 'Date', 'quotes-collection' );

	return $defaults;
}

public function quotes_table_content($column_name, $post_id) {
	$post = get_post($post_id);
	if( 'quotcoll_quote' == $column_name ) {
		echo $post->post_content;

	}
	if( 'quotcoll_quote_author' == $column_name ) {
		$author = get_post_meta($post_id, 'quotcoll_quote_author', true);
		echo $author;
	}

	if( 'quotcoll_quote_source' == $column_name ) {
		$source = get_post_meta($post_id, 'quotcoll_quote_source', true);
		echo '<i>'.$source.'</i>';
	}
	if( 'quotcoll_quote_tags' == $column_name ) {
		$tags_array = wp_get_object_terms( $post_id, 'quotcoll_quote_tag' );
		$tags_list = "";
		foreach($tags_array as $tag) {
			$tags_list .= '<li>'.$tag->name.'</li>';
		}
		if($tags_list) echo '<ul class="quotescollection-tags">'.$tags_list.'</ul>';
	}
}

public function quotes_table_sorting($columns) {
	$columns['quotcoll_quote'] = 'quotcoll_quote';
	$columns['quotcoll_quote_author'] = 'quotcoll_quote';
	$columns['quotcoll_quote_source'] = 'quotcoll_quote';
	return $columns;
}




	/**
	 * Function that creates admin menu items for our admin pages
	 */
	public function admin_menus() {
		$main_slug = 'edit.php?post_type=quotcoll_quote';
		$add_new_slug = 'post-new.php?post_type=quotcoll_quote';
		$tags_slug = 'edit-tags.php?taxonomy=quotcoll_quote_tag&post_type=quotcoll_quote';
		$import_slug = 'quotes-collection-import';
		$export_slug = 'quotes-collection-export';
		$options_slug = 'quotes-collection-options';

		$this->main_page_id = 'edit-'.Quotes_Collection_Post_Type_Quote::POST_TYPE;
		$this->add_new_quote_page_id = 'edit-'.Quotes_Collection_Post_Type_Quote::POST_TYPE;
		// $this->$quote_tags_page_id = 

		// Sub-menu item for 'Import Quotes' page
		$this->import_page_id =
			add_submenu_page(
				$main_slug,
				_x('Import Quotes', 'heading', 'quotes-collection'),
				_x('Import', 'submenu item text', 'quotes-collection'),
				self::USER_LEVEL_IMPORT_EXPORT,
				$import_slug,
				array($this, 'admin_page_import')
			);

		// Sub-menu item for 'Export Quotes' page
		$this->export_page_id =
			add_submenu_page(
				$main_slug,
				_x('Export Quotes', 'heading', 'quotes-collection'),
				_x('Export', 'submenu item text', 'quotes-collection'),
				self::USER_LEVEL_IMPORT_EXPORT,
				$export_slug,
				array($this, 'admin_page_export')
			);

		// Sub-menu item for the plugin options page
		$this->options_page_id =
			add_submenu_page(
				$main_slug,
				_x('Quotes Collection Options', 'heading', 'quotes-collection'),
				_x('Options', 'submenu item text', 'quotes-collection'),
				self::USER_LEVEL_MANAGE_OPTIONS,
				$options_slug,
				array($this, 'admin_page_options')
			);



		// Updating the member variables that hold URLs of different admin pages
		$this->admin_url = admin_url( $main_slug );
		$this->admin_add_new_url = admin_url( $add_new_slug );
		$this->admin_tags_url = admin_url( $tags_slug );
		$this->admin_import_url = admin_url( $main_slug . '&page=' . $import_slug );
		$this->admin_export_url = admin_url( $main_slug . '&page=' . $export_slug );
		$this->admin_options_url = admin_url( $main_slug . '&page=' . $options_slug );

		//Hooking the function that adds screen options for the quotes list page
		// add_action( "load-".$this->main_page_id, array($this, 'add_screen_options') );
	}


	/**
	 * Renders the 'Import Quotes' admin page
	 */
	public function admin_page_import() {

		$meta_box_content =
			'<p>' . __( "Browse and choose a <abbr title=\"JavaScript Object Notation\">JSON</abbr> (.json) file to upload, then click the 'Import' button.", 'quotes-collection') . '</p>'
			. '<div class="form-wrap">'
			. '<form name="" method="post" action="' . $this->admin_import_url . '"  enctype="multipart/form-data">'
				. wp_nonce_field( 'import_quotes',	'quotescollection_nonce', true, false )
				. '<div class="form-field">'
					. '<label for="import-file">'. __('Choose a file to upload:', 'quotes-collection')
					. '&nbsp;<input type="file" id="import-file" name="quotescollection-data-file" />'
					. '</label>'
				. '</div>'
				. '<div class="form-field">'
					. get_submit_button( _x('Import', 'submit button text', 'quotes-collection'), 'primary large', 'submit', false)
				. '</div>'
			. '</form>'
		. '</div>';

		$this->admin_page_header( 'import' );

		$this->pseudo_meta_box(
			'import',
			_x( 'Import Quotes', 'heading', 'quotes-collection' ),
			$meta_box_content
		);

		$this->admin_page_footer();
	}


	/**
	 * Renders the 'Export Quotes' admin page
	 */
	public function admin_page_export() {
		$meta_box_content =
			'<p>' . __("When you click the button below, a <abbr title=\"JavaScript Object Notation\">JSON</abbr> file with the entire collection of quotes will be created, that you can save to your computer.", 'quotes-collection') . '</p>'
			. '<div class="form-wrap">'
				. '<form name="" method="post" action="">'
					. wp_nonce_field( 'export_quotes',	'quotescollection_nonce', true, false )
					. '<div class="form-field">'
						. get_submit_button( _x('Export', 'submit button text', 'quotes-collection'), 'primary large', 'submit', false)
					. '</div>'
				. '</form>'
			. '</div>'
		;

		$this->admin_page_header( 'export' );

		$this->pseudo_meta_box(
			'export',
			_x( 'Export Quotes', 'heading', 'quotes-collection' ),
			$meta_box_content
		);

		$this->admin_page_footer();
	}


	/**
	 * Renders the options page
	 */
	public function admin_page_options() {

		global $quotescollection;

		$options = get_option( 'quotescollection' );

		$refresh_link_text =
			( isset( $options['refresh_link_text'] ) && $options['refresh_link_text'] ) ?
				$options['refresh_link_text']
				: $quotescollection->refresh_link_text;

			// $refresh_link_text = htmlentities( $refresh_link_text );

		$auto_refresh_max =
			( isset( $options['auto_refresh_max'] ) && $options['auto_refresh_max'] ) ?
				$options['auto_refresh_max']
				: $quotescollection->auto_refresh_max;

		$dynamic_fetch_check = ( isset( $options['dynamic_fetch'] ) && 'on' == $options['dynamic_fetch'] )?' checked="checked"':'';

		$role_select = array (
			'edit_posts' => '',
			'publish_posts' => '',
			'edit_others_posts' => '',
			'manage_options' => '',
		);

		if ( isset( $options['user_level_manage_quotes'] )
			&& in_array(
				$options['user_level_manage_quotes'],
				array( 'publish_posts', 'edit_others_posts', 'manage_options')
			)
		) {
			$role_select[$options['user_level_manage_quotes']] = ' selected="selected"';
		} else {
			$role_select['edit_posts'] = ' selected="selected"';
		}


		$meta_box_content =
			'<div class="form-wrap">'
				. '<form name="quotescollection_options" method="post" action="'.$this->admin_options_url.'">'
					. wp_nonce_field( 'options', 'quotescollection_nonce', true, false )
					. '<div class="form-field">'
						. '<label for="refresh_link_text">' . __("Refresh link text", 'quotes-collection') . '</label>'
						. '<input type="text" name="refresh_link_text" id="refresh_link_text" value="'.$refresh_link_text.'" style="width: 10em;" />'
					. '</div>'
					. '<div class="form-field">'
						. '<label for="auto_refresh_max">' . __('Maximum number of iterations for auto-refresh', 'quotes-collection') . '</label>'
						. '<input type="number" name="auto_refresh_max" id="auto_refresh_max" value="'.$auto_refresh_max.'" max="40" min="5" step="1" style="width: 3em;" />'
					. '</div>'
					. '<div class="form-field">'
						. '<label for="dynamic_fetch">' . __('Dynamically fetch the first random quote in widget?', 'quotes-collection')
						. '&nbsp;<input type="checkbox" name="dynamic_fetch" id="dynamic_fetch"'.$dynamic_fetch_check.' />'
						. '</label>'
						. '<p>'. __("Check this if your site is cached and the 'random quote' widget always shows a particular quote as the initial quote.", 'quotes-collection').'</p>'
					. '</div>'
					. '<div class="form-field">'
						. '<label for="user_level_manage_quotes">' . __('Minimum user role required to add and manage quotes', 'quotes-collection') . '</label>'
						. '<select name="user_level_manage_quotes" id="user_level_manage_quotes">'
							. '<option value="edit_posts"' . $role_select['edit_posts'] . '>' . __('Contributor', 'quotes-colletion') . '</option>'
							. '<option value="publish_posts"' . $role_select['publish_posts'] . '>' . __('Author', 'quotes-colletion') . '</option>'
							. '<option value="edit_others_posts"' . $role_select['edit_others_posts'] . '>' . __('Editor', 'quotes-colletion') . '</option>'
							. '<option value="manage_options"' . $role_select['manage_options'] . '>' . __('Administrator', 'quotes-colletion') . '</option>'
						. '</select>'
					. '</div>'

					. get_submit_button( _x('Update Options', 'submit button text', 'quotes-collection'), 'primary large', 'submit', false)
				. '</form>'
			. '</div>'
		;

		$this->admin_page_header( 'options' );

		$this->pseudo_meta_box(
			'options',
			_x( 'Quotes Collection Options', 'heading', 'quotes-collection' ),
			$meta_box_content
		);

		$this->admin_page_footer();

	}



	private function admin_page_header( $active_page = "quotes-list" ) {
		?>
		<div id="quotescollection-admin-page" class="wrap">
		<header>
		<h1 id="quotescollection-title">Quotes Collection</h1>
		<h2 id="quotescollection-nav" class="nav-tab-wrapper">

			<?php if( current_user_can( $this->user_level_manage_quotes ) ): ?>
				<a href="<?php echo $this->admin_url; ?>" class="nav-tab<?php echo ( 'quotes-list' == $active_page )? ' nav-tab-active' : '';?>">
					<?php _e( 'All Quotes', 'quotes-collection' ); ?>
				</a>
				<?php if( 'edit-quote' == $active_page ): ?>
					<a href="#" class="nav-tab nav-tab-active">
						<?php _ex( 'Edit Quote', 'submenu item text', 'quotes-collection' ); ?>
					</a>
				<?php endif; ?>
				<a href="<?php echo $this->admin_add_new_url; ?>" class="nav-tab<?php echo ( 'add-new' == $active_page )? ' nav-tab-active' : '';?>">
					<?php _ex( 'Add New', 'submenu item text', 'quotes-collection' ); ?>
				</a>
			<?php endif; ?>

			<?php if( current_user_can( self::USER_LEVEL_IMPORT_EXPORT ) ): ?>
				<a href="<?php echo $this->admin_import_url; ?>" class="nav-tab<?php echo ( 'import' == $active_page )? ' nav-tab-active' : '';?>">
					<?php _ex( 'Import', 'submenu item text', 'quotes-collection' ); ?>
				</a>
				<a href="<?php echo $this->admin_export_url; ?>" class="nav-tab<?php echo ( 'export' == $active_page )? ' nav-tab-active' : '';?>">
					<?php _ex( 'Export', 'submenu item text', 'quotes-collection' ); ?>
				</a>
			<?php endif; ?>

			<?php if( current_user_can( self::USER_LEVEL_MANAGE_OPTIONS ) ): ?>
				<a href="<?php echo $this->admin_options_url; ?>" class="nav-tab<?php echo ( 'options' == $active_page )? ' nav-tab-active' : '';?>">
					<?php _ex( 'Options', 'submenu item text', 'quotes-collection' ); ?>
				</a>
			<?php endif; ?>

		</h2>
		</header>
		<main>
		<?php

	}


	private function admin_page_footer() {
		?></main></div><?php
	}


	/**
	 * Mocking the meta box!
	 *
	 * @param string $id         id for the enclosing 'div.postbox' element
	 * @param string $title      Title to be displayed (3rd level header)
	 * @param string $content    The content that goes inside the 'meta box'
	 **/
	private function pseudo_meta_box( $id, $title = "", $content = "") {
		?>
			<div id="poststuff" class="wrap meta-box-holder">
				<div id="normal-sortables" class="meta-box-sortables">
					<div id="<?php echo $id; ?>" class="postbox " >
						<?php if( $title ) : ?>
						<h3 class="hndle" style="cursor:default;"><span><?php echo $title; ?></span></h3>
						<?php endif; ?>
						<div class="inside">
							<?php echo $content; ?>
						</div>
					</div>
				</div>
			</div>
		<?php
	}



	/**
	 * To process requests to add/update/delete/import/export quote/s.
	 *
	 * Hooked to the 'current_screen' action, ie., triggered immediately after
	 * the necessary elements to identify a screen are set up. So that the
	 * current screen ID can be checked with our quotescollection admin page IDs
	 * before processing requests. Also to ensure requests are processed before
	 * any headers are sent, and well before admin notices are displayed.
	 *
	 */
	public function process_requests() {

		// Proceed only if the current screen is one of the plugin's admin pages
		$screen = get_current_screen();
		if(
			$screen->id != $this->main_page_id
			&& $screen->id != $this->add_new_quote_page_id
			&& $screen->id != $this->import_page_id
			&& $screen->id != $this->export_page_id
			&& $screen->id != $this->options_page_id
			) {
			return;
		}

		global $quotescollection_db;

		if(isset($_REQUEST['submit'])) {
			if(
				$_REQUEST['submit'] == _x('Add Quote', 'submit button text', 'quotes-collection')
				&& check_admin_referer( 'add_quote', 'quotescollection_nonce' ) // Check nonce
				) {
				if( !isset( $_REQUEST['quote'] ) || false == trim( $_REQUEST['quote'] ) ) {
					$this->notices = '<div class="error"><p>'.__("The quote field cannot be blank. Fill up the quote field and try again.", 'quotes-collection').'</p></div>';
				}
				else if( $result = $quotescollection_db->put_quote($_REQUEST) ) {
					$this->notices = '<div class="updated"><p>'.__('Quote added', 'quotes-collection').'</p></div>';
					$this->quote_added = true; // set the flag
				}
				else {
					$this->notices = '<div class="error"><p>'.__('Error adding quote', 'quotes-collection').'</p></div>';
				}

			}
			else if(
				$_REQUEST['submit'] == _x('Save Changes', 'submit button text', 'quotes-collection')
				&& check_admin_referer( 'save_changes_'.$_REQUEST['quote_id'], 'quotescollection_nonce' )
				) {
				if( !isset( $_REQUEST['quote'] ) || false == trim( $_REQUEST['quote'] ) ) {
					$this->notices = '<div class="error"><p>'.__("The quote field cannot be blank. Fill up the quote field and try again.", 'quotes-collection').'</p></div>';
				}
				else if($result = $quotescollection_db->update_quote($_REQUEST)) {
					$this->notices = '<div class="updated"><p>'.__('Changes saved', 'quotes-collection').'</p></div>';
					$this->quote_updated = true; // set the flag
				}
				else
					$this->notices = '<div class="error"><p>'.__('Error updating quote', 'quotes-collection').'</p></div>';

			}
			else if(
				$_REQUEST['submit'] == _x('Import', 'submit button text', 'quotes-collection')
				&& check_admin_referer( 'import_quotes', 'quotescollection_nonce' )
				) {
				$this->process_import();
			}
			else if(
				$_REQUEST['submit'] == _x('Export', 'submit button text', 'quotes-collection')
				&& check_admin_referer( 'export_quotes', 'quotescollection_nonce' )
				) {
				$this->process_export();
			}
			else if(
				$_REQUEST['submit'] == _x('Update Options', 'submit button text', 'quotes-collection')
				&& check_admin_referer( 'options', 'quotescollection_nonce' )
				) {
				$this->update_options();
			}

		}
		else if( isset( $_REQUEST['action'] ) || isset( $_REQUEST['action2'] ) ) {
			if(
				'delete' == $_REQUEST['action']
				&& is_numeric( $_REQUEST['id'] )
				&& check_admin_referer( 'delete_quote_'.$_REQUEST['id'], 'quotescollection_nonce' )
				) {
				if( $result = $quotescollection_db->delete_quote($_REQUEST['id']) )
					$this->notices = '<div class="updated"><p>'.__('Quote deleted', 'quotes-collection').'</p></div>';
				else
					$this->notices = '<div class="error"><p>'.__('Error deleting quote', 'quotes-collection').'</p></div>';
			}
			else if( ( 'bulk_delete' == $_REQUEST['action'] || ( isset( $_REQUEST['action2'] ) && 'bulk_delete' == $_REQUEST['action2'] ) )
				&& check_admin_referer('bulk-quote_entries') ) {
				if( !isset( $_REQUEST['bulkcheck'] ) ) {
					$this->notices = '<div class="error"><p>'.__('No item selected', 'quotes-collection').'</p></div>';
				}
				else if( $result = $quotescollection_db->delete_quotes( $_REQUEST['bulkcheck'] ) ) {
					$this->notices = '<div class="updated"><p>'
						. sprintf(
							_n(
								/* translators: $s: The number of quotes deleted */
								'%s quote deleted',
								'%s quotes deleted',
								$result,
								'quotes-collection'
							),
							number_format_i18n($result) )
						.'</p></div>';
				}
				else {
					$this->notices = '<div class="error"><p>'.__('Error deleting quotes', 'quotes-collection').'</p></div>';
				}
			}
			else if( ( 'make_public' == $_REQUEST['action'] || ( isset( $_REQUEST['action2'] ) && 'make_public' == $_REQUEST['action2'] ) )
				&& check_admin_referer('bulk-quote_entries') ) {
				if( !isset( $_REQUEST['bulkcheck'] ) ) {
					$this->notices = '<div class="error"><p>'.__('No item selected', 'quotes-collection').'</p></div>';
				}
				else if( $result = $quotescollection_db->change_visibility($_REQUEST['bulkcheck'], 'yes') ) {
					$this->notices = '<div class="updated"><p>'
						. sprintf(
							_n(
								/* translators: $s: The number of quotes made public */
								'%s quote made public',
								'%s quotes made public',
								$result,
								'quotes-collection'
							), number_format_i18n($result) )
						.'</p></div>';
				}
				else
					$this->notices = '<div class="error"><p>'.__('Error. Privacy status not changed.', 'quotes-collection').'</p></div>';
			}
			else if( ( 'keep_private' == $_REQUEST['action'] || ( isset( $_REQUEST['action2'] ) && 'keep_private' == $_REQUEST['action2'] ) )
				&& check_admin_referer('bulk-quote_entries') ) {
				if( !isset( $_REQUEST['bulkcheck'] ) ) {
					$this->notices = '<div class="error"><p>'.__('No item selected', 'quotes-collection').'</p></div>';
				}
				else if( $result = $quotescollection_db->change_visibility($_REQUEST['bulkcheck'], 'no') ) {
					$this->notices = '<div class="updated"><p>'
						. sprintf(
							_n(
								/* translators: $s: The number of quotes kept private */
								'%s quote kept private',
								'%s quotes kept private',
								$result,
								'quotes-collection'
							), number_format_i18n($result) )
						.'</p></div>';
				}
				else
					$this->notices = '<div class="error"><p>'.__('Error. Privacy status not changed.', 'quotes-collection').'</p></div>';
			}
		}
	}



	private function process_import() {

		if( $_FILES['quotescollection-data-file']['error'] == UPLOAD_ERR_NO_FILE
			|| !is_uploaded_file( $_FILES['quotescollection-data-file']['tmp_name'] )
			) {
			$this->notices = '<div class="error"><p>' . __( "Please choose a file to upload before you hit the 'Import' button", 'quotes-collection' ) . '</p></div>';
			return;
		}

		$allowed_extensions = array( 'json', 'JSON' );
		if( ! in_array( pathinfo( $_FILES['quotescollection-data-file']['name'], PATHINFO_EXTENSION ), $allowed_extensions ) ) {
			$this->notices = '<div class="error"><p>' . __( "Invalid file format", 'quotes-collection' ) . '</p></div>';
			return;
		}

		if( $_FILES['quotescollection-data-file']['error'] == UPLOAD_ERR_INI_SIZE
			|| $_FILES['quotescollection-data-file']['error'] == UPLOAD_ERR_FORM_SIZE ) {
			$this->notices = '<div class="error"><p>' . __( "The file you uploaded is too big. Import failed.", 'quotes-collection' ) . '</p></div>';
			return;
		}

		if ( $_FILES['quotescollection-data-file']['error'] == UPLOAD_ERR_OK               //checks for errors
			&& is_uploaded_file( $_FILES['quotescollection-data-file']['tmp_name'] ) ) {   //checks that file is uploaded

			if( ! ( $json_data = file_get_contents( $_FILES['quotescollection-data-file']['tmp_name'] ) ) ) {
				$this->notices = '<div class="error"><p>' . __( "The file uploaded was empty", 'quotes-collection' ) . '</p></div>';
				return;
			}

			if( is_null( $quote_entries = json_decode( $json_data, true ) ) ) {
				$this->notices = '<div class="error"><p>' . __( "Error in JSON file", 'quotes-collection' ) . '</p></div>';
				return;
			}

			global $quotescollection_db;
			$result = $quotescollection_db->put_quotes($quote_entries);

			if(FALSE === $result){
				$this->notices = '<div class="error"><p>' . __( "Import failed. Please try again.", 'quotes-collection' ) . '</p></div>';
			}
			else if( 0 === $result) {
				$this->notices = '<div class="updated"><p>' . __( "No quotes imported", 'quotes-collection' ) . '</p></div>';
			}
			else {
				$this->notices = '<div class="updated"><p>'
					. sprintf( _n(
						/* translators: $s: The number of quotes imported */
						'%s quote imported',
						'%s quotes imported',
						$result,
						'quotes-collection' ), number_format_i18n($result) )
					. '</p></div>';
			}

			return;
		}
		else {
			$this->notices = '<div class="error"><p>' . __( "Import failed. Please try again.", 'quotes-collection' ) . '</p></div>';
			return;
		}


	}



	private function process_export() {
		global $quotescollection_db;
		$args = array();
		if( ! ( $quote_entries = $quotescollection_db->get_quotes_array($args) ) ) {
			$this->notices = '<div class="error"><p>' . __( "Nothing to export", 'quotes-collection' ) . '</p></div>';
			return;
		}
		foreach( $quote_entries as $index => $quote_entry ) {
			unset( $quote_entry['quote_id'] );
			unset( $quote_entry['time_added'] );
			unset( $quote_entry['time_updated'] );
			$quote_entries[$index] = $quote_entry;
		}
		$file_output = json_encode($quote_entries, JSON_PRETTY_PRINT);
		header('Content-Type: text/json');
		header('Content-Disposition: attachment; filename="quotes-collection-'.date('Ymd_His').'.json"');
		echo $file_output;
		exit;
	}



	private function update_options() {
		$options = $options_old = get_option('quotescollection');

		if( !empty($_REQUEST['refresh_link_text']) ) {
			$options['refresh_link_text'] = htmlentities( $_REQUEST['refresh_link_text'] );
		}

		if( is_numeric($_REQUEST['auto_refresh_max'])
			&& intval($_REQUEST['auto_refresh_max']) >= 5
			&& intval($_REQUEST['auto_refresh_max']) <= 40 ) {
			$options['auto_refresh_max'] = $_REQUEST['auto_refresh_max'];
		}

		if( isset($_REQUEST['dynamic_fetch']) && $_REQUEST['dynamic_fetch'] == 'on' ) {
			$options['dynamic_fetch'] = 'on';
		}
		else if( isset($options['dynamic_fetch']) ) {
			unset($options['dynamic_fetch']);
		}

		if( isset($_REQUEST['user_level_manage_quotes'])
			&& in_array(
				$_REQUEST['user_level_manage_quotes'],
				array( 'edit_posts', 'publish_posts', 'edit_others_posts', 'manage_options')
			)
		) {
			$options['user_level_manage_quotes'] = $_REQUEST['user_level_manage_quotes'];
		}

		if( $options === $options_old ) {
			$this->notices = '<div class="updated"><p>' . __( 'No change in values. Options not updated.', 'quotes-collection') . '</p></div>';
			return;
		}

		if( update_option('quotescollection', $options) ) {
			$this->notices = '<div class="updated"><p>' . __( 'Options updated', 'quotes-collection') . '</p></div>';
			return;
		}
		$this->notices = '<div class="error"><p>' . __( 'Options not updated', 'quotes-collection' ) . '</p></div>';
		return;
	}



	/** Outputs the admin notices **/
	public function display_notices() {
		echo $this->notices;
	}



	public function load_scripts_and_styles() {
		// Load the confirm box scripts and styles only if on the quotes list page.
		$screen = get_current_screen();

		// And load the quotes-collection-admin.css if current page is any of the plugin's admin pages
		if(	$screen->id == $this->main_page_id
			|| $screen->id == $this->add_new_quote_page_id
			|| $screen->id == $this->import_page_id
			|| $screen->id == $this->export_page_id
			|| $screen->id == $this->options_page_id
			) {
			wp_enqueue_style(
				'quotescollection-admin',
				quotescollection_url( 'css/quotes-collection-admin.css' ),
				array(),
				Quotes_Collection::PLUGIN_VERSION
			);
		}
	}

}
?>
