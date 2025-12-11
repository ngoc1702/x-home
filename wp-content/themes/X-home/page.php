<?php

// Setting bố cục
add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
	return 'content-sidebar';
}

// Xóa tiêu đề
remove_action('genesis_entry_header', 'genesis_do_post_title');

// Xóa social share
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating(){
	global $caia_rating;
	$star_pri = has_filter( 'the_content', array($caia_rating, 'add_rating_content_bottom'));
	if ($star_pri !== false){
		remove_filter('the_content', array($caia_rating, 'add_rating_content_bottom'), $star_pri);
	}

	global $caia_social;
	$social_pri = has_filter( 'the_content', array($caia_social, 'add_native_share_button_at_bottom'));
	if ($social_pri !== false){
		remove_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'), $social_pri);
	}
}

// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Thêm css
add_action('wp_footer', 'caia_add_style_css_breadcrum');
function caia_add_style_css_breadcrum(){
	$breadcrumb_image = rwmb_meta( 'breadcrumb_image', ['size' => 'full'] );
	if( !empty( $breadcrumb_image ) ){
		?>
		<style type="text/css">
			.breadcrumb{
				background-image: url(<?php echo $breadcrumb_image['url']; ?>);
			}
		</style>
		<?php
	}
}

// Thêm thẻ div
add_action( 'genesis_before_footer', 'caia_add_stop', 1 );
function caia_add_stop(){
	echo '<div id="stop" class="section"></div>';
}


genesis();
