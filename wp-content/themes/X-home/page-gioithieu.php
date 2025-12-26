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

add_action('genesis_before_footer', 'gioi_thieu', 7);
function gioi_thieu()
{
    global $post;

    if (is_active_sidebar('content-vechungtoigt')) {
        echo '<div class="content-vechungtoigt section"><div class="wrap">';
        dynamic_sidebar('Giới thiệu - Về chúng tôi');
        echo '</div></div>';
    }

    // Thành tựu
    $thanhtuu = get_post_meta($post->ID, 'thanhtuu', true);
    echo '<section class="content-thanhtuu"><div class="wrap">';
    if (!empty($thanhtuu)) {
        echo '<div>' . wp_kses_post($thanhtuu) . '</div>';
    }
    echo '</div></section>';

    // Tầm nhìn
    $noidung_tamnhin = get_post_meta($post->ID, 'noidung_tamnhin', true);
    echo '<div class="content-tamnhin"><div class="wrap">';
    if ($noidung_tamnhin) {
        echo '<div class="noidung_tamnhin">';
        $nd1 = isset($noidung_tamnhin['text']) ? $noidung_tamnhin['text'] : '';
        $nd2 = isset($noidung_tamnhin['image']) ? $noidung_tamnhin['image'] : '';
        if ($nd2) {
            echo '<div class="image">' . wpautop($nd2) . '</div>';
        }

        if ($nd1) {
            echo '<div class="text">' . wpautop($nd1) . '</div>';
        }
        echo '</div></div>';
    }
    echo '</div></div>';

    // Sứ mệnh
    $noidung_sumenh = get_post_meta($post->ID, 'noidung_sumenh', true);
    echo '<div class="content-sumenh"><div class="wrap">';
    if ($noidung_sumenh) {
        echo '<div class="noidung_sumenh">';
        $nd1 = isset($noidung_sumenh['text']) ? $noidung_sumenh['text'] : '';
        $nd2 = isset($noidung_sumenh['image']) ? $noidung_sumenh['image'] : '';
        if ($nd2) {
            echo '<div class="image">' . wpautop($nd2) . '</div>';
        }

        if ($nd1) {
            echo '<div class="text">' . wpautop($nd1) . '</div>';
        }
        echo '</div></div>';
    }
    echo '</div></div>';


    //Giá trị cốt lõi
    $noidung_giatri = get_post_meta($post->ID, 'noidung_giatri', true);
    echo '<div class="content-giatri"><div class="wrap">';
    if ($noidung_giatri) {
        echo '<div class="noidung_giatri">';
        $nd1 = isset($noidung_giatri['text']) ? $noidung_giatri['text'] : '';
        $nd2 = isset($noidung_giatri['image']) ? $noidung_giatri['image'] : '';
        if ($nd2) {
            echo '<div class="image">' . wpautop($nd2) . '</div>';
        }

        if ($nd1) {
            echo '<div class="text">' . wpautop($nd1) . '</div>';
        }
        echo '</div></div>';
    }
    echo '</div></div>';

    // Đội ngũ
    $tieude_doingu = get_post_meta($post->ID, 'tieude_doingu', true);
    $images = rwmb_meta(
        'anh_doingu',
        [
            'size' => 'large',
        ],
        get_the_ID()
    );

    echo '<div class="content-doingu section">';

    echo '<div class="widgettd">' . $tieude_doingu . '</div>';

    echo '<div class="doingu-wrapper">';

    if (! empty($images) && is_array($images)) {
        foreach ($images as $image) {
            echo '<div class="doingu-item">';
            echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '">';
            echo '</div>';
        }
    }

    echo '</div>';
    echo '</div>';


    // Giải thưởng
    $tieude_giaithuong = get_post_meta($post->ID, 'tieude_giaithuong', true);
    $cards_giaithuong  = get_post_meta($post->ID, 'cards_giaithuong', true);

    echo '<section class="content-giaithuong"><div class="wrap">';

    if (! empty($tieude_giaithuong)) {
        echo '<h4>' . wp_kses_post($tieude_giaithuong) . '</h4>';
    }

    echo '<div class="wrap-card">';

    if (! empty($cards_giaithuong) && is_array($cards_giaithuong)) {
        foreach ($cards_giaithuong as $card_giaithuong) {

            $img = (! empty($card_giaithuong['card_image']))
                ? wp_get_attachment_image_url($card_giaithuong['card_image'][0], 'large')
                : '';

            echo '<div class="card-item">';

            if ($img) {
                echo '<img src="' . esc_url($img) . '" alt="">';
            }

            echo '<div class="card-content">';
            echo '<h3>' . esc_html($card_giaithuong['card_title']) . '</h3>';
            echo '<p>' . esc_html($card_giaithuong['card_desc']) . '</p>';
            echo '</div>';

            echo '</div>';
        }
    }

    echo '</div>';
    if (! empty($cards_giaithuong) && is_array($cards_giaithuong)) {
        echo '<div class="giaithuong-tabs">';
        foreach ($cards_giaithuong as $card_giaithuong) {
            if (! empty($card_giaithuong['card_stt'])) {
                echo '<div class="giaithuong-tab">'
                    . esc_html($card_giaithuong['card_stt']) .
                    '</div>';
            }
        }
        echo '</div>';
    }

    echo '</div></section>';

    //Lý do
    $lydo = rwmb_meta('lydo');

    if (!empty($lydo)) {
        echo '<div class="content-lydo section"><div class="wrap">';
        foreach ($lydo as $value) {
            echo '<div class="widget">';
            echo do_shortcode(wpautop($value['nd_lydo']));
            echo '</div>';
        }
        echo '</div></div>';
    }

      // Đối tác
    $tieude_doitac = get_post_meta($post->ID, 'tieude_doitac', true);
    $images = rwmb_meta(
        'logo_doitac',
        [
            'size' => 'large',
        ],
        get_the_ID()
    );

    echo '<div class="content-doitac section"><div class="wrap">';

    echo '<div class="widgettd">' . $tieude_doitac . '</div>';

    echo '<div class="doitac-wrapper">';
    if (! empty($images) && is_array($images)) {
        foreach ($images as $image) {
            echo '<div class="doitac-item">';
            echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($image['alt']) . '">';
            echo '</div>';
        }
    }

    echo '</div>';
    echo '</div></div>';
}

// Mobile
if (wp_is_mobile()) {
}

genesis();
