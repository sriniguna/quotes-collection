<?php
/**
 * The Quotes Collection Database Class
 *
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection_DB {

	const PLUGIN_DB_VERSION = '3.0';

	private $db, $table_name;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = "`". $this->db->prefix . "quotescollection`";
	}


	/**
	 * Fetches quote entries from the database
	 *
	 * @param array $args = array()
	 * @see $this->frame_condition() for arguments that can be passed
	 * @return array of quote entries
	 */
  public function get_quotes_array($args = array()) {

		$quotes_array = array();

  	$char_limit_check = false;
  	$num_quotes = 0;
  	if( isset($args['char_limit']) && $args['char_limit'] && is_numeric($args['char_limit']) )
  		$char_limit_check = true;

  	if( $char_limit_check && isset($args['num_quotes']) && is_numeric($args['num_quotes'] ) ) {
  		$num_quotes = $args['num_quotes'];
  		unset($args['num_quotes']);
  	}

  	$args = $this->validate_args($args);

		$query = new WP_Query($args);

		if( $query->have_posts() ) {
			while( $query->have_posts() ) {
				$query->the_post();
				if( isset($args['splice']) && get_the_ID() >= $args['splice'] )
					continue;

				if( $char_limit_check && $args['char_limit'] < strlen(get_the_content() ) )
					continue;

				$quotes_array[] = array(
					'quote_id' => get_the_ID(),
					'quote' => get_the_content(),
					'author' => get_post_meta( get_the_ID(), Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR, true ),
					'author_url' => get_post_meta( get_the_ID(), Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR_URL, true ),
					'source' => get_post_meta( get_the_ID(), Quotes_Collection_Post_Type_Quote::POST_META_SOURCE, true ),
					'source_url' => get_post_meta( get_the_ID(), Quotes_Collection_Post_Type_Quote::POST_META_SOURCE_URL, true ),
					);

				if( isset($args['splice']) && !empty($quotes_array) )
					break;

				if( $num_quotes && $num_quotes == 1 ) {
					break;
				} else {
					$num_quotes--;
				}

			}
		}

		return $quotes_array;

	}


	/**
	 * Fetches quote entries from the database and returns the array of
	 * Quotes_Collection_Quote objects
	 *
	 * @param array $args = array()
	 * @see $this->frame_condition() for arguments that can be passed
	 * @return array of Quotes_Collection_Quote objects
	 */

	public function get_quotes( $args = array() ) {
		if( $quotes_array = $this->get_quotes_array( $args ) ) {
			$quotes = array();
			foreach( $quotes_array as $quote_data ) {
				$quotes[] = new Quotes_Collection_Quote( $quote_data );
			}
			return $quotes;
		}
		return array();
	}

	/** Fetches a single quote from the database **/
	public function get_quote($args = array()) {
		if( !isset($args['splice']) )
			$args['num_quotes'] = 1;
	   	if($quote_array = $this->get_quotes($args))
			return $quote_array[0];
		else return false;
	}

	/**
	 * Fetches quote entry with a specific ID
	 *
	 * @param int $quote_id
	 * @return array the quote entry
	 */
	public function get_quote_with_id($quote_id) {
		return $this->get_quote(array('quote_id' => $quote_id));
	}

	/**
	 * Checks if our Quotes Collection table is found in the database
	 *
	 * @return bool true if found, false if not
	 */
	private function is_table_found() {
	    if($this->db->get_var("SHOW TABLES LIKE '".$this->table_name."'") != $this->table_name)
			return true;
		else return false;

	}

	public function put_quotes($quotes_data = array()) {
		if(!$quotes_data) return 0;

		$num_quotes_input = 0;

		foreach($quotes_data as $quote_data) {
			if( is_object($quote_data) ) {
				$quote_data = (array) $quote_data;
			}
			$postarr = $this->postarr_for_insert($quote_data);
			if( wp_insert_post($postarr) ) {
				$num_quotes_input++;
			}
		}
		return $num_quotes_input;
	}


	private function postarr_for_insert( $quote_data = array() ) {
		$postarr = array(
			'post_type' => Quotes_Collection_Post_Type_Quote::POST_TYPE,
			'post_status' => 'publish',
			'comment_status' => 'closed',
			'ping_status' => 'closed',
		);
		$meta_input = array();

		if( isset( $quote_data['quote_id'] ) ) {
			$meta_input[Quotes_Collection_Post_Type_Quote::POST_META_QUOTE_ID_OLD] = $quote_data['quote_id'];
		}

		if( isset( $quote_data['quote'] ) && $quote_data['quote'] ) {
			$postarr['post_content'] = $quote_data['quote'];
		} else return;


		if( isset( $quote_data['author'] ) ) {
			$meta_input[Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR] = $quote_data['author'];
		}

		if( isset( $quote_data['author_url'] ) ) {
			$meta_input[Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR_URL] = $quote_data['author_url'];
		}

		if( isset( $quote_data['source'] ) ) {
			$meta_input[Quotes_Collection_Post_Type_Quote::POST_META_SOURCE] = $quote_data['source'];
		}

		if( isset( $quote_data['source_url'] ) ) {
			$meta_input[Quotes_Collection_Post_Type_Quote::POST_META_SOURCE_URL] = $quote_data['source_url'];
		}


		if( isset( $quote_data['tags'] ) ) {
			$tags = explode(',', $quote_data['tags']);
			$postarr['tax_input'] = array(	'quotcoll_quote_tag' => $tags );
		}

		if( isset( $quote_data['public'] ) && $quote_data['public'] == 'no' ) {
				$postarr['post_status'] = 'private';
		}

		if( isset( $quote_data['time_added'] ) ) {
			$postarr['post_date'] = $quote_data['time_added'];
		}

		if( isset( $quote_data['time_updated'] ) ) {
			$postarr['post_modified'] = $quote_data['time_updated'];
		}

		$postarr['meta_input'] = $meta_input;

		return $postarr;

	}




	/**
	 * Counts and returns the number of entries with a particular entries.
	 * If no parameter is passed, counts the total number of entries in DB
	 *
	 * @param array $condition
	 * @return int
	 */
	public function count($args)
	{
		$quotes = $this->get_quotes_array($args);
		return count($quotes);
	}



	public static function uninstall_db() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quotescollection" );
	}


	private function validate_args( $args = array() ) {
		$args_validated = array(
			'post_type' => Quotes_Collection_Post_Type_Quote::POST_TYPE,
			'order' => 'ASC',
			'orderby' => 'ID',
			'nopaging' => true,
			'posts_per_page' => -1,
		);

		if( isset($args['quote_id']) && is_numeric($args['quote_id']) ) {
			$args_validated['meta_key'] = Quotes_Collection_Post_Type_Quote::POST_META_QUOTE_ID_OLD;
			$args_validated['meta_value'] = $args['quote_id'];
		}

		if( isset($args['author']) && !empty($args['author']) ) {
			$args_validated['meta_key'] = Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR;
			$args_validated['meta_value'] = trim($args['author']);
		}

		if( isset($args['source']) && !empty($args['source']) ) {
			$args_validated['meta_key'] = Quotes_Collection_Post_Type_Quote::POST_META_SOURCE;
			$args_validated['meta_value'] = trim($args['source']);
		}


		if( isset($args['tags']) && $args['tags'] ) {
			$tags = explode(',', $args['tags']);
			foreach($tags as $key => $tag) {
				$tags[$key] = trim($tag);
			}
			$args_validated['tax_query'] = array(
				array(
					'taxonomy' => Quotes_Collection_Post_Type_Quote::TAXONOMY_TAG,
					'field' => 'slug',
					'terms' => $tags,
					),
				);
		}

		if( isset($args['char_limit']) && $args['char_limit'] && is_numeric( $args['char_limit'] ) )
			$args_validated['char_limit'] = $args['char_limit'];


		if( isset($args['exclude']) && !empty($args['exclude']) ) {
			$exclude = explode(',', $args['exclude']);
			foreach( $exclude as $key => $id ) {
				$id = trim($id);
				if( !is_numeric($id) ) {
					unset($exclude[$key]);
				}
				else {
					$exclude[$key] = $id;
				}
			}
			if(!empty($exclude)) {
				$args_validated['post__not_in'] = $exclude;
			}
		}

		if( isset($args['splice']) && is_numeric($args['splice']) ) {
			$args_validated['splice'] = $args['splice'];
		}

		if( isset($args['order']) ) {
			if( strtoupper($args['order']) == 'DESC' )
				$args_validated['order'] = 	'DESC';
			else if ( strtoupper($args['order']) == 'ASC' )
				$args_validated['order'] = 	'ASC';
		}

		if( isset($args['orderby']) ) {
			switch($args['orderby']) {
				case 'quote_id':
					$args_validated['orderby'] = 'ID';
					break;
				case 'author':
					$args_validated['meta_query'] = array(
						'relation' => 'OR',
						array(
							'key' => Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR,
							'compare' => 'EXISTS',
						),
						array(
							'key' => Quotes_Collection_Post_Type_Quote::POST_META_AUTHOR,
							'compare' => 'NOT EXISTS',
						),
					);
					$args_validated['orderby'] = 'meta_value';
					break;
				case 'source':
					$args_validated['meta_query'] = array(
						'relation' => 'OR',
						'source_exists' => array(
							'key' => Quotes_Collection_Post_Type_Quote::POST_META_SOURCE,
							'compare' => 'EXISTS',
						),
						'source_not_exists' => array(
							'key' => Quotes_Collection_Post_Type_Quote::POST_META_SOURCE,
							'compare' => 'NOT EXISTS',
						),
					);
					$args_validated['orderby'] = array(
						'source_not_exists'	=> $args_validated['order'],
						'source_exists' => $args_validated['order']
					);
					break;
				case 'time_added':
					$args_validated['orderby'] = 'date';
					break;
				case 'random':
					$args_validated['orderby'] = 'rand';

			}
		}


		if( isset($args['num_quotes']) && $args['num_quotes'] && is_numeric($args['num_quotes']) ) {
			$args_validated['nopaging'] = false;
			$args_validated['posts_per_page'] = $args['num_quotes'];
		}
		if( isset($args['start']) && is_numeric($args['start']) ) {
			$args_validated['offset'] = $args['start'];
		}

		return $args_validated;

	}




}

?>
