<?php
/**
 * Quotes Collection Widget
 *
 * @package Quotes Collection
 * @since 2.0
 */

class Quotes_Collection_Widget extends WP_Widget {

	/**
	 * Constructor. Sets up the widget name, description, etc.
	 */
	function __construct() {
		parent::__construct(
			'quotescollection', // Base ID
			_x('Random Quote', 'widget name', 'quotes-collection'), // Name
			array( 'description' => _x('Random quote from your quotes collection', 'widget description', 'quotes-collection'), ) // Args
			);
	}

	/**
	 * Register the widget. Should be hooked to 'widgets_init'.
	 */
	public static function register() {
		register_widget( get_class() );
	}

	/**
	 * Front end output
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $quotescollection;

		if( $instance ) {
			$options['title'] = isset($instance['title'])?$instance['title']:__('Random Quote', 'quotes-collection');
			$options['show_author'] = isset($instance['show_author'])?$instance['show_author']:1;
			$options['show_source'] = isset($instance['show_source'])?$instance['show_source']:1;
			$options['ajax_refresh'] = isset($instance['ajax_refresh'])?$instance['ajax_refresh']:1;
			$options['auto_refresh'] = isset($instance['auto_refresh'])?$instance['auto_refresh']:0;
			$options['random'] = isset($instance['random_refresh'])?$instance['random_refresh']:1;
			if($options['auto_refresh'])
				$options['auto_refresh'] = isset($instance['refresh_interval'])?$instance['refresh_interval']:5;
			$options['char_limit'] = $instance['char_limit'];
			$options['tags'] = $instance['tags'];
		}
		else {  // Default options
			$options = $this->default_options();
			$options['random'] = $options['random_refresh'];
		}


		$options['order'] = 'DESC';
		$options['echo'] = 0;
		$options['instance'] = 'w_'.str_replace('-', '_', $args['widget_id']);

		if($quote = $quotescollection->quote($options)) {
			extract( $args );
			echo $before_widget;
			if($options['title']) echo $before_title . apply_filters('the_title', $options['title']) . $after_title . "\n";
			echo $quote;
			echo $after_widget;
		}

	}


	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		if( $instance ) {
			$options['title'] = isset($instance['title'])?$instance['title']:__('Random Quote', 'quotes-collection');
			$options['show_author'] = isset($instance['show_author'])?$instance['show_author']:1;
			$options['show_source'] = isset($instance['show_source'])?$instance['show_source']:1;
			$options['ajax_refresh'] = isset($instance['ajax_refresh'])?$instance['ajax_refresh']:1;
			$options['auto_refresh'] = isset($instance['auto_refresh'])?$instance['auto_refresh']:0;
			$options['random_refresh'] = isset($instance['random_refresh'])?$instance['random_refresh']:1;
			$options['refresh_interval'] = isset($instance['refresh_interval'])?$instance['refresh_interval']:5;
			$options['char_limit'] = $instance['char_limit'];
			$options['tags'] = $instance['tags'];
		}
		else {
			$options = $this->default_options();
		}


		$show_author_checked = $show_source_checked	= $ajax_refresh_checked = $auto_refresh_checked = $random_refresh_checked = '';
		$int_select = array ( '5' => '', '10' => '', '15' => '', '20' => '', '30' => '', '60' => '');
		if($options['show_author'])
			$show_author_checked = ' checked="checked"';
		if($options['show_source'])
			$show_source_checked = ' checked="checked"';
		if($options['ajax_refresh'])
			$ajax_refresh_checked = ' checked="checked"';
		if($options['auto_refresh'])
			$auto_refresh_checked = ' checked="checked"';
		if($options['random_refresh'])
			$random_refresh_checked = ' checked="checked"';
		$int_select[$options['refresh_interval']] = ' selected="selected"';


		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title', 'quotes-collection' ).'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $options['title'] ).'">';
		echo '</p>';

		echo '<p>';
		echo '<input type="checkbox" id="'.$this->get_field_id( 'show_author' ).'" name="'.$this->get_field_name('show_author').'"'.$show_author_checked.' />';
		echo '<label for="'.$this->get_field_id( 'show_author' ).'">'.__( 'Show author?', 'quotes-collection' ).'</label>';
		echo '</p>';

		echo '<p>';
		echo '<input type="checkbox" id="'.$this->get_field_id( 'show_source' ).'" name="'.$this->get_field_name('show_source').'"'.$show_source_checked.' />';
		echo '<label for="'.$this->get_field_id( 'show_source' ).'">'.__( 'Show source?', 'quotes-collection' ).'</label>';
		echo '</p>';

		echo '<p>';
		echo '<input type="checkbox" id="'.$this->get_field_id( 'ajax_refresh' ).'" name="'.$this->get_field_name('ajax_refresh').'"'.$ajax_refresh_checked.' />';
		echo '<label for="'.$this->get_field_id( 'ajax_refresh' ).'">'.__( 'Refresh feature', 'quotes-collection' ).'</label>';
		echo '</p>';

		echo "<p style=\"text-align:left;\"><small><a id=\"quotescollection-adv-key\" style=\"cursor:pointer;\" onclick=\"jQuery('div#quotescollection-adv-opts').slideToggle();\">".__('Advanced options', 'quotes-collection')." &raquo;</a></small></p>";
		echo "<div id=\"quotescollection-adv-opts\" style=\"display:none\">";

		echo '<p>';
		echo '<input type="checkbox" id="'.$this->get_field_id( 'random_refresh' ).'" name="'.$this->get_field_name('random_refresh').'"'.$random_refresh_checked.' />';
		echo '<label for="'.$this->get_field_id( 'random_refresh' ).'">'.__( 'Random refresh', 'quotes-collection' ).'</label>';
		echo '<br/><span class="setting-description"><small>'.__('Unchecking this will rotate quotes in the order added, latest first.', 'quotes-collection').'</small></span></p>';
		echo '</p>';

		echo '<p>';
		echo '<input type="checkbox" id="'.$this->get_field_id( 'auto_refresh' ).'" name="'.$this->get_field_name('auto_refresh').'"'.$auto_refresh_checked.' />';
		echo '<label for="'.$this->get_field_id( 'auto_refresh' ).'">'.__( 'Auto refresh', 'quotes-collection' ).'</label>';
		echo ' <label for="'.$this->get_field_id( 'refresh_interval' ).'"">';
		printf( __('every %s sec', 'quotes-collection'), '<input type="number" id="'.$this->get_field_id( 'refresh_interval' ).'" name="'.$this->get_field_name('refresh_interval').'" value="'.$options['refresh_interval'].'" min="3" max="60" step="1" style="width:3em;" />' );
		echo '</label>';
		echo '</p>';
		
		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'tags' ).'">'.__( 'Tags filter', 'quotes-collection' ).'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'tags' ).'" name="'.$this->get_field_name( 'tags' ).'" type="text" value="'.esc_attr( $options['tags'] ).'">';
		echo '<br/><span class="setting-description"><small>'.__('Comma separated', 'quotes-collection').'</small>';
		echo '</p>';

		echo '<p>';
		echo '<label for="'.$this->get_field_id( 'char_limit' ).'">'.__( 'Character limit', 'quotes-collection' ).'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'char_limit' ).'" name="'.$this->get_field_name( 'char_limit' ).'" type="text" value="'.esc_attr( $options['char_limit'] ).'">';
		echo '</p>';

		echo '</div> <!-- #quotescollection-adv-opts -->';

	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( stripslashes( $new_instance['title'] ) ) : '';
		$instance['show_author'] = (isset($new_instance['show_author']) && $new_instance['show_author'])?1:0;
		$instance['show_source'] = (isset($new_instance['show_source']) && $new_instance['show_source'])?1:0;
		$instance['ajax_refresh'] = (isset($new_instance['ajax_refresh']) && $new_instance['ajax_refresh'])?1:0;
		$instance['auto_refresh'] = (isset($new_instance['auto_refresh']) && $new_instance['auto_refresh'])?1:0;
		$instance['refresh_interval'] = $new_instance['refresh_interval'];
		$instance['random_refresh'] = (isset($new_instance['random_refresh']) && $new_instance['random_refresh'])?1:0;
		$instance['tags'] = strip_tags(stripslashes($new_instance['tags']));
		$instance['char_limit'] = strip_tags(stripslashes($new_instance['char_limit']));
		if(!$instance['char_limit'])
			$instance['char_limit'] = __('none', 'quotes-collection');

		return $instance;
	}

	/**
	 * The default widget options
	 *
	 * @return array The default options
	 */
	private function default_options() {
		return array(
			'title'               => __('Random Quote', 'quotes-collection'), 
			'show_author'         => 1,
			'show_source'         => 0, 
			'ajax_refresh'        => 1,
			'auto_refresh'        => 0,
			'random_refresh'      => 1,
			'refresh_interval'    => 5,
			'tags'                => '',
			'char_limit'          => 500,
		);
	}


}

?>