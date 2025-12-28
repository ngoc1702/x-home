<?php
add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
  return 'full-width-content';
}

// Xóa post-info và post-meta của Genesis
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Xóa social share và rating mặc định
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating(){
    global $caia_rating, $caia_social;

    $star_pri = has_filter( 'the_content', array($caia_rating, 'add_rating_content_bottom'));
    if ( $star_pri !== false ){
        remove_filter('the_content', array($caia_rating, 'add_rating_content_bottom'), $star_pri);
    }

    $social_pri = has_filter( 'the_content', array($caia_social, 'add_native_share_button_at_bottom'));
    if ( $social_pri !== false ){
        remove_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'), $social_pri);
    }
}


//add_action('genesis_entry_footer', 'caia_add_content_post_meta', 1);
function caia_add_content_post_meta(){
    echo '<div class="content-info-meta section">';
        echo '<div class="rating-social section">';
            echo do_shortcode('[caia_rating]');
            echo do_shortcode('[caia_social_share]');
        echo '</div>';
    echo '</div>';
}


// Bỏ title/content mặc định để tự dựng layout
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

// Render layout flex + content + gallery (trong bài viết)
add_action( 'genesis_entry_content', 'adsdigi_project_hero_and_gallery', 12 );
function adsdigi_project_hero_and_gallery() {

    // HERO: Title trái - Content phải
    echo '<section class="project-hero-flex">';

        echo '<div class="project-hero-title">';
            echo '<h1 class="project-title-text">' . esc_html( get_the_title() ) . '</h1>';
			if ( function_exists('rwmb_meta') ) {
        $diachi = rwmb_meta( 'diachi' );

        if ( ! empty( $diachi ) ) {
            echo '<div class="project-address">';
                echo '<i class="fa-solid fa-location-dot" aria-hidden="true"></i>';
                echo '<span>' . esc_html( $diachi ) . '</span>';
            echo '</div>';
        }
    }
        echo '</div>';

        echo '<div class="project-hero-content">';
            // in nội dung bài viết (áp filter đầy đủ)
            genesis_do_post_content();
        echo '</div>';

    echo '</section>';

    // GALLERY: nằm dưới hero
    echo project_gallery_lightbox_html();
}

/**
 * Gallery lightbox (MetaBox: anh_project)
 * Trả về HTML để dễ chèn đúng vị trí.
 */
function project_gallery_lightbox_html() {
    if ( ! function_exists( 'rwmb_meta' ) ) return '';

    $post_id = get_queried_object_id();
    if ( ! $post_id ) $post_id = get_the_ID();
    if ( ! $post_id ) return '';

    $images = rwmb_meta( 'anh_project', [
        'object_type' => 'post',
        'size'        => 'full',
    ], $post_id );

    if ( empty( $images ) || ! is_array( $images ) ) return '';

    ob_start();

    echo '<div class="project-gallery">';
    foreach ( $images as $img ) {

        if ( is_array( $img ) ) {
            $full_url = $img['full_url'] ?? $img['url'] ?? '';
        } else {
            $full_url = wp_get_attachment_image_url( (int) $img, 'full' );
        }

        if ( ! $full_url ) continue;

        echo '<a href="' . esc_url( $full_url ) . '" class="swipebox" data-rel="lightbox-gallery-1">';
            echo '<img src="' . esc_url( $full_url ) . '" alt="">';
        echo '</a>';
    }
    echo '</div>';

    return ob_get_clean();
}

// Thêm bài viết liên quan
add_action('genesis_after_content_sidebar_wrap', 'caia_add_product_YARPP', 9);
function caia_add_product_YARPP()
{
    if (function_exists('yarpp_related')) {
        yarpp_related([
            'post_type' => 'project',
            'threshold' => 1,
            'template' => 'yarpp-template-project.php',
            'limit' => 3,
        ]);
    }
}


genesis();
