<?php

global $wp_query;
$id_category_product = caia_get_option('id_category_product');
$id_category_product = explode(',', $id_category_product);
$sub_cats = [];

foreach( $id_category_product as $id ){
    $children = get_term_children( $id, 'category' );
    $sub_cats = array_merge( $sub_cats, $children );
}

$id_category_product = array_merge( $id_category_product, $sub_cats );

$args = array_merge( 
    $wp_query->query, 
    array( 
        'post_type' => 'post',
        'posts_per_page' => 12,
        'post_status' => 'publish',
        'category__not_in' => $id_category_product 
    ) 
);

query_posts( $args );

// Thêm class body riêng
add_filter( 'body_class', function( $classes ) {
    $classes[] = 'class-new';
    return $classes;
});

// Xóa các phần thừa
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Đưa ảnh lên trước tiêu đề
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );

// Xóa "Read more"
remove_filter( 'excerpt_more', 'be_more_link' );
remove_filter( 'get_the_content_more_link', 'be_more_link' );
remove_filter( 'the_content_more_link', 'be_more_link' );

// Thêm banner đầu trang
add_action('genesis_after_header', 'add_page_banner');
function add_page_banner() {
    global $post;

    echo '<div class="content-hero section">';
    echo '  <div class="hero-image">';

    if ( is_search() && is_active_sidebar('search-banner') ) {
        dynamic_sidebar('search-banner');
    } else {
        $images = function_exists('rwmb_meta') ? rwmb_meta('anh_banner', ['size' => 'full']) : [];
        if ( ! empty($images) ) {
            foreach ( $images as $image ) {
                echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '">';
            }
        }
    }

    echo '  </div>';
    echo '  <div class="content-breadcrumb">';
    echo '      <h2 class="title">' . ( is_search() ? 'Kết quả tìm kiếm' : get_the_title() ) . '</h2>';
    echo          do_shortcode('[breadcrumb]');
    echo '  </div>';
    echo '</div>';
}

// Hiển thị tiêu đề + excerpt cùng khối
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
add_action( 'genesis_entry_content', 'custom_post_title_excerpt' );

function custom_post_title_excerpt() {
    echo '<div class="post-info-block">';
    echo '<h2 class="entry-title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
    the_excerpt();
    echo '</div>';
}
add_action ('genesis_after_content_sidebar_wrap' , 'add_banner_before_footer');
function add_banner_before_footer() {
     	if( is_active_sidebar( 'content-anhhero' ) ){
		echo '<div  class="content-anhhero">';
			dynamic_sidebar( 'Toàn bộ - Ảnh nền trước chân trang' );
		echo '</div>';
}
}

genesis();
