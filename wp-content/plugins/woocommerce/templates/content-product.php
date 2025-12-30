<?php
defined('ABSPATH') || exit;

global $product;

if (! is_a($product, WC_Product::class) || ! $product->is_visible()) {
	return;
}
?>

<li <?php wc_product_class( 'product-card', $product ); ?>>
	<div class="product-card__inner">

		<?php
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
			<span class="product-card__badge"><?php echo esc_html( $terms[0]->name ); ?></span>
		<?php endif; ?>

		<div class="product-card__thumb">
			<?php
			do_action( 'woocommerce_before_shop_loop_item' );
			do_action( 'woocommerce_before_shop_loop_item_title' );
			woocommerce_template_loop_product_link_close();
			?>
		</div>

		<div class="product-card__content">
			<div class="product-card__title">
				<?php do_action( 'woocommerce_shop_loop_item_title' ); ?>
				<p class="product-card__desc">Đa dạng kích thước, màu sắc</p>
			</div>

			<?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?>

			<div class="product-card__actions">
				<?php woocommerce_template_loop_add_to_cart(); ?>
				<a class="btn btn--outline" href="<?php the_permalink(); ?>">Xem Chi Tiết</a>
			</div>
		</div>

	</div>
</li>
