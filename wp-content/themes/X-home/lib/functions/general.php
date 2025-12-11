<?php
/*
* Helper functions for different purposes.
* @category CAIA
* @package  Functions
* @author   HoangLT, TuanNM
*/

// Thêm file custom.css
add_action( 'wp_head', 'caia_custom_css' );
function caia_custom_css(){
	$custom_css = caia_get_design_option( 'custom_css' );
	if ( ! empty( $custom_css ) ){
		echo '<style type="text/css">' . $custom_css . '</style>';
	}
}

// Chọn layout cho các trang
add_filter( 'genesis_pre_get_option_site_layout', 'caia_custom_layout', 101 );
function caia_custom_layout( $opt ){
	global $wp_query;

	/** Homepage / Front Page */
	if ( ( is_home() || is_front_page() ) && caia_get_layout_option( 'home_layout' ) )
		$opt = caia_get_layout_option( 'home_layout' );

	/** Search (has results!) */
	elseif ( ( is_search() && ! empty( $wp_query->posts ) ) && caia_get_layout_option( 'search_layout' ) )
		$opt = caia_get_layout_option( 'search_layout' );

	/** 404 Error */
	elseif ( is_404() && caia_get_layout_option( '404_layout' ) )
		$opt = caia_get_layout_option( '404_layout' );

	/** Date (general) */
	elseif ( is_date() && caia_get_layout_option( 'date_layout' ) )
		$opt = caia_get_layout_option( 'date_layout' );

	/** Author */
	elseif ( is_author() && caia_get_layout_option( 'author_layout' ) )
		$opt = caia_get_layout_option( 'author_layout' );

	/** Category (all!) */
	elseif ( is_category() && caia_get_layout_option( 'category_layout' ) )
		$opt = caia_get_layout_option( 'category_layout' );

	/** Tag (all!) */
	elseif ( is_tag() && caia_get_layout_option( 'tag_layout' ) )
		$opt = caia_get_layout_option( 'tag_layout' );

	/** Custom taxonomy */
	elseif ( is_tax() && caia_get_layout_option( $wp_query->queried_object->taxonomy . '_layout' ) )
		$opt = caia_get_layout_option( $wp_query->queried_object->taxonomy . '_layout' );

	/** Taxonomy (all) */
	elseif ( is_tax() && caia_get_layout_option( 'taxonomy_layout' ) )
		$opt = caia_get_layout_option( 'taxonomy_layout' );

	/** Posts (all!) */
	elseif ( is_single() && caia_get_layout_option( 'post_layout' ) )
		$opt = caia_get_layout_option( 'post_layout' );

	/** Pages (all!) */
	elseif ( is_page() && caia_get_layout_option( 'page_layout' ) )
		$opt = caia_get_layout_option( 'page_layout' );
	/** Custom post type */
	elseif ( is_singular() && caia_get_layout_option( $wp_query->post->post_type . '_layout' ) )
		$opt = caia_get_layout_option( $wp_query->post->post_type . '_layout' );

	return $opt;
}

// Sửa tiêu đề chuyên mục và thẻ
add_action( 'genesis_before_loop', 'caia_archive_heading', 5 );
function caia_archive_heading()
{
	if ( ! is_archive() )
		return;

	// default heading tag
	$tag = apply_filters( 'caia_default_archive_heading_tag', 'h1' );
	$heading = apply_filters( 'caia_default_archive_heading', '' );

	$rel = 'rel="nofollow"';
	if ( is_paged() )
		$rel = 'rel="nofollow"';

	if ( is_category() )
	{
		$category_id = get_query_var( 'cat' );

		$tag = apply_filters( 'caia_category_heading_tag', $tag, $category_id );
		$heading = sprintf(
			'<%s class="archive-heading"><a href="%s" title="%s" %s>%s</a></%s>',
			$tag,
			get_category_link( $category_id ),
			get_cat_name( $category_id ),
			$rel,
			get_cat_name( $category_id ),
			$tag
		);
	}
	elseif ( is_tag() )
	{
		$tag_id = get_query_var( 'tag_id' );
		$post_tag = get_tag( $tag_id );

		$tag = apply_filters( 'caia_tag_heading_tag', $tag, $tag_id );
		$heading = sprintf(
			'<%s class="archive-heading"><a href="%s" title="%s" %s>%s</a></%s>',
			$tag,
			get_tag_link( $tag_id ),
			$post_tag->name,
			$rel,
			$post_tag->name,
			$tag
		);
	}
	elseif ( is_tax() )
	{		
		$queried_object = get_queried_object();

		$tag = apply_filters( 'caia_taxonomy_heading_tag', $tag, $queried_object->taxonomy, $queried_object->term_id );
		$heading = sprintf(
			'<%s class="archive-heading"><a href="%s" title="%s" %s>%s</a></%s>',
			$tag,
			get_term_link($queried_object->slug, $queried_object->taxonomy),			
			$queried_object->name,
			$rel,
			$queried_object->name,
			$tag
		);
	}
	else
	{
		$heading = sprintf(
			'<%s class="archive-heading">%s</%s>',
			$tag,
			get_bloginfo( 'name' ),
			$tag
		);
	}

	echo apply_filters( 'caia_archive_heading', $heading );
}