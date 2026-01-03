<?php
add_filter('genesis_pre_get_option_site_layout', 'caia_cpt_layout');
function caia_cpt_layout()
{
	return 'full-width-content';
}

//  remove_action( 'genesis_loop', 'genesis_do_loop' );
// Xóa post-info và post-meta của Genesis
remove_action('genesis_entry_header', 'genesis_post_info', 12);
remove_action('genesis_entry_footer', 'genesis_post_meta');

// Xóa social share và rating mặc định
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating()
{
	global $caia_rating, $caia_social;

	$star_pri = has_filter('the_content', array($caia_rating, 'add_rating_content_bottom'));
	if ($star_pri !== false) {
		remove_filter('the_content', array($caia_rating, 'add_rating_content_bottom'), $star_pri);
	}

	$social_pri = has_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'));
	if ($social_pri !== false) {
		remove_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'), $social_pri);
	}
}

// Thêm thông tin dưới nội dung bài viết
add_action('genesis_entry_footer', 'caia_add_content_post_meta', 1);
function caia_add_content_post_meta()
{
	echo '<div class="content-info-meta section">';
	echo '<div class="rating-social section">';
	echo do_shortcode('[caia_rating]');
	echo do_shortcode('[caia_social_share]');
	echo '</div>';
	echo '</div>';
}

add_action('genesis_after_header', 'add_page_banner');
function add_page_banner()
{
	global $post;

	echo '<section class="content-hero section">';
	echo '  <div class="hero-image">';

	// ✅ Shop + taxonomy + product detail: dùng chung widget Banner Trang Cửa Hàng
	if (
		(function_exists('is_shop') && is_shop())
		|| (function_exists('is_product_taxonomy') && is_product_taxonomy())
		|| (function_exists('is_product') && is_product())
	) {
		if (is_active_sidebar('shop-sidebar')) {
			dynamic_sidebar('shop-sidebar');
		} else {
			// fallback nếu chưa có widget
			echo '<img src="' . esc_url(get_stylesheet_directory_uri() . '/images/default-banner.jpg') . '" alt="Banner">';
		}

	} elseif (is_search() && is_active_sidebar('shop-sidebar')) {
		dynamic_sidebar('shop-sidebar');

	} else {
		$images = function_exists('rwmb_meta') ? rwmb_meta('anh_banner', ['size' => 'full']) : [];
		if (!empty($images)) {
			foreach ($images as $image) {
				echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '">';
			}
		} else {
			echo '<img src="' . esc_url(get_stylesheet_directory_uri() . '/images/default-banner.jpg') . '" alt="Banner">';
		}
	}

	echo '  </div>';

	echo '  <div class="content-breadcrumb">';
	echo '      <div class="breadcrumb-inner">';

	if (is_shop()) {
		echo '          <h2 class="title">Sản phẩm</h2>';
	} elseif (is_product_taxonomy()) {
		echo '          <h2 class="title">' . single_term_title('', false) . '</h2>';
	} elseif (is_search()) {
		echo '          <h2 class="title">Kết quả tìm kiếm</h2>';
	} else {
		echo '          <h2 class="title">' . get_the_title() . '</h2>';
	}

	echo do_shortcode('[breadcrumb]');
	echo '      </div>';
	echo '  </div>';

	echo '</section>';
}


remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'add_thongtin_sp');

function add_thongtin_sp()
{
	if (!function_exists('is_product') || !is_product())
		return;

	global $post, $product;

	if (!$post || empty($post->ID))
		return;

	if (function_exists('wc_setup_product_data')) {
		wc_setup_product_data($post);
	}
	$product = wc_get_product($post->ID);
	if (!$product)
		return;

	$color_tax = 'pa_mau-sac';
	$attr_key = 'attribute_' . $color_tax; // attribute_pa_mau-sac

	// Map variation -> image
	$variation_map = [];
	if ($product instanceof WC_Product_Variable) {
		foreach ($product->get_available_variations() as $v) {
			$vid = (int) ($v['variation_id'] ?? 0);
			$img_id = (int) ($v['image_id'] ?? 0);
			if (!$vid || !$img_id)
				continue;

			$attrs = is_array($v['attributes'] ?? null) ? $v['attributes'] : [];
			$val = (string) ($attrs[$attr_key] ?? '');
			if ($val === '')
				continue;

			$variation_map[] = [
				'variation_id' => $vid,
				'image_id' => $img_id,
				'attr_key' => $attr_key,
				'attr_val' => $val,
			];
		}
	}

	// Active image theo default color
	$active_img_id = (int) $product->get_image_id();
	$default_attrs = $product->is_type('variable') ? (array) $product->get_default_attributes() : [];
	$default_color = (string) ($default_attrs[$color_tax] ?? '');

	if ($default_color !== '') {
		foreach ($variation_map as $row) {
			if ($row['attr_val'] === $default_color) {
				$active_img_id = (int) $row['image_id'];
				break;
			}
		}
	}
	if (!$active_img_id && !empty($variation_map)) {
		$active_img_id = (int) $variation_map[0]['image_id'];
	}

	echo '<section class="adsdigi-pdp">';

	echo '<div id="product-' . esc_attr($product->get_id()) . '" class="' . esc_attr(implode(' ', wc_get_product_class('', $product))) . '">';

	do_action('woocommerce_before_single_product');

	if (post_password_required()) {
		echo get_the_password_form();
		echo '</div></section>';
		return;
	}

	echo '<div class="adsdigi-pdp__grid">';

	// LEFT
	echo '<div class="adsdigi-pdp__left">';
	echo '<div class="adsdigi-pdp__main">';
	if ($active_img_id) {
		echo wp_get_attachment_image($active_img_id, 'large', false, ['class' => 'adsdigi-pdp__mainImg custom-main-img']);
	} else {
		echo wc_placeholder_img('large');
	}
	echo '</div>';

	echo '<div class="adsdigi-pdp__thumbs custom-variation-thumbs" aria-label="Ảnh theo màu">';
	foreach ($variation_map as $row) {
		$is_active = ((int) $row['image_id'] === (int) $active_img_id) ? ' is-active' : '';
		$thumb = wp_get_attachment_image((int) $row['image_id'], 'woocommerce_thumbnail', false, [
			'class' => 'adsdigi-pdp__thumbImg custom-variation-thumb-img',
		]);

		echo '<button type="button" class="adsdigi-pdp__thumb custom-variation-thumb' . esc_attr($is_active) . '"'
			. ' data-variation-id="' . esc_attr($row['variation_id']) . '"'
			. ' data-attr-key="' . esc_attr($row['attr_key']) . '"'
			. ' data-attr-val="' . esc_attr($row['attr_val']) . '"'
			. ' data-image-id="' . esc_attr($row['image_id']) . '">'
			. $thumb
			. '</button>';
	}
	echo '</div>';
	echo '</div>';

	// RIGHT
	echo '<div class="adsdigi-pdp__right">';
	// ===== Thông tin thêm: Kích thước + Thương hiệu =====
	$length = $product->get_length();
	$width = $product->get_width();
	$height = $product->get_height();
	$unit = get_option('woocommerce_dimension_unit');

	$brand_text = '';
	// Nếu bạn dùng taxonomy brand phổ biến: product_brand / pa_thuong-hieu / yith_product_brand...
	$brand_terms = get_the_terms($product->get_id(), 'product_brand');
	if (!empty($brand_terms) && !is_wp_error($brand_terms)) {
		$brand_text = join(', ', wp_list_pluck($brand_terms, 'name'));
	}




	$cats = wc_get_product_category_list($product->get_id());
	if ($cats)
		echo '<div class="adsdigi-pdp__cats">' . $cats . '</div>';

	echo '<h1 class="adsdigi-pdp__title">' . esc_html(get_the_title($product->get_id())) . '</h1>';

	echo '<div class="adsdigi-pdp__priceRow">';
	// giá gốc
	echo '<span>Giá:</span></span><div class="custom-base-price adsdigi-pdp__price">';
	woocommerce_template_single_price();
	echo '</div>';
	// giá variation sẽ được Woo/JS cập nhật nếu bạn dùng
	echo '<div class="custom-variation-price adsdigi-pdp__priceVar"></div>';
	echo '</div>';

	echo '<div class="adsdigi-pdp__rating">';
	woocommerce_template_single_rating();
	echo '</div>';

	echo '<div class="adsdigi-pdp__excerpt">';
	woocommerce_template_single_excerpt();
	echo '</div>';

	// Render
	echo '<div class="adsdigi-pdp__specs">';

	if ($length || $width || $height) {
		echo '<div class="adsdigi-pdp__spec">';
		echo '<span class="adsdigi-pdp__specLabel">Kích thước:</span> ';
		echo '<span class="adsdigi-pdp__specVal">'
			. esc_html($length) . ' x ' . esc_html($width) . ' x ' . esc_html($height)
			. ($unit ? ' ' . esc_html($unit) : '')
			. '</span>';
		echo '</div>';
	}

	if ($brand_text) {
		echo '<div class="adsdigi-pdp__spec">';
		echo '<span class="adsdigi-pdp__specLabel">Thương hiệu:</span> ';
		echo '<span class="adsdigi-pdp__specVal">' . esc_html($brand_text) . '</span>';
		echo '</div>';
	}

	echo '</div>';



	echo '<div class="adsdigi-pdp__buy">';

	woocommerce_template_single_add_to_cart();
	echo '</div>';
	// Nút custom
	echo '<div class="btn_group">';
	echo '<div class="adsdigi-pdp__ctaRow">';
	echo '<a class="adsdigi-btn adsdigi-btn--secondary" href="tel:02366533838">TƯ VẤN BÁO GIÁ</a>';
	echo '</div>';

	echo '<div class="adsdigi-pdp__ctaRow adsdigi-pdp__ctaRow--full">';
	echo '<a class="adsdigi-btn adsdigi-btn--outline" >ĐẶT HÀNG YÊU CẦU</a>';
	echo '</div>';
	echo '</div>';




	echo '</div>';

	echo '</div>'; // grid

	echo '<div class="adsdigi-pdp__below">';
	woocommerce_output_product_data_tabs();
	echo '</div>';

	do_action('woocommerce_after_single_product');

	echo '</div>'; // #product
	echo '</section>';
}

add_action('genesis_loop', 'add_thongtinmota');

function add_thongtinmota() {
	if ( ! function_exists('is_product') || ! is_product() ) {
		return;
	}

	global $post;

	if ( ! $post || empty($post->ID) ) {
		return;
	}

	$product = wc_get_product($post->ID);
	if ( ! $product ) {
		return;
	}
	$content = get_post_field('post_content', $post->ID);
	if ( empty($content) ) {
		return;
	}
	$content = apply_filters('the_content', $content);

	echo '<section class="adsdigi-product-description section">';
	echo '  <div class="container">';
	echo '      <title class="section-title">Thông tin sản phẩm</title>';
	echo '      <div class="product-description__content">';
	echo            $content;
	echo '      </div>';
	echo '  </div>';
	echo '</section>';
}




add_action('genesis_loop', 'add_sitereviews');

function add_sitereviews() {
    if (!is_singular()) return; // dùng singular cho chắc (post/page/product…)

    // Không render trong request AJAX/REST
    if (wp_doing_ajax()) return;
    if (defined('REST_REQUEST') && REST_REQUEST) return;

    $post_id = get_queried_object_id();
    if (!$post_id) return;

    echo '<section id="danhgia" class="review-section">';

        echo '<div class="review-head">';
            echo '<h2 class="review-title">Khách hàng đánh giá</h2>';
        echo '</div>';

        // VIEW
        echo '<div class="review-mode review-mode--view is-active" id="reviewView">';
            echo '<div class="review-top">';
                echo '<div class="review-summary">';
                    // ÉP gán đúng bài
                    echo do_shortcode('[site_reviews_summary assigned_posts="'.$post_id.'"]');
                echo '</div>';

                echo '<div class="review-cta">';
                    echo '<button type="button" class="btn-review-toggle" data-target="form">ĐÁNH GIÁ CỦA BẠN</button>';
                echo '</div>';
            echo '</div>';

            echo '<div class="review-list-wrap">';
                echo do_shortcode('[site_reviews assigned_posts="'.$post_id.'" count="5" pagination="ajax"]');
            echo '</div>';
        echo '</div>';

        // FORM
        echo '<div class="review-mode review-mode--form" id="reviewForm">';
            echo '<div class="review-form-head">';
                echo '<h3 class="review-form-title">Gửi đánh giá</h3>';
                echo '<button type="button" class="btn-review-toggle btn-close" data-target="view">ĐÓNG LẠI</button>';
            echo '</div>';

            echo do_shortcode('[site_reviews_form assigned_posts="'.$post_id.'" hide="title,email,terms"]');
        echo '</div>';

    echo '</section>';
}




add_action('genesis_loop', 'add_thongtincamket');

function add_thongtincamket() {
		if (is_active_sidebar('content-camket')) {
		echo '<div  class="content-camket section"><div class="wrap">';
		dynamic_sidebar('Sản phẩm -  Cam kết');
		echo '</div></div>';
	}
}


// Thêm sản phẩm liên quan
add_action('genesis_after_content_sidebar_wrap', 'caia_add_product_YARPP', 9);
function caia_add_product_YARPP() {
    static $done = false;
    if ($done) return;
    $done = true;

    if (function_exists('yarpp_related')) {
        yarpp_related([
            'post_type'  => ['project'],
            'threshold'  => 1,
            'template'   => 'yarpp-template-product.php',
            'limit'      => 3,
        ]);
    }
}

genesis();
