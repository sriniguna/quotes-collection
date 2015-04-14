<?php
/**
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection_Shortcode {

	public function __construct() {
		add_shortcode( 'quotescollection', array($this, 'do_shortcode') );
		add_shortcode( 'quotcoll', array( $this, 'do_shortcode' ) );
		add_shortcode( 'quotecoll', array( $this, 'do_shortcode' ) ); // just in case, somebody misspells the shortcode
		add_filter( 'the_content', array( $this, 'filter_content' ), 7); // Backwards compatibility
		add_filter( 'the_excerpt', array( $this, 'filter_content' ), 7); // Backwards compatibility
	}

	public function do_shortcode($atts = array()) {
		$db = new Quotes_Collection_DB();
		extract( shortcode_atts( array(
			'limit' => 0,
			'id' => 0,
			'author' => '',
			'source' => '',
			'tags' => '',
			'orderby' => 'quote_id',
			'order' => 'ASC',
			'paging' => false,
			'limit_per_page' => 10
		), $atts ) );

		// Initialize the variable that holds args to be passed to the DB function to get the quotes
		// And set 'public' argument to 'yes', because we don't want to display private quotes in public
		$db_args = array('public' => 'yes');

		// 'quote_id' is also a valid shortcode parameter synonymous to 'id'
		if(isset($quote_id) && is_numeric($quote_id)) $id = $quote_id;

		// If an id is specified, no need to process other attributes. Simply get the quote and return.
		if($id && is_numeric($id)) {
			$db_args['quote_id'] = $id;
			if( $quote = Quotes_Collection_Quote::with_condition($db_args) ) {
				return $quote->output_format();
			}
			else
				return "";
		}

		if($author)
			$db_args['author'] = $author;
		if($source)
			$db_args['source'] = $source;
		
		if($tags) 
			$db_args['tags'] = $tags;
		
		switch($orderby) {
			case 'quote_id':
			case 'author':
			case 'source':
			case 'time_added':
			case 'random':
				$db_args['orderby'] = $orderby;
				break;
			case 'date_added':
				$db_args['orderby'] = 'time_added';
				break;
			case 'rand':
				$db_args['orderby'] = 'random';
				break;
			default:
				$db_args['orderby'] = 'quote_id';
		}

		if($order == 'DESC' || $order == 'desc' || $order == 'Desc')
			$db_args['order'] = 'DESC';
		else $db_args['order'] = 'ASC';


		$page_nav = "";

		if($paging == true || $paging == 1) {
	
			$num_quotes = $db->count($db_args);
		
			$total_pages = ceil($num_quotes / $limit_per_page);
		
		
			if(!isset($_GET['quotes_page']) || !$_GET['quotes_page'] || !is_numeric($_GET['quotes_page']))
				$page = 1;
			else
				$page = $_GET['quotes_page'];
		
			if($page > $total_pages) $page = $total_pages;
		
			if($page_nav = $this->pagenav($total_pages, $page, 0, 'quotes_page'))
				$page_nav = '<div class="quotescollection_pagenav">'.$page_nav.'</div>';
			
			$start = ($page - 1) * $limit_per_page;

			$db_args['num_quotes'] = $limit_per_page;
			$db_args['start'] = $start;
		
		}
	
		else if($limit && is_numeric($limit))
			$db_args['num_quotes'] = $limit;

				
		if( $quotes_data = $db->get_quotes($db_args) ) {
			return $page_nav.$this->output_format($quotes_data).$page_nav;
		}
		else
			return "";

	}

	private function output_format($quotes = array()) {
		
		$display = "";

		foreach($quotes as $quote) {
			$display .= $quote->output_format();
		}

		return apply_filters( 'quotescollection_shortcode_output_format', $display );

	}

	private function pagenav($total, $current = 1, $format = 0, $paged = 'paged', $url = "") {
		if($total == 1 && $current == 1) return "";
	
		if(!$url) {
			$url = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$url .= "s";}
			$url .= "://";
			if ($_SERVER["SERVER_PORT"] != "80") {
				$url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
			} else {
				$url .= $_SERVER["SERVER_NAME"];
			}

			if ( get_option('permalink_structure') != '' ) {
				if($_SERVER['REQUEST_URI']) {
					$request_uri = explode('?', $_SERVER['REQUEST_URI']);
					$url .= $request_uri[0];
				}
				else $url .= "/";
			}
			else {
				$url .= $_SERVER["PHP_SELF"];
			}
			
			if($query_string = $_SERVER['QUERY_STRING']) {
				$parms = explode('&', $query_string);
				$y = '';
				foreach($parms as $parm) {
					$x = explode('=', $parm);
					if($x[0] == $paged) {
						$query_string = str_replace($y.$parm, '', $query_string);
					}
					else $y = '&';
				}
				if($query_string) {
					$url .= '?'.$query_string;
					$a = '&';
				}
				else $a = '?';	
			}
			else $a = '?';
		}
		else {
			$a = '?';
			if(strpos($url, '?')) $a = '&';	
		}
		
		if(!$format || $format > 2 || $format < 0 || !is_numeric($format)) {	
			if($total <= 8) $format = 1;
			else $format = 2;
		}
		
		
		if($current > $total) $current = $total;
			$pagenav = "";

		if($format == 2) {
			$first_disabled = $prev_disabled = $next_disabled = $last_disabled = '';
			if($current == 1)
				$first_disabled = $prev_disabled = ' disabled';
			if($current == $total)
				$next_disabled = $last_disabled = ' disabled';

			$pagenav .= "<a class=\"first-page{$first_disabled}\" title=\"".__('Go to the first page', 'quotes-collection')."\" href=\"{$url}\">&laquo;</a>&nbsp;&nbsp;";

			$pagenav .= "<a class=\"prev-page{$prev_disabled}\" title=\"".__('Go to the previous page', 'quotes-collection')."\" href=\"{$url}{$a}{$paged}=".($current - 1)."\">&#139;</a>&nbsp;&nbsp;";

			$pagenav .= '<span class="paging-input">'.$current.' of <span class="total-pages">'.$total.'</span></span>';

			$pagenav .= "&nbsp;&nbsp;<a class=\"next-page{$next_disabled}\" title=\"".__('Go to the next page', 'quotes-collection')."\" href=\"{$url}{$a}{$paged}=".($current + 1)."\">&#155;</a>";

			$pagenav .= "&nbsp;&nbsp;<a class=\"last-page{$last_disabled}\" title=\"".__('Go to the last page', 'quotes-collection')."\" href=\"{$url}{$a}{$paged}={$total}\">&raquo;</a>";
		
		}
		else {
			$pagenav = __("Goto page:", 'quotes-collection');
			for( $i = 1; $i <= $total; $i++ ) {
				if($i == $current)
					$pagenav .= "&nbsp;<strong>{$i}</strong>";
				else if($i == 1)
					$pagenav .= "&nbsp;<a href=\"{$url}\">{$i}</a>";
				else 
					$pagenav .= "&nbsp;<a href=\"{$url}{$a}{$paged}={$i}\">{$i}</a>";
			}
		}
		return $pagenav;

	}


	public function filter_content( $text )
	{
		$start = strpos($text,"[quote|id=");
		if ($start !== FALSE) {
			$text = preg_replace_callback( "/\[quote\|id=(\d+)\]/i", array( $this, 'displayquote'), $text );
		}
		$start = strpos($text,"[quote|random]");
		if ($start !== FALSE) {
			$text = preg_replace_callback( "/\[quote\|random\]/i", array( $this, 'displayquote' ), $text );
		}
		$start = strpos($text,"[quote|all]");
		if ($start !== FALSE) {
			$text = preg_replace_callback( "/\[quote\|all\]/i", array( $this, 'do_shortcode' ), $text );
		}
		$start = strpos($text,"[quote|author=");
		if($start !== FALSE) {
			$text = preg_replace_callback("/\[quote\|author=(.{1,})?\]/i", array( $this, 'displayquotes_author' ), $text);
		}
		$start = strpos($text,"[quote|source=");
		if($start !== FALSE) {
			$text = preg_replace_callback("/\[quote\|source=(.{1,})?\]/i", array( $this, 'displayquotes_source' ), $text);
		}
		$start = strpos($text,"[quote|tags=");
		if($start !== FALSE) {
			$text = preg_replace_callback("/\[quote\|tags=(.{1,})?\]/i", array( $this, 'displayquotes_tags' ), $text);
		}
		return $text;
	}

	private function displayquote($matches)
	{
		if(!isset($matches[1]) || (isset($matches[1]) && !$matches[1]) || $matches[0] == "[quote|random]")
			$atts = array( 'orderby' => 'random', 'limit' => 1 );
		else
			$atts = array (	'id' => $matches[1] );
		
		return $this->do_shortcode($atts);
	}


	private function displayquotes_author($matches)
	{
		return $this->do_shortcode(array('author'=>$matches[1]));
	}


	private function displayquotes_source($matches)
	{
		return $this->do_shortcode(array('source'=>$matches[1]));
	}

	private function displayquotes_tags($matches)
	{
		return $this->do_shortcode(array('tags'=>$matches[1]));
	}



}

?>