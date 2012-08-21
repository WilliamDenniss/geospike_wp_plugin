<?php
/*
Plugin Name: Geospike
Plugin URI: http://geospike.com
Description: Adds a Geospike feed widget and travel map widget.
Version: 1.0
Author: Geospike
Author URI: http://geospike.com
License: BSD 3
*/

/*  
Copyright (c) 2012, Geospike Pty Ltd
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
Neither the name of the Geospike Pty Ltd nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
		if ( ! empty( $title ) ){
			echo $before_title . $title . $after_title;
		}
			
		if (strlen($instance['username']) > 0) {
			$travelmap_url = 'http://gs-cdn.spikeimg.com/' . $instance['username'] . '/travelmap';
			$travelmap_url_small = $travelmap_url . '?width=300&marker_scale=0.5';
			echo '<a href="http://geospike.com/' . $instance['username'] . '"><img class="geospike-travel-map" src="' . $travelmap_url_small . '"></img></a>';
		} else {
			echo '<i>Please set Geospike username in widget settings.</i>';
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
		
		echo $before_widget;
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		if (strlen($instance['username']) > 0) {
			$feed = new SimplePie();

			$feed->set_cache_location(ABSPATH.'/wp-content/cache');		// set the cache dir wp cache dir

			// Set which feed to process.
			$feed->set_feed_url('http://gs-cdn.spikeimg.com/' . $instance['username']);

			// Run SimplePie.
			$feed->init();

			// This makes sure that the content is sent to the browser as text/html and the UTF-8 character set (since we didn't change it).
			$feed->handle_content_type();

			/*
			Here, we'll loop through all of the items in the feed, and $item represents the current item in the loop.
			*/

			// show 5 latest spikes
			foreach ($feed->get_items(0, 5) as $item){

				$description = $item->get_description();
				$strlen_limit = 30;
				$truncated_description = (strlen($description) > $strlen_limit) ? substr($description,0,$strlen_limit - 3).'...' : $description;		// truncate the string if needed

				echo '<div class="geospike-feed-item">';
				echo '<h3><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></h3>';
				echo '<p>' . $description . '</p>';
				echo '</div>';
			}
		} else {
			echo '<i>Please set Geospike username in widget settings.</i>';
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
			$title = __( 'Geospike Feed', 'text_domain' );
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

function geospike_init(){
	wp_register_style( 'geospike_css', plugins_url('geospike.css',__FILE__ ) );
	wp_enqueue_style('geospike_css');
}

function geospike_register_widgets() {

	register_widget( 'Geospike_Feed_Widget' );
	register_widget( 'Geospike_Travelmap_Widget' );
}

add_action( 'widgets_init', 'geospike_register_widgets' );
add_action('init', 'geospike_init');

?>