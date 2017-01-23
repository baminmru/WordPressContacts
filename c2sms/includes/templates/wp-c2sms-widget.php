<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wp-c2sms' ); ?></label> 
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
</p>

<p>
	<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'wp-c2sms' ); ?></label> 
	<textarea class="widefat" id="<?php echo $this->get_field_id( 'description' ); ?>" name="<?php echo $this->get_field_name( 'description' ); ?>"><?php echo esc_attr( $description ); ?></textarea>
	<p class="description"><?php _e( 'HTML code is valid.', 'wp-c2sms' ); ?></p>
</p>

