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
    echo '  </div>'; 
    echo '</section>'; 
}

add_action('genesis_after_header', 'add_catergory_product');
function add_catergory_product() {

	// Nếu bạn muốn hiện mọi trang thì bỏ điều kiện này
	if ( ! function_exists('is_woocommerce') || ! is_woocommerce() ) {
		return;
	}

	$cats = get_terms([
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,      
		'orderby'    => 'menu_order',
		'order'      => 'ASC',
	]);

	if ( is_wp_error($cats) || empty($cats) ) return;

	// Build tree: parent -> children
	$by_parent = [];
	foreach ($cats as $c) {
		$pid = (int) $c->parent;
		if (!isset($by_parent[$pid])) $by_parent[$pid] = [];
		$by_parent[$pid][] = $c;
	}

	// Active term nếu đang ở trang taxonomy
	$current_term_id = 0;
	if ( function_exists('is_product_taxonomy') && is_product_taxonomy() ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error($term) && ! empty($term->term_id) ) {
			$current_term_id = (int) $term->term_id;
		}
	}

	// Render item
	$render_item = function($cat, $depth = 0) use ($current_term_id) {
		$link = get_term_link($cat);
		if ( is_wp_error($link) ) return;

		$is_active = ((int)$cat->term_id === $current_term_id) ? ' is-active' : '';
		$depth_cls = ' depth-' . (int)$depth;

		$thumb_id = (int) get_term_meta($cat->term_id, 'thumbnail_id', true);
		if ($thumb_id) {
			$img_html = wp_get_attachment_image(
				$thumb_id,
				'woocommerce_thumbnail',
				false,
				[
					'class'   => 'adsdigi-catbar__img',
					'alt'     => $cat->name,
					'loading' => 'lazy',
				]
			);
		} else {
			$img_html = '<img class="adsdigi-catbar__img" src="' . esc_url(wc_placeholder_img_src('woocommerce_thumbnail')) . '" alt="' . esc_attr($cat->name) . '" loading="lazy">';
		}

		echo '<a class="adsdigi-catbar__item' . esc_attr($is_active . $depth_cls) . '" href="' . esc_url($link) . '">';
		echo '  <span class="adsdigi-catbar__thumb">' . $img_html . '</span>';
		echo '  <span class="adsdigi-catbar__name">' . esc_html($cat->name) . '</span>';
		echo '</a>';
	};

	// Render tree recursively
	$walk = function($parent_id, $depth = 0) use (&$walk, $by_parent, $render_item) {
		if (empty($by_parent[$parent_id])) return;
		foreach ($by_parent[$parent_id] as $cat) {
			$render_item($cat, $depth);
			$walk((int)$cat->term_id, $depth + 1);
		}
	};

	echo '<section class="adsdigi-catbar section">';

    	if (is_active_sidebar('content-danhmucsp')) {
		echo '<div  class="content-danhmucsp section"><div class="wrap">';
		dynamic_sidebar('Sản phẩm - Danh mục sản phẩm');
		echo '</div></div>';
	}

	echo '    <div class="adsdigi-catbar__grid">';
	$walk(0, 0);

	echo '    </div>';

	echo '</section>';
}



	get_header( 'shop' ); ?>

<div class="shop-wrapper">
  <h2 class="title">Tất cả sản phẩm</h2>

  <?php do_action( 'woocommerce_before_main_content' ); ?>

  <div class="shop-products">
    <?php if ( woocommerce_product_loop() ) : ?>

      <?php do_action( 'woocommerce_before_shop_loop' ); ?>

      <?php woocommerce_product_loop_start(); ?>

        <?php if ( wc_get_loop_prop( 'total' ) ) : ?>
          <?php while ( have_posts() ) : the_post(); ?>
            <?php do_action( 'woocommerce_shop_loop' ); ?>
            <?php wc_get_template_part( 'content', 'product' ); ?>
          <?php endwhile; ?>
        <?php endif; ?>

      <?php woocommerce_product_loop_end(); ?>

      <?php do_action( 'woocommerce_after_shop_loop' ); ?>

    <?php else : ?>

      <?php do_action( 'woocommerce_no_products_found' ); ?>

    <?php endif; ?>
  </div>

  <?php do_action( 'woocommerce_after_main_content' ); ?>
 
</div>


		 <?php if ( is_active_sidebar('content-bosuutap') ) {
			echo '<div  class="content-bosuutap section"><div class="wrap">';
			dynamic_sidebar('Sản phẩm - Bộ sưu tập nội thất');
			echo '</div></div>';
		}?>

		 <?php if ( is_active_sidebar('content-cauhoi') ) {
			echo '<div  class="content-cauhoi section"><div class="wrap">';
			dynamic_sidebar('Sản phẩm - Câu hỏi thường gặp');
			echo '</div></div>';
		}?>


<?php get_footer( 'shop' ); ?>

