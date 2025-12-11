<?php

//Xóa tiêu đề chuyên mục
remove_action( 'genesis_before_loop', 'caia_archive_heading', 5 );

// Đặt class riêng
add_filter( 'body_class','wp_body_classes_new' );
function wp_body_classes_new( $classes ) {
    $classes[] = 'class-new';
    return $classes;
}	

// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

//Đưa ảnh lên trước tiêu đề
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );

//Sửa chữ read-more
add_filter( 'excerpt_more', 'be_more_link' );
add_filter( 'get_the_content_more_link', 'be_more_link' );
add_filter( 'the_content_more_link', 'be_more_link' );
function be_more_link($more_link) {
	$lang = get_bloginfo('language');
	if($lang == 'vi'){
		return sprintf('...<a href="%s" class="more-link">%s</a>', get_permalink(), 'Tìm hiểu thêm');
	}else{
		return sprintf('...<a href="%s" class="more-link">%s</a>', get_permalink(), 'See more');
	}
}

// Thêm css
add_action('wp_footer', 'caia_add_style_css_breadcrum');
function caia_add_style_css_breadcrum(){
	$current_category = get_queried_object();
	$breadcrumb_image = get_term_meta( $current_category->term_id, 'breadcrumb_image', true );
	$breadcrumb_image = wp_get_attachment_image_src( $breadcrumb_image, 'full' );
	if( !empty( $breadcrumb_image ) ){
		?>
		<style type="text/css">
			.breadcrumb{
				background-image: url(<?php echo $breadcrumb_image[0]; ?>);
			}
		</style>
		<?php
	}
}