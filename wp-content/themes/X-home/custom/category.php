<?php

add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
  return 'full-width-content';
}

//Xóa tiêu đề chuyên mục
remove_action( 'genesis_before_loop', 'caia_archive_heading', 5 );
add_action( 'genesis_before_content', 'caia_archive_heading', 5 );

// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

//Đưa ảnh lên trước tiêu đề
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );




remove_action( 'genesis_before_content', 'caia_archive_heading', 5 );
remove_action( 'genesis_before_loop', 'caia_archive_heading', 5 );



// Không tải loop mặc định của Genesis
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'custom_category_loop' );

function custom_category_loop() {

    if ( ! is_category() ) return;

    $category = get_queried_object();
    if ( ! $category || ! isset( $category->term_id ) ) return;

    $cat_id = $category->term_id;
    $paged  = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'cat'            => $cat_id,
    );

    $query = new WP_Query( $args );

    echo '<div class="blog-wrapper">';

    // ===== TITLE CATEGORY =====
    echo '<h1 class="archive-title">' . esc_html( single_cat_title('', false) ) . '</h1>';

    if ( $query->have_posts() ) :

        echo '<div class="blog-grid">';

        while ( $query->have_posts() ) : $query->the_post(); ?>

            <article class="blog-item">

                <a href="<?php the_permalink(); ?>" class="blog-thumb">
                    <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium_large' ); ?>
                </a>

                <div class="blog-info">
                    <h3 class="blog-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>

               
                </div>

            </article>

        <?php endwhile;

        echo '</div>';

        // ===== PAGINATION =====
        echo '<div class="pagination">';
        echo paginate_links( array(
            'base'      => trailingslashit( get_category_link( $cat_id ) ) . '%_%',
            'format'    => 'page/%#%/',
            'current'   => max( 1, $paged ),
            'total'     => $query->max_num_pages,
            'prev_text' => '«',
            'next_text' => '»',
        ) );
        echo '</div>';

    else :
        echo '<p>Không có bài viết nào.</p>';
    endif;

    echo '</div>';

    wp_reset_postdata();
}


if (wp_is_mobile() ){

}