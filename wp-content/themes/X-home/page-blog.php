<?php

/* Template Name: Trang blog */

add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
  return 'full-width-content';
}


// Xóa tiêu đề
//remove_action('genesis_entry_header', 'genesis_do_post_title');

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

//Đưa ảnh lên trước tiêu đề
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );

// remove_action( 'genesis_after_header', 'genesis_do_breadcrumbs',9);
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action ('genesis_loop' , 'add_page_blog');
add_action ('genesis_after_header' , 'add_page_banner');


remove_action('genesis_before_footer','caia_add_content_after_footer',8);

function add_page_banner() {
    global $post;

    $images = rwmb_meta( 'anh_banner', ['size' => 'full'] ); 

    echo '<div class="content-hero section">';
    echo '  <div class="hero-image">';

    if ( ! empty( $images ) ) {
        foreach ( $images as $image ) {
            echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '">';
        }
    }

    echo '  </div>';
    echo '<div class="content-breadcrumb">';
    echo '  <h2 class="title">' . get_the_title() . '</h2>';

    echo do_shortcode('[breadcrumb]');

    echo '</div>';
    echo '</div>';
}

function add_page_blog() {
	

    // Xác định trang hiện tại
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Truy vấn các bài viết thuộc post type = 'post'
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'paged'          => $paged,
    );

    $query = new WP_Query($args);

    echo '<div class="blog-wrapper">';

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>

            <article class="blog-item">
                <a href="<?php the_permalink(); ?>" class="blog-thumb">
                    <?php if (has_post_thumbnail()) {
                        the_post_thumbnail('medium');
                    } ?>
                </a>
                <div class="blog-info">
                <h2 class="blog-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <div class="blog-meta">
    <i class="fa-solid fa-calendar-days"></i>
    <span class="blog-date"><?php echo get_the_date( 'd/m/Y' ); ?></span>
</div>
                <div class="blog-excerpt">
  <?php echo wp_trim_words( get_the_excerpt(), 40, '...' ); ?>
</div>

                </div>
            </article>

        <?php endwhile;

        // Phân trang
        echo '<div class="pagination">';
        echo paginate_links(array(
            'total'   => $query->max_num_pages,
            'current' => $paged,
            'prev_text' => '«',
            'next_text' => '»',
        ));
        echo '</div>';

    else :
        echo '<p>Không có bài viết nào.</p>';
    endif;

    echo '</div>';

    // Reset lại query chính
    wp_reset_postdata();
}



// add_action('genesis_loop', 'add_page_info');
// function add_page_info(){
// global $post;
// $tieude = get_post_meta($post->ID, 'tieude', true);
// $info = rwmb_meta('khung_hotro');

// echo '<div class="aside-box section">
// <div class="aside-info">';
// echo '<h2 class="widgettitle">'. $tieude .'</h2>';
// echo '<div class="nd">';
// 		foreach ($info as $value) {
// 			echo '<div class="box-info">';
// 			echo do_shortcode(wpautop($value['nd']));
// 			echo '</div>';
// 		}
// 		echo '</div>';
// echo '<div></div>';
// }

add_action ('genesis_after_content_sidebar_wrap' , 'add_banner_before_footer');
function add_banner_before_footer() {
     	if( is_active_sidebar( 'content-anhhero' ) ){
		echo '<div  class="content-anhhero">';
			dynamic_sidebar( 'Toàn bộ - Ảnh nền trước chân trang' );
		echo '</div>';
}
}

// Mobile
if (wp_is_mobile() ){

}

genesis();
