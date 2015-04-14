<?php
/**
 * The Quotes List Table
 * 
 * Generates the list of quotes to be displayed in the plugin's main admin page.
 *
 * @package Quotes Collection
 * @since 2.0
 */

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class Quotes_Collection_Admin_List_Table extends WP_List_Table {

	public $total_items;
	public $total_list_items;
	public $filtered;

	function __construct(){
		global $status, $page;
				
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'quote_entry',     //singular name of the listed records
			'plural'    => 'quote_entries',   //plural name of the listed records
			'ajax'      => false              //does this table support ajax?
		) );
		
	}

	function column_default($item, $column_name){
		switch($column_name){
			case 'quote_id':
			case 'author':
			case 'source':
			case 'tags':
			case 'public':
			case 'time_added':
				return $item[$column_name];
			default:
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}


	function column_quote($item) {
		global $quotescollection_admin;

		$edit_link = wp_nonce_url(
			$quotescollection_admin->admin_url . '&action=edit&id=' . $item['quote_id'],
			'edit_quote_' . $item['quote_id'],
			'quotescollection_nonce'
			);
		$delete_link = wp_nonce_url(
			$quotescollection_admin->admin_url . '&action=delete&id=' . $item['quote_id'],
			'delete_quote_' . $item['quote_id'],
			'quotescollection_nonce'
			);

		$actions = array(
			'edit'      => '<a href="'.$edit_link.'">' . __('Edit', 'quotes-collection') . '</a>',
			'delete'    => '<a href="'.$delete_link.'">' . __('Delete', 'quotes-collection') . '</a>',
		);
		
		//Return the title contents
		return sprintf('<div>%1$s</div>%2$s',
			/*$1%s*/ $item['quote'],
			/*$2%s*/ $this->row_actions($actions)
		);

	}

	function column_source( $item ) {
		$output = "";
		if( $item['author'] ) {
			$output = $item['author'];
		}
		if( $item['source'] ) {
			if($output) {
				$output .= ', ';
			}
			$output .= '<i>'.$item['source'].'</i>';
		}
		return $output;
	}

	function column_tags( $item ) {
		$tags = '';
		if( $item['tags'] ) {
			$tags_array = explode( ',', $item['tags'] );
			$tags = '<ul class="quotescollection-tags">';
			foreach( $tags_array as $key => $tag ) {
				$tags .= '<li class="quotescollection-tag">';
				$tags .= stripslashes( trim( $tag ) );
				$tags .= '</li>';
			}
			$tags .= '</ul>';
		}
		return $tags;
	}

	function column_date( $item ) {
		$date = date_create( $item['time_added'] );
		$abbr_title = date_format( $date, _x('Y/m/d h:i:s A', 'date and time format', 'quotes-collection') );
		$date_display = date_format( $date, _x( 'Y/m/d', 'date format', 'quotes-collection') );
		$display = '<div class="date"><abbr title="'.$abbr_title.'">'.$date_display.'</abbr></div>';
		$public_display = ($item['public'] == 'no')?__('Private', 'quotes-collection'):__('Public', 'quotes-collection');
		$display .= '<div class="public">'.$public_display.'</div>';
		return $display;
	}

	function column_cb($item){
		return sprintf(
			'<input type="checkbox" name="bulkcheck[]" value="%s" />',
			/*%s*/ $item['quote_id']                //The value of the checkbox should be the record's id
		);
	}

	function get_columns(){
		$columns = array(
			'cb'         => '<input type="checkbox" />', //Render a checkbox instead of text
			'quote_id'	 => __('ID', 'quotes-collection'),
			'quote'      => __('Quote', 'quotes-collection'),
			'source'     => __('Author', 'quotes-collection').', '.__('Source', 'quotes-collection'),
			'tags'       => __('Tags', 'quotes-collection'),
			'date'       => __('Date', 'quotes-collection')
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'quote_id'   => array('quote_id', false),     
			'quote'      => array('quote', false),
			'date'       => array('time_added', false)
		);
		return $sortable_columns;
	}

	function get_bulk_actions() {
		$actions = array(
			'bulk_delete'   => __('Delete', 'quotes-collection'),
			'make_public'   => __('Make public', 'quotes-collection'),
			'keep_private'  => __('Keep private', 'quotes-collection'),
		);
		return $actions;
	}



	function output_format( $items, $highlight = "" ) {
		foreach( $items as $key => $quote_data ) {
			$quote = new Quotes_Collection_Quote( $quote_data );
			$quote->prepare_data();
			$quote_array = (array) $quote;
			if($highlight) {
				$quote_array = preg_replace( 
					"/$highlight/i", 
					"<span class='highlight'>\$0</span>", 
					$quote_array
				);
				unset($quote_array['quote_id']);
				unset($quote_array['public']);
				unset($quote_array['time_added']);
				unset($quote_array['time_updated']);
			}
			$items[$key] = array_merge( (array) $quote, $quote_array );
		}
		return $items;
	}



	/** 
	 * Prepares the data for display. This method queries the database, sorts
	 * and filters the data, and generally gets it ready to be displayed. 
	 * 
	 * @global Quotes_Collection_DB $quotescollection_db
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->total_items
	 * @uses $this->total_list_items
	 * @uses $this->filtered
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->output_format()
	 * @uses $this->set_pagination_args()
	 **/
	function prepare_items() {
		global $quotescollection_db; //This is used only if making any database queries

		
		
		/*
		 * Define the column headers. This includes a complete array of columns 
		 * to be displayed (slugs & titles), a list of columns to be kept hidden,
		 * and a list of columns that are sortable. Each of these can be defined
		 * in another method (as we've done here) before being used to build the
		 * value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = array('time_added');
		$sortable = $this->get_sortable_columns();
		
		
		/*
		 * Build an array to be used by the class for column headers. 
		 * The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = $this->get_column_info();//array($columns, $hidden, $sortable);
		

		
		// Frame the parameters to be passed to fetch the data from the database
		$db_args = array();
		$db_args['orderby'] = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'quote_id';
		if( empty($_REQUEST['order']) ) {
			if( 'quote_id' == $db_args['orderby'] )
				$db_args['order'] = 'DESC';
			else $db_args['order'] = 'ASC';
		}
		else 
			$db_args['order'] = $_REQUEST['order'];

		if( isset( $_REQUEST['s'] ) && !empty( $_REQUEST['s'] ) ) {
			$db_args['search'] = (string) $_REQUEST['s'];
		}
		else $db_args['search'] = '';

		if( isset( $_REQUEST['public'] ) && ( $_REQUEST['public'] == 'yes' || $_REQUEST['public'] == 'no' ) ) {
			$db_args['public'] = $_REQUEST['public'];
		}
		else $db_args['public'] = '';


		
		/* Items per page to be shown. We get this from the value set in screen
           options. Default is 20 */
		$per_page = $this->get_items_per_page( 'quotescollection_items_per_page', 20 );
				
		$current_page = $this->get_pagenum();
		
		$this->total_items = $quotescollection_db->count();

		if( $db_args['search'] || $db_args['public']) {
			$this->total_list_items = $quotescollection_db->count( $db_args );
			$this->filtered = true;
		} else {
			$this->total_list_items = $this->total_items;
			$this->filtered = false;
		}
		
		$db_args['num_quotes'] = $per_page;
		$db_args['start'] = ( $current_page - 1 ) * $per_page;
		
		// Now, fetching the data from the database		
		$data = $quotescollection_db->get_quotes_array( $db_args );
		$this->items = $this->output_format( $data, $db_args['search'] );
		
		
		// Register our pagination options & calculations.
		$this->set_pagination_args( array(
			'total_items' => $this->total_list_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($this->total_list_items/$per_page)
		) );
	}


}
?>