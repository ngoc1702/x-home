<?php
// taxonomy-product_cat.php

$term = get_queried_object();

if ( $term && isset($term->slug) ) {
    $current_slug = $term->slug;
    $current_id   = $term->term_id;
    $taxonomy     = $term->taxonomy;

    // Tìm danh mục cha cấp 1
    $top_parent = $term;
    while ( $top_parent->parent != 0 ) {
        $top_parent = get_term( $top_parent->parent, $taxonomy );
    }

    // Lấy slug cha cấp 1
    $parent_slug = $top_parent->slug;

    // Lấy page có slug trùng với cha cấp 1
    $parent_page = get_page_by_path( $parent_slug );
    if ( $parent_page ) {
        $parent_url = get_permalink( $parent_page );

        // Nếu là chính cha cấp 1 → không thêm hash
        if ( $current_slug === $parent_slug ) {
            wp_redirect( $parent_url );
            exit;
        } else {
            // Nếu là con → thêm hash
            wp_redirect( $parent_url . '#' . $current_slug );
            exit;
        }
    }
}

// Nếu không tìm thấy thì load mặc định
get_header();
echo '<p>Không tìm thấy danh mục phù hợp</p>';
get_footer();
