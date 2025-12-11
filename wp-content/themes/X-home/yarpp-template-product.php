<?php
global $post;

// Lấy danh mục (category) của sản phẩm hiện tại
$terms = wp_get_post_terms($post->ID, 'product_cat', array('fields' => 'ids'));

if (!empty($terms)) {
   if (wp_is_mobile()) {
    $posts_per_page =4;
} else {
    $posts_per_page = 5;
}

$args = array(
    'post_type'      => 'product',
    'posts_per_page' => $posts_per_page,
    'post__not_in'   => array($post->ID),
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $terms,
        ),
    ),
);

    $related_query = new WP_Query($args);

    if ($related_query->have_posts()):
        echo '<div class="wrap"><div class="widget">';
        echo '<div class="widgettitle">Sản phẩm liên quan</div>';
        echo '<div class="main-posts">';

        while ($related_query->have_posts()) : $related_query->the_post(); 
            $product = wc_get_product(get_the_ID());

            echo '<div class="product">';
                echo '<a href="' . get_permalink() . '" rel="bookmark" title="' . esc_attr(get_the_title()) . '" class="alignleft">';
                    the_post_thumbnail('product-image');
                echo '</a>';

                echo '<div class="list-info">';
                    echo '<p class="widget-item-title"><a href="' . get_permalink() . '" title="' . esc_attr(get_the_title()) . '">' . get_the_title() . '</a></p>';

                    if ($product && $product->is_purchasable() && $product->is_in_stock()) {
                        echo '<form method="post" action="' . esc_url($product->add_to_cart_url()) . '">';
                        echo '<input type="hidden" name="add-to-cart" value="' . esc_attr($product->get_id()) . '" />';
                        echo '<button type="submit" class="button add_to_cart_button ajax_add_to_cart">Thêm giỏ hàng</button>';
                        echo '</form>';
                    }
                echo '</div>'; // .list-info

            echo '</div>'; // .product

        endwhile;

        echo '</div></div></div>';
        wp_reset_postdata();
    endif;
}
?>
