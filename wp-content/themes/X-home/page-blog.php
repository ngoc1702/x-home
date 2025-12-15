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

remove_action( 'genesis_loop', 'genesis_do_loop' );
function add_page_blog() {

    	if( is_active_sidebar( 'content-posts' ) ){
		echo '<div class="content-posts section"><div class="wrap">';
			dynamic_sidebar( 'Tin tức - Bài viết nổi bật' );
		echo '</div></div>';
	}


    // Lấy tất cả danh mục có bài viết
    $categories = get_categories(array(
        'hide_empty' => true
    ));

    if (empty($categories)) return;

    foreach ($categories as $category) {

        // Query bài viết theo từng danh mục
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => 6,
            'cat'            => $category->term_id,
            'post_status'    => 'publish'
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {

            echo '<section class="blog-category">';

                // Tiêu đề danh mục
                echo '<h2 class="category-title">' . esc_html($category->name) . '</h2>';

                echo '<div class="blog-grid">';

                while ($query->have_posts()) {
                    $query->the_post(); ?>

                    <article class="blog-item">
                        <a href="<?php the_permalink(); ?>">
                            <div class="thumb">
                                <?php 
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                }
                                ?>
                            </div>

                            <h3 class="title"><?php the_title(); ?></h3>
                        </a>
                    </article>

                <?php }

                echo '</div>'; 
            echo '</section>';

            wp_reset_postdata();
        }
    }
}




// Mobile
if (wp_is_mobile() ){

}

genesis();
