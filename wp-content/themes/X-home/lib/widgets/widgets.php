<?php
/**
 * Handles including the widget class files, and registering the widgets in
 * WordPress.
 *
 * @category CAIA
 * @package  Widgets
 * @author   HoangLT
 */

// Thêm khung soạn thảo cho widget Image upload
add_action( 'admin_enqueue_scripts', 'custom_widgets_widget_editor_script' );
function custom_widgets_widget_editor_script() 
{
    global $pagenow;
    if ( 'widgets.php' === $pagenow || 'customize.php' === $pagenow ) {
        wp_enqueue_editor();
        wp_enqueue_script('wp-editor-widgets', CHILD_URL.'/lib/widgets/wp-editor-widgets.js', array('jquery'));
    }
}

global $_caia_widget_code;
$_caia_widget_code = array();

function caia_register_widget_code($code)
{
	global $_caia_widget_code;
	if(is_string($code) && !empty($code)){
		$code = strtolower($code);
		$code = str_replace(' ', '-', $code);
		if(!in_array($code, $_caia_widget_code)){
			$_caia_widget_code[] = $code;
		}
	}
}

function caia_dropdown_widget_code($input)
{
	global $_caia_widget_code;
	extract($input);
	?>
	<select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
		<option value="" <?php selected( '', $selected ); ?>><?php _e('None', 'caia'); ?></option>
    	<?php foreach( $_caia_widget_code as $code ) : ?>
        	<option value="<?php echo $code; ?>" <?php selected( $code, $selected ); ?>><?php echo $code; ?></option>
		<?php endforeach; ?>
    </select>
	<?php
}

require( CAIA_WIDGETS_DIR . '/code-widget.php' );
require( CAIA_WIDGETS_DIR . '/post-list-widget.php' );
require( CAIA_WIDGETS_DIR . '/image-upload-widget.php' );

//Xóa và đăng ký widget
remove_action( 'widgets_init', 'genesis_load_widgets' );
add_action( 'widgets_init', 'caia_load_widgets' );
function caia_load_widgets()
{

	register_widget( 'Caia_Code_Widget' );
	register_widget( 'Caia_Post_List_Widget' );
	register_widget( 'Images_upload_Widget' );

	if(is_admin())
	{
		unregister_widget('Genesis_Featured_Post');
		unregister_widget('Genesis_Featured_Page');
		unregister_widget('Genesis_User_Profile_Widget');
		unregister_widget('WP_Widget_Recent_Posts');
		unregister_widget('WP_Widget_Recent_Comments');
		unregister_widget('WP_Widget_Meta');
		unregister_widget('WP_Widget_Archives');
		unregister_widget('WP_Widget_RSS');
		unregister_widget('WP_Widget_Pages');
		unregister_widget('WP_Widget_Media_Video');
		unregister_widget('WP_Widget_Media_Gallery');
		unregister_widget('WP_Widget_Media_Audio');
		unregister_widget('WP_Widget_Calendar');
		unregister_widget('WP_Widget_Block');
		unregister_widget('WP_Widget_Custom_HTML');
		unregister_widget('WP_Widget_Tag_Cloud');
		unregister_widget('WP_Widget_Categories');
	}
}
