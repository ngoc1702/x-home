<?php

/* Template Name: Trang Giới thiệu */

add_filter('genesis_site_layout', 'caia_cpt_layout');
function caia_cpt_layout()
{
	return 'full-width-content';
}

// Xóa tiêu đề
remove_action('genesis_entry_header', 'genesis_do_post_title');

// Xóa social share
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating()
{
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




add_action ('genesis_after_header' , 'add_page_banner');
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


add_action('genesis_before_footer', 'gioi_thieu', 7);
function gioi_thieu() {
    global $post;
     // --- Lý do chọn chúng tôi ---
     $tieude_taisao = get_post_meta($post->ID, 'tieude_taisao', true);
    $cards_taisao = get_post_meta($post->ID, 'cards_taisao', true); 

    echo '<section class="content-lydo"><div class="wrap">';
    if (!empty($tieude_taisao)) {
        echo '<h4>' . wp_kses_post($tieude_taisao) . '</h4>';
    }

    echo '<div class="wrap-card">';
    if (!empty($cards_taisao) && is_array($cards_taisao)) {
        foreach ($cards_taisao as $cards_taisao) {
            $img = isset($cards_taisao['card_image']) && !empty($cards_taisao['card_image'])
                ? wp_get_attachment_image_url($cards_taisao['card_image'][0], 'large')
                : '';

            echo '<div class="card-item">';
            if ($img) {
                echo '<img src="' . esc_url($img) . '" alt="">';
            }

            echo '<div class="card-content">';
            echo '<h5>' . esc_html($cards_taisao['card_title']) . '</h5>';
            echo '<p>' . esc_html($cards_taisao['card_desc']) . '</p>';
            echo '</div>'; 

            echo '</div>';
        }
    }
    echo '</div>'; 
    echo '</div></section>';


    // --- Đồng hành ---
    $tieude_donghanh = get_post_meta($post->ID, 'tieude_donghanh', true);
    $noidung_donghanh = get_post_meta($post->ID, 'noidung_donghanh', true);


    echo '<div class="content-donghanh"><div class="wrap">';
    echo '<div class="widgetcl">' . $tieude_donghanh . '</div>';

    if ($noidung_donghanh) {
        echo '<div class="noidung_donghanh">';

        // Lấy trực tiếp từng field
        $nd1 = isset($noidung_donghanh['text']) ? $noidung_donghanh['text'] : '';
        $nd2 = isset($noidung_donghanh['image']) ? $noidung_donghanh['image'] : '';

        if ($nd2) {
            echo '<div class="image">' . wpautop($nd2) . '</div>';
        }

        if ($nd1) {
            echo '<div class="text">' . wpautop($nd1) . '</div>';
        }

        echo '</div></div>'; 
    }

    // --- Đối tác ---
     $tieude_doitac = get_post_meta($post->ID, 'tieude_doitac', true);
    $cards = get_post_meta($post->ID, 'cards_group', true); // Lấy toàn bộ nhóm card

    echo '<section class="content-doitac"><div class="wrap">';
    if (!empty($tieude_doitac)) {
        echo '<h4>' . wp_kses_post($tieude_doitac) . '</h4>';
    }

    echo '<div class="wrap-card">';
    if (!empty($cards) && is_array($cards)) {
        foreach ($cards as $card) {
            $img = isset($card['card_image']) && !empty($card['card_image'])
                ? wp_get_attachment_image_url($card['card_image'][0], 'large')
                : '';

            echo '<div class="card-item">';
            if ($img) {
                echo '<img src="' . esc_url($img) . '" alt="">';
            }

            echo '<div class="card-content">';
            echo '<h5>' . esc_html($card['card_title']) . '</h5>';
            echo '<p>' . esc_html($card['card_desc']) . '</p>';
            echo '</div>'; 

            echo '</div>';
        }
    }
    echo '</div>'; 
    echo '</div></section>';


       // --- Giá trị cốt lõi ---
     $tieude_giatri = get_post_meta($post->ID, 'tieude_giatri', true);
    $cards_giatri = get_post_meta($post->ID, 'cards_giatri', true); 

    echo '<section class="content-giatri"><div class="wrap">';
    if (!empty($tieude_giatri)) {
        echo '<h4>' . wp_kses_post($tieude_giatri) . '</h4>';
    }

    echo '<div class="wrap-card">';
    if (!empty($cards_giatri) && is_array($cards_giatri)) {
        foreach ($cards_giatri as $cards_giatri) {
            $img = isset($cards_giatri['card_image']) && !empty($cards_giatri['card_image'])
                ? wp_get_attachment_image_url($cards_giatri['card_image'][0], 'large')
                : '';

            echo '<div class="card-item">';
            if ($img) {
                echo '<img src="' . esc_url($img) . '" alt="">';
            }

            echo '<div class="card-content">';
            echo '<h5>' . esc_html($cards_giatri['card_title']) . '</h5>';
            echo '<p>' . esc_html($cards_giatri['card_desc']) . '</p>';
            echo '</div>'; 

            echo '</div>';
        }
    }
    echo '</div></div>'; 
    echo '</section>';
    echo '</div></div>';


 

}






// Mobile
if (wp_is_mobile()) {
}

genesis();
