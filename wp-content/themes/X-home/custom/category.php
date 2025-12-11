<?php

add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
  return 'full-width-content';
}

//Xóa tiêu đề chuyên mục
remove_action( 'genesis_before_loop', 'caia_archive_heading', 5 );
add_action( 'genesis_before_content', 'caia_archive_heading', 5 );

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
		return sprintf('...<a href="%s" class="more-link">%s</a>', get_permalink(), 'Đọc thêm');
	}else{
		return sprintf('...<a href="%s" class="more-link">%s</a>', get_permalink(), 'See more');
	}
}
