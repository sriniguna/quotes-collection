<?php
/**
 * The Quotes Collection Database Class
 * 
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection_DB {

	const PLUGIN_DB_VERSION = '1.4'; 

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
			$sql = "SELECT `quote_id`, `quote`, `author`, `source`, `tags`, `public`, `time_added`
			FROM " . $this->table_name;

		if($args) {
			$sql .= $this->frame_condition($args);
		}

		if($quotes = $this->db->get_results($sql, ARRAY_A))
			return $quotes;	
		else
			return array();
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

	/**
	 * Validates the quote data before it can be safely stored in the database
	 *
	 * @param array $data the quote data
	 * @return array the validated data
	 */
	private function validate_data($data = array()) {
		if(!$data) return array();
	    global $allowedposttags;

		$quote = wp_kses( stripslashes($data['quote']), $allowedposttags );
		$author = wp_kses( stripslashes($data['author']), array( 'a' => array( 'href' => array(),'title' => array() ) ) ) ;	
		$source = wp_kses( stripslashes($data['source']), array( 'a' => array( 'href' => array(),'title' => array() ) ) ) ;	
		$tags = strip_tags( stripslashes($data['tags']) );
		
		$tags = explode(',', $tags);
		foreach ($tags as $key => $tag)
			$tags[$key] = trim($tag);
		$tags = implode(',', $tags);
		if( !isset( $data['public'] ) || ( isset( $data['public'] ) && $data['public'] == 'no' ) )
			$public = "no";
		else
			$public = "yes";
		$data = compact("quote", "author", "source", "tags", "public");
		return $data;
	}

	
	/**
	 * Function to store a single quote in the db
	 *
	 * @param array $entry the quote data
	 */
	public function put_quote($quote_data = array()) {
		if( is_object($quote_data) ) {
			$quote_data = (array) $quote_data;
		}
	    if(!$quote_data || !$quote_data['quote']) return false;
		if(!$this->is_table_found()) 
			return false;
		$quote_data = $this->validate_data($quote_data);

		extract($quote_data);
		
	    $insert = $this->db->prepare( "INSERT INTO " . $this->table_name .
			"(`quote`, `author`, `source`, `tags`, `public`, `time_added`)" .
			"VALUES (%s, %s, %s, %s, %s, NOW())" , $quote, $author, $source, $tags, $public);	
		
		$result = $this->db->query($insert);

		if( 1 == $result ) {
			return $this->db->insert_id;
		}
		else return $result;
	}

	/**
	 * Function to store a bulk of quote entries. Used by the import
	 * functionality.
	 *
	 * @param array $quotes_data a multidimensional array of quote data
	 */
	public function put_quotes($quotes_data = array()) {
		if(!$quotes_data) return 0;

		$values = array();
		$placeholders = array();

		$insert = "INSERT INTO " . $this->table_name .
			" (`quote`, `author`, `source`, `tags`, `public`, `time_added`)" .
			" VALUES ";

		foreach($quotes_data as $quote_data) {
			if( is_object($quote_data) ) {
				$quote_data = (array) $quote_data;
			}
			$quote_data = $this->validate_data($quote_data);

			extract($quote_data);

			array_push($values, $quote, $author, $source, $tags, $public);

			$placeholders[] = "(%s, %s, %s, %s, %s, NOW())";
		}

		$insert .= implode(', ', $placeholders);

		$insert = $this->db->prepare($insert, $values);

		return $this->db->query($insert);
	}

	/**
	 * Updates a quote entry in DB with new values
	 *
	 * @param array $entry the quote entry
	 */
	public function update_quote($quote_data = array()) {
		if(!$this->is_table_found()) 
			return false;

		if( is_object($quote_data) ) {
			$quote_data = (array) $quote_data;
		}

		if( !$quote_data['quote'] )  return 0;
		if( !($quote_id = $quote_data['quote_id']) )  return $this->put_quote($quote_data);

		$quote_data = $this->validate_data($quote_data);
		extract($quote_data);
		$update = "UPDATE " . $this->table_name . "
			SET `quote` = %s,
				`author` = %s,
				`source` = %s, 
				`tags` = %s,
				`public` = %s, 
				`time_updated` = NOW()
			WHERE `quote_id` = %d";
		$update = $this->db->prepare( $update, $quote, $author, $source, $tags, $public, $quote_id);
		return $this->db->query( $update );
	}


	/**
	 * Deletes a quote entry in the DB
	 *
	 * @param int $quote_id the ID of the entry to be deleted
	 */
	public function delete_quote($quote_id) {
		if(is_numeric($quote_id)) {
				$sql = "DELETE from " . $this->table_name .
				" WHERE quote_id = " . $quote_id;
			return $this->db->query($sql);
		}
		else return 0;
	}


	/**
	 * Function to delete a bulk of quotes
	 *
	 * @param array $quote_ids an array of IDs of the entries to be deleted
	 */
	public function delete_quotes($quote_ids) {
		if(!$quote_ids)
			return 0;

		foreach( $quote_ids as $quote_id ) {
			if(! is_numeric($quote_id) )
				return 0;
		}

		$sql = "DELETE FROM ".$this->table_name
			."WHERE quote_id IN (".implode(', ', $quote_ids).")";
		return $this->db->query($sql);
	}

	/**
	 * Function to make a set of entries private or public
	 *
	 * @param array $quote_ids an array of IDs of the entries to be updated
	 * @param string $visibility should be'yes' or 'no'
	 */
	public function change_visibility($quote_ids, $visibility = 'yes') {
		if( !$quote_ids || ($visibility != 'yes' && $visibility != 'no') )
			return 0;
		$sql = "UPDATE ".$this->table_name
			."SET public = '".$visibility."',
			time_updated = NOW()
			WHERE quote_id IN (".implode(', ', $quote_ids).")";
		return $this->db->query($sql);
	}

	/**
	 * Counts and returns the number of entries with a particular entries.
	 * If no parameter is passed, counts the total number of entries in DB
	 *
	 * @param array $condition
	 * @return int
	 */
	public function count($condition = array())
	{	
		$sql = "SELECT COUNT(*) FROM " . $this->table_name;
		if($condition)
			$sql .= $this->frame_condition($condition);
		$count = $this->db->get_var($sql);
		return $count;
	}


	public static function install_db() {

		if( 
			( ! current_user_can( 'activate_plugins' ) )
			|| (
				$options = get_option('quotescollection')
				&& isset( $options['db_version'] )
				&& self::PLUGIN_DB_VERSION == $options['db_version'] 
			)
		) {
			return;
		}	

		global $wpdb;

		$table_name = $wpdb->prefix.'quotescollection';

		if(!defined('DB_CHARSET') || !($db_charset = DB_CHARSET))
			$db_charset = 'utf8';
		$db_charset = "CHARACTER SET ".$db_charset;
		if(defined('DB_COLLATE') && $db_collate = DB_COLLATE) 
			$db_collate = "COLLATE ".$db_collate;


		// if table name already exists
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
	   		$wpdb->query("ALTER TABLE `{$table_name}` {$db_charset} {$db_collate}");

	   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY quote TEXT {$db_charset} {$db_collate}");

	   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY author VARCHAR(255) {$db_charset} {$db_collate}");

	   		$wpdb->query("ALTER TABLE `{$table_name}` MODIFY source VARCHAR(255) {$db_charset} {$db_collate}");

	   		if(!($wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'tags'"))) {
	   			$wpdb->query("ALTER TABLE `{$table_name}` ADD `tags` VARCHAR(255) {$db_charset} {$db_collate} AFTER `source`");
			}
	   		if(!($wpdb->get_results("SHOW COLUMNS FROM {$table_name} LIKE 'public'"))) {
	   			$wpdb->query("ALTER TABLE `{$table_name}` CHANGE `visible` `public` enum('yes', 'no') DEFAULT 'yes' NOT NULL");
			}
		}
		else {
			//Creating the table ... fresh!
			$sql = "CREATE TABLE " . $table_name . " (
				quote_id MEDIUMINT NOT NULL AUTO_INCREMENT,
				quote TEXT NOT NULL,
				author VARCHAR(255),
				source VARCHAR(255),
				tags VARCHAR(255),
				public enum('yes', 'no') DEFAULT 'yes' NOT NULL,
				time_added datetime NOT NULL,
				time_updated datetime,
				PRIMARY KEY  (quote_id)
			) {$db_charset} {$db_collate};";
			$results = $wpdb->query( $sql );
		}
		
		$options['db_version'] = self::PLUGIN_DB_VERSION;
		update_option('quotescollection', $options);

	}


	public static function uninstall_db() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}quotescollection" );
	}



	/**
	 * Frames the database query condition
	 * 
	 * @param array $args = array (
	 *    'quote_id'   => 0,          // Number, quote_id of the quote to be fetched
	 *    'author'     => '',         // String, to fetch quote/s by a particular author
	 *    'source'     => '',         // String, to fetch quote/s with a particular source
	 *    'tags'       => '',         // String, comma separated tags, to fetch quote/s having one of the tags
	 *    'public'     => ''          // String, 'yes' or 'no'
	 *    'char_limit' => 0,          // Number, character limit
	 *    'exclude'    => 0,          // Number, quote_id of the particular quote to be excluded
	 *    'splice'     => 0,          // Number, quote_id, only quotes with IDs less than this will be fetched
	 *    'orderby'    => 'quote_id', // String, can be one of 'quote_id', 'author', 'source', 'time_added', 'random'
	 *    'order'      => 'ASC',      // String, 'ASC' or 'DESC' 
	 *    'num_quotes' => 0,          // Number of quotes to be fetched
	 *    'start'      => 0,          // Number, quote_id, used in pagination along with num_quotes
	 * )
	 *
	 * @return string    The SQL condition
	 */
	private function frame_condition($args = array()) {
		if(!$args) return "";

		$condition = '';

		if( isset($args['quote_id']) && is_numeric($args['quote_id']) ) {
			$condition .= " `quote_id` = ".$args['quote_id'];
		}

		if( isset($args['exclude']) && is_numeric($args['exclude']) ) {
			$condition .= " `quote_id` <> ".$args['exclude'];
		}

		if( isset($args['splice']) && is_numeric($args['splice']) ) {
			$condition .= " `quote_id` < ".$args['splice'];
		}

		if( isset($args['author']) ) {
			$condition .= " `author`='" . esc_sql( stripslashes( strip_tags ( $args['author'] ) ) ) . "'";
		}
		if( isset($args['source']) ) {
			if($condition) $condition .= " AND";
			$condition .= " `source`='" . esc_sql( stripslashes( strip_tags ( $args['source'] ) ) ) . "'";
		}
		if( isset($args['tags']) && is_string($args['tags']) && !empty($args['tags']) ) {
			$taglist = explode(',', html_entity_decode($args['tags']));
			$tag_condition = "";
			foreach($taglist as $tag) {
						$tag = $this->db->esc_like( strip_tags( trim( $tag ) ) );
				if($tag_condition) $tag_condition .= " OR ";
				$tag_condition .= 
					"tags = '{$tag}' "
					."OR tags LIKE '{$tag},%' "
					."OR tags LIKE '%,{$tag},%' "
					."OR tags LIKE '%,{$tag}'";
			}
			if($tag_condition) {
				if($condition) $condition .= " AND";
				$condition .= " ({$tag_condition})";
			}
		}
		if( isset($args['search']) && is_string($args['search']) && !empty($args['search']) ) {
			$search_query = $this->db->esc_like( strip_tags( trim( $args['search'] ) ) );
			
			$search_condition = 
				"quote = '{$search_query}' "
				."OR quote LIKE '{$search_query}%' "
				."OR quote LIKE '%{$search_query}%' "
				."OR quote LIKE '%{$search_query}' "
				."OR author = '{$search_query}' "
				."OR author LIKE '{$search_query},%' "
				."OR author LIKE '%{$search_query}%' "
				."OR author LIKE '%{$search_query}' "
				."OR source = '{$search_query}' "
				."OR source LIKE '{$search_query}%' "
				."OR source LIKE '%{$search_query}%' "
				."OR source LIKE '%{$search_query}' "
				."OR tags = '{$search_query}' "
				."OR tags LIKE '{$search_query},%' "
				."OR tags LIKE '%,{$search_query},%' "
				."OR tags LIKE '%,{$search_query}'";
				
			if($condition) $condition .= " AND";
			$condition .= " ({$search_condition})";
		}

		if(isset($args['char_limit']) && is_numeric($args['char_limit']) && $args['char_limit'] > 0) {
			if($condition) $condition .= " AND";
			$condition .= " CHAR_LENGTH(`quote`) <= ".$args['char_limit'];
		}

		if(isset($args['public']) && ( $args['public'] == 'yes' || $args['public'] == 'no' ) ) {
			if($condition) $condition .= " AND";
			$condition .= " `public` = '". esc_sql($args['public'])."'";			
		}

		if($condition)
			$condition = " WHERE".$condition;

		if(isset($args['orderby']) && $args['orderby']) {
			if($args['orderby'] == "random")
				$condition .= " ORDER BY RAND(UNIX_TIMESTAMP(NOW()))";
			else if(in_array($args['orderby'], array('quote_id', 'quote', 'author', 'source', 'time_added'))) {
				$condition .= " ORDER BY `".$args['orderby']."`";
				if( isset($args['order']) && ($args['order'] == 'DESC' || $args['order'] == 'desc') )
					$condition .= " DESC";
			}
		}
		if(isset($args['num_quotes']) && is_numeric($args['num_quotes'])) {
			if(isset($args['start']) && is_numeric($args['start'])) {
				$condition .= " LIMIT ".$args['start'].", ".$args['num_quotes']	;
			}
			else
			$condition .= " LIMIT ".$args['num_quotes'];
		}
		return $condition;

	} 



} 

?>