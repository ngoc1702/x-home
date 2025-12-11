<?php

// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Xóa social share và rating mặc định
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

// Thêm thông tin dưới nội dung bài viết
add_action('genesis_entry_footer', 'caia_add_content_post_meta', 1);
function caia_add_content_post_meta(){
	echo '<div class="content-info-meta section">';
		echo '<div class="rating-social section">';
			echo do_shortcode('[caia_rating]');	
			echo do_shortcode('[caia_social_share]');
		echo '</div>';
	echo '</div>';
}

function add_post_thumbnail_to_content( $content ) {
    if ( is_singular('post') && has_post_thumbnail() && is_main_query() ) {
        $thumbnail = get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'post-thumbnail' ) );
        $content = $thumbnail . $content;
    }
    return $content;
}
add_filter( 'the_content', 'add_post_thumbnail_to_content' );


// Thêm bài viết liên quan
// add_action( 'genesis_before_sidebar_widget_area', 'caia_add_post_YARPP' );
// function caia_add_post_YARPP(){
// 	yarpp_related(
// 		array(
// 			'post_type' => 'post',
// 			'threshold' => 1,
// 			'template' => 'yarpp-template-post.php',
// 			'limit' => 5,
// 		)
// 	);
// }

genesis();