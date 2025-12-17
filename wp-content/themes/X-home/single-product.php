<?php
add_filter( 'genesis_pre_get_option_site_layout', 'adsdigi_site_layout' );
function adsdigi_site_layout( $layout ) {
    // Áp dụng cho category archive 
        $layout = 'content-sidebar'; // hoặc 'sidebar-content'   
    return $layout;
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



// Custom layout cho trang sản phẩm
add_action( 'genesis_before_content_sidebar_wrap', 'add_thongtin_sp' );
function add_thongtin_sp() {
  global $product;

    // Đảm bảo $product luôn là WC_Product
    if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
        $product = wc_get_product( get_the_ID() );
    }

    if ( ! $product ) return;

    $anhsp         = rwmb_meta('anhsp'); 
    $regular_price = $product->get_regular_price() ? wc_price($product->get_regular_price()) : '';
    $sale_price    = $product->get_sale_price() ? wc_price($product->get_sale_price()) : '';
    $excerpt       = get_the_excerpt();

	echo '<div class="content-sanpham">';

		// Cột trái: Slider
		echo '<div class="slide_sp">';

			// Slider chính
			if ( !empty($anhsp) ) {
    echo '<div class="slider-for">';
    foreach ( $anhsp as $image ) {
        echo '<img src="' . esc_url( $image['full_url'] ) . '" alt="">';
    }
    echo '</div>';

    // Slider nav
    echo '<div class="slider-nav">';
    foreach ( $anhsp as $image ) {
        echo '<img src="' . esc_url( $image['full_url'] ) . '" alt="">';
    }
    echo '</div>';
} else {
    // Nếu không có ảnh metabox, lấy ảnh đại diện sản phẩm
    $thumb_id = get_post_thumbnail_id( get_the_ID() );
    if ( $thumb_id ) {
        $thumb_url = wp_get_attachment_image_url( $thumb_id, 'full' );
        echo '<div class="slider-for">';
            echo '<img src="' . esc_url( $thumb_url ) . '" alt="">';
        echo '</div>';
        echo '<div class="slider-nav">';
            echo '<img src="' . esc_url( $thumb_url ) . '" alt="">';
        echo '</div>';
    }
}

		echo '</div>'; 


		echo '<div class="info_sp">';


			echo '<h1 class="product-title">' . get_the_title() . '</h1>';


			// echo '<div class="product-price">';
			// 	echo '<span class="price-label">Giá: </span>';
			// 	if ( $sale_price ) {
			// 		echo '<span class="price-new">' . $sale_price . '</span>';
			// 		echo '<span class="price-old"><strike>' . $regular_price . '</strike></span>';   
			// 	} else {
			// 		echo '<span class="price-current">' . $regular_price . '</span>';
			// 	}
			// echo '</div>';


			if ( $excerpt ) {
				echo '<div class="product-excerpt">' . $excerpt . '</div>';
			}


			// if ( is_active_sidebar( 'content-luuy' ) ) {
			// 	echo '<div class="content-luuy section"><div class="wrap">';
			// 		dynamic_sidebar( 'Sản phẩm - Lưu ý' );
			// 	echo '</div></div>';
			// }

			// Form mua hàng
			echo '<div class="product-buy-form-wrap">';
			// if ( $product->is_purchasable() && $product->is_in_stock() ) {
			// 	echo '<div class="product-buy-form">';
			// 		woocommerce_template_single_add_to_cart();
			// 	echo '</div>';
			// }
			echo '<p class="btn_call"><a href="tel: 0963797000">Liên hệ</a></p>';
			echo '<p class="btn_baogia"><a >Báo giá</a></p>';
            echo '</div>'; 
		echo '</div>'; 

	echo '</div>'; 
}

// Thêm bài viết liên quan
add_action('genesis_after_content_sidebar_wrap', 'caia_add_product_YARPP', 9);
function caia_add_product_YARPP(){
    if ( function_exists('yarpp_related') ) {
        yarpp_related([
            'post_type' => 'product',
            'threshold' => 1,
            'template' => 'yarpp-template-product.php',
            'limit' => 4,
        ]);
    }
}


// Thêm khu vực tư vấn
add_action('genesis_before_sidebar_widget_area', 'add_product_info');
function add_product_info(){
	if ( is_active_sidebar( 'content-tuvan' ) ){
		echo '<div class="content-tuvan section"><div class="wrap">';
			dynamic_sidebar( 'Sản phẩm - Hỗ trợ tư vấn' );
		echo '</div></div>';
	}
}

// Thêm khu vực hỗ trợ mua hàng
add_action('genesis_before_sidebar_widget_area', 'add_product_muahang');
function add_product_muahang(){
	if ( is_active_sidebar( 'content-muahang' ) ){
		echo '<div class="content-muahang section"><div class="wrap">';
			dynamic_sidebar( 'Sản phẩm - Hỗ trợ mua hàng' );
		echo '</div></div>';
	}
}

genesis();