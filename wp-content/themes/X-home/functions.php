<?php
/**
 * Custom hooks and functions.
 *
 * @category CAIA
 * @package  Functions
 * @author   HoangLT
 */

// Block edit theme and plugin in daskboard.
define('DISALLOW_FILE_EDIT',true);

// Tắt tự động cập nhật
if(!defined( 'AUTOMATIC_UPDATER_DISABLED')){
	define( 'AUTOMATIC_UPDATER_DISABLED', true);
}

// Init theme
require_once( TEMPLATEPATH . '/lib/init.php' );
require( CHILD_DIR . '/lib/init.php' );

/** Load custom functions */
if ( file_exists( CAIA_CUSTOM_DIR . '/functions.php' ) ){
	require( CAIA_CUSTOM_DIR . '/functions.php' );
}

// Đặt mặc định đường dẫn tĩnh
add_action( 'init', 'default_permalinks' );
function default_permalinks(){
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%.html' );
}

// Thêm thẻ meta responsive
add_action('wp_head','caia_add_meta_responsive');
function caia_add_meta_responsive(){
	echo "<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' />";
	// echo '<meta name="viewport" content="width=device-width, initial-scale=1.0>';
}

// function add_aos_library() {
//     // CSS AOS
//     wp_enqueue_style('aos-css', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css');

//     // JS AOS
//     wp_enqueue_script('aos-js', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js', array(), null, true);

//     // Khởi tạo AOS
//     // wp_add_inline_script('aos-js', 'AOS.init();');

// 	wp_add_inline_script('aos-js', '
//     AOS.init({
//         duration: 800,
//         once: true
//     });
// ');
// }
// add_action('wp_enqueue_scripts', 'add_aos_library');


