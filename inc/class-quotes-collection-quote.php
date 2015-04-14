<?php
/**
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection_Quote {
	public $quote_id;
	public $quote;
	public $author;
	public $source;
	public $tags;
	public $public;
	public $time_added;
	public $time_updated;

	public function __construct($quote_data) {
		if( isset($quote_data['quote_id']) && is_numeric($quote_data['quote_id']) ) {
			$this->quote_id = $quote_data['quote_id'];
		}
		if( isset($quote_data['quote']) && !empty($quote_data['quote']) ) {
			$this->quote = $quote_data['quote'];
		}
		$this->author = isset($quote_data['author'])? $quote_data['author'] : '';
		$this->source = isset($quote_data['source'])? $quote_data['source'] : '';
		$this->tags = isset($quote_data['tags'])? $quote_data['tags'] : '';
		$this->public = isset($quote_data['public'])? $quote_data['public'] : 'yes';
		$this->time_added = isset($quote_data['time_added'])? $quote_data['time_added'] : '';
		$this->time_updated = isset($quote_data['time_updated'])? $quote_data['time_updated'] : '';
	}

	public static function with_condition( $args ) {
		global $quotescollection_db;
		return $quotescollection_db->get_quote($args);
	}

	public static function with_id( $quote_id ) {
		return self::with_condition( array( 'quote_id' => $quote_id ) );
	}

	public static function with_quote( $quote ) {
		$instance = new Quotes_Collection_Quote( array( 'quote' => $quote ) );
		return $instance;
	}

	public static function random() {
		return self::with_condition( array( 'random' => 1 ) );
	}


	public function store() {
		global $quotescollection_db;
		if( !$this->quote ) {
			return false;
		}
		else if( !$this->quote_id ) {
			$result = $quotescollection_db->put_quote($this);
			if( $result && is_numeric( $result ) ) {
				$this->quote_id = $result;
				return true;
			}
			else
				return $result;
		}
		else {
			$result = $quotescollection_db->update_quote($this);
			return $result;
		}
	}

	public function data_as_array() {
		if( !$this->quote ) {
			return (array) $this;
		}
		else return array();
	}

	public function text_format( $text ) {
		if( !$text )
			return;

		$text = make_clickable($text); 
		$text = wptexturize(str_replace(array("\r\n", "\r", "\n"), '', nl2br(trim($text))));
		
		return $text;	
	}

	public function prepare_data() {
		$this->quote = $this->text_format( $this->quote );
		$this->author = $this->text_format( $this->author );
		$this->source = $this->text_format( $this->source );
	}

	public function output_format( $options = array() ) {

		$display = "";

		if( !$this->quote )
			return $display;

		$default_options = array(
			'show_author' => 1,
			'show_source' => 1,
			'before' => '<blockquote id="quote-' . $this->quote_id . '" class="quotescollection-quote">',
			'after' => '</blockquote>',
		);

		$options = array_merge( $default_options, $options );

		$this->prepare_data();

		$display .= "<p>" . $this->quote . "</p>";

		$attribution = "";

		if( $options['show_author'] && $this->author ) {
			$attribution = '<cite class="author">' . $this->author . '</cite>';
		}

		if( $options['show_source'] && $this->source ) {
			if($attribution) $attribution .= ", ";
			$attribution .= '<cite class="title source">' . $this->source . '</cite>';
		}

		if($attribution) {
			$display .= "\n".'<footer class="attribution">&mdash;&nbsp;'.$attribution.'</footer>';
		}

		if( $options['before'] ) {
			$display = $options['before'] . $display;
		}

		if( $options['after'] ) {
			$display .= $options['after'];
		}

		return apply_filters( 'quotescollection_output_format', $display );
	}
}
?>