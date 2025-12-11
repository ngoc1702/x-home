<?php

/**
 * Hook widget class
 *
 * @since 2.0.0
 */
class Caia_Code_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'caia_code_widget', 'description' => __('Hỗ trợ thêm shortcode'));
		$control_ops = array('width' => 400, 'height' => 350);
		parent::__construct('code_widget', __('CAIA - Code'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {

		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$code = empty( $instance['code'] ) ? '' : $instance['code'];
		$parameter = empty( $instance['parameter'] ) ? '' : $instance['parameter'];

		// preprocess before widget		  
		/* Add the width from $widget_width to the class from the $before widget */
	  	if($code){	
			$tmp_str = 'class="';
			$pos = strpos($before_widget, $tmp_str);			
			if ($pos) {
			    $before_widget = substr_replace($before_widget, $tmp_str . 'code-' . $code . ' ', $pos, strlen($tmp_str));
			    
			}
		}

		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } 
		do_action( 'caia_code_widget_do_content', $code, $parameter );	
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['code'] = $new_instance['code'];
		$instance['parameter'] = $new_instance['parameter'];
		
	
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'code' => '' ) );
		$title = strip_tags($instance['title']);
		$code = $instance['code'];
		$parameter = $instance['parameter'];
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
        	<label>
        		<?php 
				caia_dropdown_widget_code(array('id' => $this->get_field_id( 'code' ), 
													'name' => $this->get_field_name( 'code' ),
													'selected' => $code));
											
				_e( 'Widget code (only coder use)', 'caia' ); ?>
				
			</label>
        </p>
        <p><label for="<?php echo $this->get_field_id('parameter'); ?>"><?php _e('Input:'); ?></label>
		<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id('parameter'); ?>" name="<?php echo $this->get_field_name('parameter'); ?>"><?php echo esc_html($parameter); ?></textarea>
		</p>
		
	<?php
	}
}


// bo sung tinh nang do_shortcode cho child theme
caia_register_widget_code('shortcode_widget'); // dky 1 widget code mới
add_action('caia_code_widget_do_content', 'caia_hook_shortcode_widget', 10, 2);	
function caia_hook_shortcode_widget($code, $parameter)
{
	if($code === 'shortcode_widget'){		
		echo do_shortcode($parameter);
	}
}