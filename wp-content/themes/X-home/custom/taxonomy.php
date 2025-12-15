
<?php
get_header();

// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Đưa ảnh lên trước tiêu đề
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );

remove_action( 'genesis_after_header', 'genesis_do_breadcrumbs',9);
// Xóa tiêu đề chuyên mục mặc định
remove_action( 'genesis_before_loop', 'caia_archive_heading', 5 );


// Sửa chữ read-more
add_filter( 'excerpt_more', 'be_more_link' );
add_filter( 'get_the_content_more_link', 'be_more_link' );
add_filter( 'the_content_more_link', 'be_more_link' );
function be_more_link($more_link) {
    return sprintf('', get_permalink(), '');
}



// Gỡ loop mặc định
remove_action( 'genesis_loop', 'genesis_do_loop' );
        

// Lấy ID của term hiện tại
$term_id = get_queried_object_id();



// Lấy dữ liệu meta từ taxonomy
$tieude = rwmb_meta( 'prefix_tieude', ['object_type' => 'term'], $term_id );
$nd     = rwmb_meta( 'prefix_noidung', ['object_type' => 'term'], $term_id );
?>

<div class="taxonomy-wrapper">
    <div class="taxonomy-header">
       <?php if ( $tieude ) : ?>
            <div class="taxonomy-title"><?php echo wp_kses_post( $tieude ); ?></div>
        <?php else : ?>
            <h1 class="taxonomy-title"><?php single_term_title(); ?></h1>
        <?php endif; ?>
        </div>

    <!-- Danh sách sản phẩm -->
   <div class="taxonomy-products">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) : the_post();
            echo '<article class="entry">';

            // Ảnh nằm trên tiêu đề
            echo '<div class="entry-image">';
            genesis_do_post_image();
            echo '</div>';
            // Hiển thị danh mục sản phẩm
            $terms = get_the_terms( get_the_ID(), 'product_cat' );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                echo '<div class="product-categories">';
                foreach ( $terms as $term ) {
                    echo '<a href="' . esc_url( get_term_link( $term ) ) . '" class="product-category">' . esc_html( $term->name ) . '</a> ';
                }
                echo '</div>';
            }

            // Tiêu đề
            genesis_do_post_title();

            
            // Nội dung
            genesis_do_post_content();

            echo '</article>';
        endwhile;
    else :
        echo '<p>Không có sản phẩm nào.</p>';
    endif;
    ?>
</div>


    <!-- Nội dung meta -->
    <div class="taxonomy-content">
        <?php if ( $nd ) : ?>
            <section class="taxonomy-desc">
                <?php
                if ( is_array( $nd ) ) {
                    foreach ( $nd as $item ) {
                        echo wpautop( $item );
                    }
                } else {
                    echo wpautop( $nd );
                }
                ?>
            </section>
        <?php endif; ?>
    </div>
</div>

<?php
