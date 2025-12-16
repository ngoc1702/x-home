<?php

/* Template Name: Trang liên hệ */

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

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'caia_add_lienhe');


function caia_add_lienhe()
{
	global $post;
	$form = get_post_meta($post->ID, 'form', true);

	echo '<div  class="content-lienhe section">';
	echo '<div class="contact-section">';
	echo rwmb_meta('nd');
	echo '</div>';
	echo '<div class="form_lienhe">';
	echo do_shortcode($form);
	echo '</div>';
	echo '</div>';
}



add_action('genesis_loop', 'caia_add_map');
function caia_add_map()
{
	global $post;
	$map = get_post_meta($post->ID, 'map', true);
	echo '<div class="content-map section">';
	echo do_shortcode($map);
	echo '</div>';

}





// Mobile
if (wp_is_mobile()) {
}

genesis();
