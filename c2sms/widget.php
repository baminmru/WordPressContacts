<?php
/**
 * Adds BAMI_Widget widget.
 */
class WPC2SMS_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'bami_widget',
			__( 'Contact to SMS Form', 'wp-c2sms' ),
			array( 'description' => __( 'Contact to SMS Widget', 'wp-c2sms' ), )
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
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		
		wp_contacts_by_sms($instance['description'], $instance['show_group']);
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Site contacts by SMS', 'wp-c2sms' );
		$description = ! empty( $instance['description'] ) ? $instance['description'] : '';
		include dirname( __FILE__ ) . "/includes/templates/wp-c2sms-widget.php"; 
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
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['description'] = ( ! empty( $new_instance['description'] ) ) ? $new_instance['description'] : '';
		return $instance;
	}

}

// register WPC2SMS_Widget widget
function register_WPC2SMS_Widget() {
    register_widget( 'WPC2SMS_Widget' );
}
add_action( 'widgets_init', 'register_WPC2SMS_Widget' );