<?php
/**
 * The main plugin class
 * 
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection {
	
	/** Plugin version **/
	const PLUGIN_VERSION = '2.0.4';

	public $refresh_link_text;
	public $auto_refresh_max;

	function __construct() {
		load_plugin_textdomain( 'quotes-collection', false, quotescollection_rel_path( 'languages' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts_and_styles' ) );
		add_action( 'wp_ajax_quotescollection', array( $this, 'ajax_response' ) );
		add_action( 'wp_ajax_nopriv_quotescollection', array( $this, 'ajax_response' ) );
		$this->initialize_options();
	}


	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( 'activate-plugin_'.$plugin );

		Quotes_Collection_DB::install_db();
	}

	
	/** Instantiate the plugin classes. Hooked to 'plugins_loaded' action **/
	public static function load() {
		global $quotescollection;
		global $quotescollection_db;
		global $quotescollection_admin;
		global $quotescollection_shortcode;
		
		if( NULL === $quotescollection ) {
			$quotescollection = new self();
		}

		if( NULL === $quotescollection_db ) {
			$quotescollection_db = new Quotes_Collection_DB();
		}

		if( is_admin() && NULL === $quotescollection_admin ) {
			$quotescollection_admin = new Quotes_Collection_Admin();
		}

		if( NULL === $quotescollection_shortcode ) {
			$quotescollection_shortcode = new Quotes_Collection_Shortcode();
		}
	}

	/** Load scripts and styles required at the front end **/
	public function load_scripts_and_styles() {

		// Enqueue scripts required for the ajax refresh functionality
		wp_enqueue_script( 
			'quotescollection', // handle
			quotescollection_url( 'js/quotes-collection.js' ), // source
			array('jquery'), // dependencies
			self::PLUGIN_VERSION, // version
			false // load in header, because quotecollectionTimer() has to be loaded before it's called
			);
		wp_localize_script( 'quotescollection', 'quotescollectionAjax', array(
			// URL to wp-admin/admin-ajax.php to process the request
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	 
			// generate a nonce with a unique ID "myajax-post-comment-nonce"
			// so that you can check it later when an AJAX request is sent
			'nonce' => wp_create_nonce( 'quotescollection' ),

			'nextQuote' => $this->refresh_link_text,
			'loading' => __('Loading...', 'quotes-collection'),
			'error' => __('Error getting quote', 'quotes-collection'),
			'autoRefreshMax' => $this->auto_refresh_max,
			'autoRefreshCount' => 0
			)
		);

		// Enqueue styles for the front end
		if ( !is_admin() ) {
			wp_register_style( 
				'quotescollection', 
				quotescollection_url( 'css/quotes-collection.css' ), 
				false, 
				self::PLUGIN_VERSION 
				);
			wp_enqueue_style( 'quotescollection' );
		}

	}

	/** Initialize the plugin options **/
	private function initialize_options() {
		$this->refresh_link_text = __("Next quote &raquo;", 'quotes-collection');
		$this->auto_refresh_max = 20;
		if( $options = get_option( 'quotescollection' ) ) {
			if( isset($options['refresh_link_text']) && $options['refresh_link_text'] ) {
				$this->refresh_link_text = $options['refresh_link_text'];
			}
			if( isset($options['auto_refresh_max']) && $options['auto_refresh_max'] ) {
				$this->auto_refresh_max = $options['auto_refresh_max'];
			}
		}
		else {
			$options = array(
				'refresh_link_text' => $this->refresh_link_text,
				'auto_refresh_max'     => $this->auto_refresh_max,
				);

			add_option( 'quotescollection', $options );
		}       
	}


	/** Ajax response **/
	public function ajax_response()
	{
		check_ajax_referer('quotescollection'); 
		
		$char_limit = (isset($_POST['char_limit']) && is_numeric($_POST['char_limit']))?$_POST['char_limit']:'';
		$tags = $_POST['tags'];
		$orderby = $_POST['orderby'];
		$order = '';
		$exclude = '';
		$splice = '';

		if($orderby == 'random' && $_POST['current'] && is_numeric($_POST['current'])) {
			$exclude = $_POST['current'];
			$splice = '';
			$order = '';
		}
		else {
			if ($_POST['current'] && is_numeric($_POST['current']))
				$splice = $_POST['current'];
			$exclude = '';
			$order = 'DESC';
		}
		
		$args = array(
			'char_limit' => $char_limit,
			'tags' => $tags,
			'orderby' => $orderby,
			'order' => $order,
			'splice' => $splice,
			'exclude' => $exclude,
			'public' => 'yes',
		);


		global $quotescollection_db;

		if(false === ($quote_data = $quotescollection_db->get_quote($args)) && $splice) {
			$args['splice'] = '';
			$quote_data = $quotescollection_db->get_quote($args);
		}

		if($quote_data) {
			$quote_data->prepare_data(); // Format the data for output before sending
			$response = json_encode($quote_data);
			@header("Content-type: text/json; charset=utf-8");
			die( $response ); 
		}
		else
			die();
	}


	/**
	 * Function to display a random (or not so random) quote with (or without)
	 * ajax refresh functionality. Used by the 'quotescollection_quote' template
	 * function and the 'Random Quote' widget.
	 */
	public function quote( $args = array() ) {

		global $quotescollection_db;

		$args_default = array(
			'instance'       => '',
			'show_author'    => 1,
			'show_source'    => 0,
			'ajax_refresh'   => 1,
			'random'         => 1,
			'auto_refresh'   => 0,
			'tags'           => '',
			'char_limit'     => 500,
			'echo'           => 1,
		);

		// Merge with default values
		$args = array_merge( $args_default, $args );

		$instance = ( is_string( $args['instance'] ) )? $args['instance'] : '';
		$show_author = ( false == $args['show_author'] )? 0 : 1;
		$show_source = ( true == $args['show_source'] )? 1 : 0;
		$ajax_refresh = ( false == $args['ajax_refresh'] )? 0 : 1;
		$auto_refresh = 0;
		if( $args['auto_refresh'] ) {
			if( is_numeric( $args['auto_refresh'] ) ) {
				$auto_refresh = $args['auto_refresh'];
			}
			else if( true === $args['auto_refresh'] ) {
				$auto_refresh = 5;
			}
		}

		$random = ( false == $args['random'] )? 0 : 1; 

		// Tags can only be passed as a string, comma separated
		$tags = ( is_string( $args['tags'] ) )? $args['tags'] : '';

		// Only a numeral can be passed as char_limit
		$char_limit = ( is_numeric($args['char_limit']) )? $args['char_limit'] : 500;

		// By default, we fetch a random quote
		$orderby = 'random';
		if( !$random ) {
			$orderby = 'quote_id';
		}

		// Frame the conditions to fetch the quote
		$condition = array(
			'orderby'    => $orderby,
			'order'      => 'DESC',
			'tags'       => $tags,
			'char_limit' => $char_limit,
			'public'     => 'yes',
		);

		if(! ($count = $quotescollection_db->count( $condition ) ) ) {
			return "";
		}

		$dynamic_fetch = 0;
		if( 'random' == $orderby
			&& ( $options = get_option('quotescollection') )
			&& isset( $options['dynamic_fetch'] )
			&& $options['dynamic_fetch'] == 'on'
			&& $count > 1
			) {
			$dynamic_fetch = 1;
		}

		$display = "";

		// And fetch the quote, only if dynamic fetch is off
		if( !$dynamic_fetch ) {
			if ( $quote = Quotes_Collection_Quote::with_condition( $condition ) ) {
				$curr_quote_id = $quote->quote_id;
				$display = $quote->output_format( 
					array( 
						'show_author' => $show_author,
						'show_source' => $show_source,
						'before' => '',
						'after' => '',
					)
				);
				if( !$display ) {
					return "";
				}
			}
			else {
				return "";
			}
		}
		else {
			$curr_quote_id = 0;
		}
		// Refresh functionality is included only when there is more than one quote for the condition
		// And instance ID is required for the refresh functionality
		if( $count > 1 && $instance ) {
			 if( $dynamic_fetch || $ajax_refresh ) {
				$display .= "<script type=\"text/javascript\">\n";
				$display .= 'var args_'.$instance.' = {'
					.'"instanceID":"'.$instance.'", '
					.'"currQuoteID":'.$curr_quote_id.', '
					.'"showAuthor":'.$show_author.', '
					.'"showSource":'.$show_source.', '
					.'"tags":"'.$tags.'", '
					.'"charLimit":'.$char_limit.', '
					.'"orderBy":"'.$orderby.'", '
					.'"ajaxRefresh":'.$ajax_refresh.', '
					.'"autoRefresh":'.$auto_refresh.', '
					.'"dynamicFetch":'.$dynamic_fetch
				.'};';

				if( $dynamic_fetch ) {
					$display .= 'quotescollectionRefresh(args_'.$instance.');';
				}
				else if( $ajax_refresh && $auto_refresh ) {
					$display .= 'quotescollectionTimer(args_'.$instance.');';
				}
				else if ( $ajax_refresh && !$auto_refresh ) {
					$display .= 
						"\n<!--\ndocument.write(\""
							.'<footer class=\"navigation\">'
								.'<div class=\"nav-next\">'
									.'<a class=\"next-quote-link\" style=\"cursor:pointer;\" onclick=\"quotescollectionRefresh(args_'.$instance.')\">'
										. html_entity_decode( $this->refresh_link_text )
									.'</a>'
								.'</div>'
							.'</footer>'
						."\")\n//-->\n";
				}

				$display .= "</script>\n";
			}
		}

		$instance_id = ' id="'.$args['instance'].'"';
		$display = '<div class="quotescollection-quote"'.$instance_id.'>'.$display.'</div>';

		// If 'echo' argument is false, we return the display.
		if( isset($args['echo']) && !$args['echo'] ) {
			return $display;
		}
		else { // Else we simply output the display
			echo $display;
		}

		return true;

	}


}

?>