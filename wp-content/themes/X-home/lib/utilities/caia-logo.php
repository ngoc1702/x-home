<?php

define('CAIA_LOGO_VERSION', '2.0');

/*
24/03/2023: 
- Thay ảnh logo thành dạng Image có đầy đủ title, alt lấy theo tiêu đề website
*/

// Thêm setting bật tính năng lên đầu trang
add_action( 'caia_settings_metaboxes', 'caiatn_add_theme_settings_boxes_logo' );
function caiatn_add_theme_settings_boxes_logo( $pagehook ){
	add_meta_box( 'caia-settings-logo', __( 'Thêm Logo', 'caia' ), 'caia_add_button_logo', $pagehook, 'main' );
}

function caia_add_button_logo(){
	$logo_id = caia_get_option( 'edit_logo' );
	$image = wp_get_attachment_image_src( $logo_id );
	if ( !empty( $image ) ) {
		echo '<a href="#" class="caia-upl"><img src="' . $image[0] . '" style="width: 100px;height: 100px;object-fit: contain;"/></a><br>';
		echo '<a href="#" class="caia-rmv">Xoá ảnh</a>';
		?><input class="caia-logo" type="hidden" name="<?php echo CAIA_SETTINGS_FIELD; ?>[edit_logo]" value="<?php echo $logo_id; ?>"/><?php
	}else{
		echo '<a href="#" class="caia-upl">Thêm ảnh</a>';
		echo '<a href="#" class="caia-rmv" style="display:none">Xoá ảnh</a>';
		?><input class="caia-logo" type="hidden" name="<?php echo CAIA_SETTINGS_FIELD; ?>[edit_logo]" value="<?php echo caia_option( 'edit_logo' ); ?>"/><?php		
	}
}

add_action( 'admin_enqueue_scripts', 'caia_include_js' );
function caia_include_js() {
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 	wp_enqueue_script( 'caiauploadlogo', home_url() . '/wp-content/themes/caia/lib/utilities/js/edit-logo.js', array( 'jquery' ) );
}

add_action( 'wp_head', 'caia_custom_logo_css' );
function caia_custom_logo_css(){
	$logo_id = caia_get_option( 'edit_logo' );
	$image = wp_get_attachment_image_src( $logo_id, 'full' );
	if( !empty($image) ){
		echo '<style>.site-title a,.site-title a:hover{background: unset;text-indent: inherit;display: grid;align-items: center;}</style>';
	}
}

add_filter('genesis_seo_title', 'caia_site_title' );
function caia_site_title( $title ) {
	$logo_id = caia_get_option( 'edit_logo' );
	$image = wp_get_attachment_image_src( $logo_id, 'full' );
	$site_title = get_bloginfo( 'name' );

	if( is_home() ){
		$tag = 'h1';
	}else{
		$tag = 'p';
	}	

	if( !empty($image) ){
		$custom_title = '<img src="'.$image[0].'" alt="'.$site_title.'" title ="'.$site_title.'" >';
	}else{
		$custom_title = $site_title;
	}

	$inside = sprintf( '<a href="%s" title="%s">%s</a>', trailingslashit( home_url() ), esc_attr( get_bloginfo( 'name' ) ), $custom_title );

	$title = sprintf ( '<%s class="site-title" itemprop="headline">%s</%s>', $tag, $inside, $tag );

	return $title;

}