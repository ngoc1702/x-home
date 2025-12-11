<?php
/*
  Plugin Name: Caldera Forms
  Plugin URI: https://calderawp.com/caldera-forms/
  Description: Easy to use, grid based responsive form builder for creating simple to complex forms fixed by Caia.
  Author: David Cramer for CalderaWP LLC
  Version: 4.4.1
  Author URI: https://calderawp.com
  Text Domain: caldera-forms
  GitHub Plugin URI: https://github.com/CalderaWP/Caldera-Forms/
  GitHub Branch:     current-stable
 */

/*
Change log:
	- 29/11/21: bổ sung hỗ trợ giá trị mặc định {current_url}
	- 07/02/22: xử lý để {$current_url} hiển thị query_string
	- 4/4/24: fix lỗi edit form với PHP8
	- 23/4/24: fix lỗi replace %2F ở cookie _landing_page
	- 3/5/24: fix lỗi variable IP lấy ko đúng ở core.php
*/


//initilize plugin

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('CFCORE_PATH', plugin_dir_path(__FILE__));
define('CFCORE_URL', plugin_dir_url(__FILE__));
define('CFCORE_VER', '1.4.1');
define('CAIA_CALDERA_VER', '4.5');
define('CFCORE_EXTEND_URL', 'https://api.calderaforms.com/1.0/');
define('CFCORE_BASENAME', plugin_basename( __FILE__ ));

/**
 * Caldera Forms DB version
 *
 * @since 1.3.4
 *
 * PLEASE keep this an integer
 */
define( 'CF_DB', 4 );
$caia_caldera_add_script_current_url = false;

// init internals of CF
include_once CFCORE_PATH . 'classes/core.php'; // neeeds the core at the very least before plugins loaded
add_action( 'init', array( 'Caldera_Forms', 'init_cf_internal' ) );
// table builder
register_activation_hook( __FILE__, array( 'Caldera_Forms', 'activate_caldera_forms' ) );

// load system
add_action( 'plugins_loaded', 'caldera_forms_load', 0 );
function caldera_forms_load(){

	include_once CFCORE_PATH . 'classes/autoloader.php';
	include_once CFCORE_PATH . 'classes/widget.php';
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_DB', CFCORE_PATH . 'classes/db' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Entry', CFCORE_PATH . 'classes/entry' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Email', CFCORE_PATH . 'classes/email' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Processor_Interface', CFCORE_PATH . 'processors/classes/interfaces' );
	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms_Processor', CFCORE_PATH . 'processors/classes' );

	Caldera_Forms_Autoloader::add_root( 'Caldera_Forms', CFCORE_PATH . 'classes' );
	Caldera_Forms_Autoloader::register();

	// includes
	include_once CFCORE_PATH . 'includes/ajax.php';
	include_once CFCORE_PATH . 'includes/field_processors.php';
	include_once CFCORE_PATH . 'includes/custom_field_class.php';
	include_once CFCORE_PATH . 'includes/filter_addon_plugins.php';
	include_once CFCORE_PATH . 'includes/compat.php';
	include_once CFCORE_PATH . 'processors/functions.php';

	/**
	 * Runs after all of the includes and autoload setup is done in Caldera Forms core
	 *
	 * @since 1.3.5.3
	 */
	do_action( 'caldera_forms_includes_complete' );

	// TuanNM
	add_action('caldera_forms_render_start', 'caia_cal_add_script_current_url', 10 , 1);	  

}

function caia_cal_add_script_current_url($form){
	global $caia_caldera_add_script_current_url;
	if (!$caia_caldera_add_script_current_url){
		$caia_caldera_add_script_current_url = true;
		add_action('wp_footer', 'caia_cal_add_script_current_url_footer', 10, 1);
	}		
}
function caia_cal_add_script_current_url_footer(){	
	?>
	<script>setTimeout(function() {	
		jQuery("form.caldera_forms_form input.form-control[value='~current_url~']").attr('value', window.location.href);		
		jQuery("form.caldera_forms_form input.form-control[value='~cookie:source~']").attr('value', getCookie('_source'));		
		jQuery("form.caldera_forms_form input.form-control[value='~cookie:landing_page~']").attr('value', getCookie('_landing_page').replaceAll('%2F', '/'));
		}, 1500);
	</script>
	<?php
}

add_action( 'plugins_loaded', array( 'Caldera_Forms', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'Caldera_Forms_Tracking', 'get_instance' ) );


// Admin & Admin Ajax stuff.
if ( is_admin() || defined( 'DOING_AJAX' ) ) {
	add_action( 'plugins_loaded', array( 'Caldera_Forms_Admin', 'get_instance' ) );
	add_action( 'plugins_loaded', array( 'Caldera_Forms_Support', 'get_instance' ) );
}
