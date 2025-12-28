<?php
/* Template Name: Trang Dịch vụ */

// Ép layout full width cho trang này
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

// Xóa social share
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating() {
    global $caia_rating;
    $star_pri = has_filter('the_content', array($caia_rating, 'add_rating_content_bottom'));
    if ($star_pri !== false) {
        remove_filter('the_content', array($caia_rating, 'add_rating_content_bottom'), $star_pri);
    }

    global $caia_social;
    $social_pri = has_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'));
    if ($social_pri !== false) {
        remove_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'), $social_pri);
    }
}

// Xóa post-info và post-meta
remove_action('genesis_entry_header', 'genesis_post_info', 12);
remove_action('genesis_entry_footer', 'genesis_post_meta');

// Đưa ảnh lên trước tiêu đề (KHÔNG còn tác dụng nhiều vì mình bỏ genesis_do_loop)
remove_action('genesis_entry_content', 'genesis_do_post_image', 8);
add_action('genesis_entry_header', 'genesis_do_post_image', 1);

// Bỏ loop mặc định, dùng loop custom
remove_action('genesis_loop', 'genesis_do_loop');



// Bỏ content after footer
remove_action('genesis_before_footer', 'caia_add_content_after_footer', 8);

add_action('genesis_after_header', 'add_page_banner',);
function add_page_banner()
{
    global $post;

    $images = rwmb_meta('anh_banner', ['size' => 'full']);

    echo '<div class="content-hero section">';
    echo '  <div class="hero-image">';

    if (! empty($images)) {
        foreach ($images as $image) {
            echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '">';
        }
    }

    echo '  </div>';
    echo '<div class="content-breadcrumb">';
    echo '  <h2 class="title">' . get_the_title() . '</h2>';

    echo do_shortcode('[breadcrumb]');

    echo '</div>';
    echo '</div>';
}





add_action('genesis_before_footer', 'caia_project_archive_loop', 7);
function caia_project_archive_loop() {
    	if (is_active_sidebar('content-dichvu')) {
		echo '<div  class="content-dichvu section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Dịch vụ');
		echo '</div></div>';
	}

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    $args  = [
        'post_type'      => 'project',
        'posts_per_page' => 12,
        'paged'          => $paged,
    ];

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {

        echo '<div class="page_congtrinh section"><div class="wrap">
        <h2>Công trình nổi bật</h2>
        <div class="main-posts">';

        while ( $query->have_posts() ) {
            $query->the_post();

            echo '<div class="project">';

            // Ảnh đại diện
            if ( has_post_thumbnail() ) {
                echo '<div class="thumb">';
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail( 'large', [ 'class' => 'project-thumb' ] );
                echo '</a>';
                echo '</div>';
            }

            echo '<div class="list-info">';
            echo '<h3 class="title"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            $diachi = get_post_meta( get_the_ID(), 'diachi', true );
            if ( ! empty( $diachi ) ) {
                echo '<p class="project-diachi">
                            <i class="fas fa-map-marker-alt"></i> ' . esc_html( $diachi ) . '
                          </p>';
            }

            echo '</div>'; 
            echo '</div>'; 
        }

        echo '</div></div>'; 
         echo '</div>';
        //  echo '<div class="pagination">';
        // echo paginate_links( [
        //     'total'     => $query->max_num_pages,
        //     'current'   => $paged,
        //     'type'      => 'list',
        //     'prev_next' => false,
        // ] );
        // echo '</div></div>';
        wp_reset_postdata();
    }

    	if (is_active_sidebar('content-dangky')) {
		echo '<div  class="content-dangky section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Đăng ký tư vấn');
		echo '</div></div>';
	}
}

// Mobile
if ( wp_is_mobile() ) {
  
}

genesis();