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
			$width = $instance['width'];
			$marker_scale = $instance['marker_scale'];
			$travelmap_url_small = $travelmap_url . '?width=' . $width . '&marker_scale=' . $marker_scale;
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
		
		$width = intval($new_instance['width']);
		if ($width <= 0 || $width > 1000){
			$width = 300;
		}
		$instance['width'] = $width;
		
		$marker_scale = floatval($new_instance['marker_scale']);
		if ($marker_scale <= 0 || $marker_scale > 3){
			$marker_scale = 0.5;
		}
		$instance['marker_scale'] = $marker_scale;
		
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
		
		if ( isset( $instance[ 'width' ] ) ) {
			$width = $instance[ 'width' ];
		}
		else {
			$width = 300;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Map Width:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $width ); ?>" />
		</p>
		
		
		<?php 
		
		if ( isset( $instance[ 'marker_scale' ] ) ) {
			$marker_scale = $instance[ 'marker_scale' ];
		}
		else {
			$marker_scale = 0.5;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'marker_scale' ); ?>"><?php _e( 'Flag Scale:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'marker_scale' ); ?>" name="<?php echo $this->get_field_name( 'marker_scale' ); ?>" type="text" value="<?php echo esc_attr( $marker_scale ); ?>" />
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
		include_once(ABSPATH . WPINC . '/feed.php');
		echo $before_widget;
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		if (strlen($instance['username']) > 0) {
			
			$feed = fetch_feed( 'http://gs-cdn.spikeimg.com/' . $instance['username'] );
			
			$limit = $instance[ 'limit' ];
			if ($limit <= 0) {
				$limit = 5;
			}
			
			if (!is_wp_error( $feed ) ){ // Checks that the object is created correctly 

			    // Build an array of all the items, starting with element 0 (first element).
			    $items = $feed->get_items(0, $limit); 

				foreach ($items as $item){

					$description = $item->get_description();
					
					// remove style attributes from html. ref: http://stackoverflow.com/a/5518159
					$description = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $description);		

					echo '<div class="geospike-feed-item">';
					echo '<h3><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></h3>';
					echo '<p>' . $description . '</p>';
					echo '</div>';
				}
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
		$limit = intval($new_instance['limit']);
		if ($limit <= 0){
			$limit = 5;
		} 
		$instance['limit'] = $limit;

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
		
		if ( isset( $instance[ 'limit' ] ) ) {
			$limit = $instance[ 'limit' ];
		}
		else {
			$limit = 5;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Number of spikes to show (max 10):' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="text" value="<?php echo esc_attr( $limit ); ?>" />
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