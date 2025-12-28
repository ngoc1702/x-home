<?php
/**
 * Template: taxonomy (Genesis) - subcats + custom project layout
 */

add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

get_header();

// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

// Breadcrumb + heading mặc định (nếu theme bạn có)
remove_action( 'genesis_after_header', 'genesis_do_breadcrumbs', 9 );
remove_action( 'genesis_before_loop', 'caia_archive_heading', 5 );

// Read more
add_filter( 'excerpt_more', 'be_more_link' );
add_filter( 'get_the_content_more_link', 'be_more_link' );
add_filter( 'the_content_more_link', 'be_more_link' );
function be_more_link( $more_link ) {
	return '';
}

// Gỡ loop mặc định
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'catergory_project_custom' );
function catergory_project_custom() {

	global $wp_query;

	// Set 12 bài / page (cách này vẫn chạy, nhưng tốt nhất nên dùng pre_get_posts)
	$wp_query->set('posts_per_page', 12);
	$wp_query->get_posts();

	$taxonomy = 'project_cat';
	$current  = get_queried_object();

	$term_id   = ( $current && isset( $current->term_id ) ) ? (int) $current->term_id : 0;
	$parent_id = ( $current && isset( $current->parent ) )  ? (int) $current->parent  : 0;

	// ===== Heading title cho H2 (ưu tiên MetaBox nếu có) =====
	$heading = single_term_title( '', false );
	if ( function_exists( 'rwmb_meta' ) && $term_id ) {
		$tieude = rwmb_meta( 'prefix_tieude', [ 'object_type' => 'term' ], $term_id );
		if ( ! empty( $tieude ) ) {
			$heading = wp_strip_all_tags( $tieude );
		}
	}

	// In heading TRƯỚC subcategory
	echo '<h2>' . esc_html( $heading ) . '</h2>';

	// ===== SUBCATS =====
	// base_id: nếu đang ở con -> base = cha, nếu đang ở cha -> base = chính nó
	$base_id = $parent_id ? $parent_id : $term_id;

	$subcats = get_terms( [
		'taxonomy'   => $taxonomy,
		'parent'     => $base_id,
		'hide_empty' => false,
		'orderby'    => 'term_id',
		'order'      => 'ASC',
	] );

	if ( ! is_wp_error( $subcats ) && ! empty( $subcats ) ) {
		echo '<div class="taxonomy-subcats">';
		foreach ( $subcats as $t ) {
			$is_active = ( $parent_id > 0 && (int) $t->term_id === $term_id );

			echo '<a class="subcat-item ' . ( $is_active ? 'is-active' : '' ) . '" href="' . esc_url( get_term_link( $t ) ) . '">'
				. esc_html( $t->name ) .
			'</a>';
		}
		echo '</div>';
	}

	// ===== LOOP bài viết trong taxonomy =====
	if ( have_posts() ) {

		echo '<div class="page_congtrinh section"><div class="wrap">';
		echo '<div class="main-posts">';

		while ( have_posts() ) {
			the_post();

			echo '<div class="project">';

			if ( has_post_thumbnail() ) {
				echo '<div class="thumb">';
				echo '<a href="' . esc_url( get_permalink() ) . '">';
				the_post_thumbnail( 'large', [ 'class' => 'project-thumb' ] );
				echo '</a>';
				echo '</div>';
			}

			echo '<div class="list-info">';
			echo '<h3 class="title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';

			$diachi = get_post_meta( get_the_ID(), 'diachi', true );
			if ( ! empty( $diachi ) ) {
				echo '<p class="project-diachi"><i class="fas fa-map-marker-alt"></i> ' . esc_html( $diachi ) . '</p>';
			}

			echo '</div>'; 
			echo '</div>'; 
		}

		echo '</div>'; 

		// Pagination
		$paged = max( 1, (int) get_query_var( 'paged' ) );

		$pagination = paginate_links( [
			'total'     => (int) $wp_query->max_num_pages,
			'current'   => $paged,
			'type'      => 'list',
			'prev_next' => false,
		] );

		if ( $pagination ) {
			echo '<div class="pagination">' . $pagination . '</div>';
		}

		echo '</div></div>'; 

	} else {
		echo '<p>Không có bài nào.</p>';
	}
}



if ( function_exists( 'genesis' ) ) {
	genesis();
} else {
	get_footer();
}
