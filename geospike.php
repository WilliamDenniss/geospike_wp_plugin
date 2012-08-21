<?php
/*
Plugin Name: Geospike
Plugin URI: http://www.geospike.com
Description: Adds a Geospike feed widget and travel map widget.
Version: 1.0
Author: Geospike
Author URI: http://www.geospike.com
License: GPL2
*/

/*  Copyright 2012  Geospike Pty Ltd  (email : support@geospike.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once('simplepie/simplepie_1.3.mini.php');

class Geospike_Travelmap_Widget extends WP_Widget{
	
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'geospike_travelmap_widget', // Base ID
			'Geospike Travel Map', // Name
			array( 'description' => __( 'A widget that displays a Geospike user\'s travel map', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		$travelmap_url = 'http://gs-cdn.spikeimg.com/' . $instance['username'] . '/travelmap';
		$travelmap_url_small = $travelmap_url . '?width=250&marker_scale=0.4';
		echo '<a href="' . $travelmap_url_small . '"><img src="' . $travelmap_url_small . '"></img></a>';
		echo $after_widget;
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
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Geospike Travel Map', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		
		<?php 
		
		if ( isset( $instance[ 'username' ] ) ) {
			$username = $instance[ 'username' ];
		}
		else {
			$username = __( '', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Geospike Username:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" />
		</p>
		<?php
	}
}

class Geospike_Feed_Widget extends WP_Widget {


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'geospike_feed_widget', // Base ID
			'Geospike Feed', // Name
			array( 'description' => __( 'A widget that displays a Geospike user\'s feed', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		$feed = new SimplePie();

		// Set which feed to process.
		$feed->set_feed_url('http://geospike.com/Paludis');
		//$feed->set_feed_url('http://geospike.com/' . $instance['username']);
		
		// Run SimplePie.
		$feed->init();
		
		// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
		$feed->handle_content_type();
		
		/*
		Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
		*/

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		echo __( 'Hello, World!', 'text_domain' );
		
		foreach ($feed->get_items() as $item){
			echo '<div class="item">';
			echo '<h2><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></h2>';
			echo '<p>' . $item->get_description() . '</p>';
			echo '</div';
		}
		
		echo $after_widget;
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
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

}

function geospike_register_widgets() {
	register_widget( 'Geospike_Feed_Widget' );
	register_widget( 'Geospike_Travelmap_Widget' );
}

add_action( 'widgets_init', 'geospike_register_widgets' );

?>