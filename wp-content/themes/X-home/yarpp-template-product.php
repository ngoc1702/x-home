<?php
global $post;

$terms = wp_get_post_terms( $post->ID, 'product_cat', array( 'fields' => 'ids' ) );

if ( ! empty( $terms ) ) {

        $posts_per_page = 3;


    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => $posts_per_page,
        'post__not_in'   => array( $post->ID ),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $terms,
            ),
        ),
    );

    $related_query = new WP_Query( $args );

    if ( $related_query->have_posts() ) :
        echo '<div class="wrap"><div class="widget">';
        echo '<div class="widgettitle">Sản phẩm liên quan</div>';

        // Wrapper theo kiểu WooCommerce
        echo '<ul class="products main-posts">';

        while ( $related_query->have_posts() ) :
            $related_query->the_post();

            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) continue;
            ?>
            
            <li <?php wc_product_class( 'product-card', $product ); ?>>
                <div class="product-card__inner">

                    <?php
                    $cat_terms = get_the_terms( $product->get_id(), 'product_cat' );
                    if ( ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ) : ?>
                        <span class="product-card__badge"><?php echo esc_html( $cat_terms[0]->name ); ?></span>
                    <?php endif; ?>

                    <div class="product-card__thumb">
                        <?php
                        // Mở link sản phẩm
                        do_action( 'woocommerce_before_shop_loop_item' );

                        // Thumbnail (theo Woo template)
                        do_action( 'woocommerce_before_shop_loop_item_title' );

                        // Đóng link
                        woocommerce_template_loop_product_link_close();
                        ?>
                    </div>

                    <div class="product-card__content">
                        <div class="product-card__title">
                            <?php do_action( 'woocommerce_shop_loop_item_title' ); ?>
                            <p class="product-card__desc">Đa dạng kích thước, màu sắc</p>
                        </div>

                        <?php
                        // Giá + rating (nếu theme bật)
                        do_action( 'woocommerce_after_shop_loop_item_title' );
                        ?>

                        <div class="product-card__actions">
                            <?php
                            // Nút add to cart chuẩn Woo (tự xử lý stock/purchasable, ajax class, v.v.)
                            woocommerce_template_loop_add_to_cart();
                            ?>
                            <a class="btn btn--outline" href="<?php the_permalink(); ?>">Xem Chi Tiết</a>
                        </div>
                    </div>

                </div>
            </li>

            <?php
        endwhile;

        echo '</ul>';
        echo '</div></div>';

        wp_reset_postdata();
    endif;
}
?>
