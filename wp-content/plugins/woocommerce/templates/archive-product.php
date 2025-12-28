<?php
add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
  return 'full-width-content';
}

defined( 'ABSPATH' ) || exit;

add_action('genesis_after_header', 'add_page_banner');
function add_page_banner() {
    global $post;

    echo '<section class="content-hero section">';
    echo '  <div class="hero-image">';

    // --- Nếu là trang cửa hàng hoặc danh mục sản phẩm ---
    if ( ( is_shop() || is_product_taxonomy() ) && is_active_sidebar('shop-sidebar') ) {
        dynamic_sidebar('shop-sidebar');
    }
    // --- Nếu là trang tìm kiếm ---
    elseif ( is_search() && is_active_sidebar('shop-sidebar') ) {
        dynamic_sidebar('shop-sidebar');
    }
    // --- Các trang khác (có Meta Box banner) ---
    else {
        $images = function_exists('rwmb_meta') ? rwmb_meta('anh_banner', ['size' => 'full']) : [];
        if ( ! empty($images) ) {
            foreach ( $images as $image ) {
                echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '">';
            }
        } else {
            // Ảnh fallback nếu chưa có banner
            echo '<img src="' . esc_url( get_stylesheet_directory_uri() . '/images/default-banner.jpg' ) . '" alt="Banner">';
        }
    }

    echo '  </div>'; // end hero-image

    // --- Breadcrumb + tiêu đề ---
    echo '  <div class="content-breadcrumb">';
    echo '      <div class="breadcrumb-inner">';
    
    if ( is_shop() ) {
        echo '          <h2 class="title">Sản phẩm</h2>';
    } elseif ( is_product_taxonomy() ) {
        echo '          <h2 class="title">' . single_term_title('', false) . '</h2>';
    } elseif ( is_search() ) {
        echo '          <h2 class="title">Kết quả tìm kiếm</h2>';
    } else {
        echo '          <h2 class="title">' . get_the_title() . '</h2>';
    }

    echo              do_shortcode('[breadcrumb]');
    echo '      </div>';
    echo '  </div>'; // end content-breadcrumb

    echo '</section>'; // end content-hero
}


get_header( 'shop' ); ?>
<div class="shop-wrapper">
    <aside class="shop-sidebar">
        <?php
        // Sidebar filter custom
        if ( is_active_sidebar('content-filter') ) {
            dynamic_sidebar('content-filter');
        } else {
            the_widget( 'WC_Widget_Price_Filter' );
        }
        ?>
    </aside>

    <div class="shop-products">
        <?php
        do_action( 'woocommerce_before_main_content' );

        if ( woocommerce_product_loop() ) {

            do_action( 'woocommerce_before_shop_loop' );

            woocommerce_product_loop_start();

            if ( wc_get_loop_prop( 'total' ) ) {
                while ( have_posts() ) {
                    the_post();

                    do_action( 'woocommerce_shop_loop' );

                    wc_get_template_part( 'content', 'product' );
                }
            }

            woocommerce_product_loop_end();

            do_action( 'woocommerce_after_shop_loop' );
        } else {
            do_action( 'woocommerce_no_products_found' );
        }

        do_action( 'woocommerce_after_main_content' );
        ?>
    </div>
</div>

<?php get_footer( 'shop' ); ?>
