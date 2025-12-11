<?php

/* Change Log:
- 30/09/21: ra version 1.0
*/


add_action( 'wp', 'caia_optimize_tag_page', 10, 1 );

function caia_optimize_tag_page(){	
	global $wp_query;

	if (is_tag() && !is_admin()){			

		$tag = $wp_query->query_vars['tag'] ?? '';

		if ($tag){
			
			$count = $wp_query->found_posts;

			if (class_exists('WPSEO_Taxonomy_Meta')){
				$noindex = WPSEO_Taxonomy_Meta::get_term_meta( $tag, 'post_tag', 'noindex' ); // noindex, index, default	
			}else{
				$noindex = 'default';
			}

			if ($noindex === 'default' && defined( 'CAIA_TAG_NOINDEX_DEFAULT' )){
				$noindex = 'noindex';
			}
			
			// echo $tag, ' - ', $noindex;
			
			if ($count == 1){
				$post = $wp_query->posts[0];
				$new_url= get_permalink($post->ID);
				if ($new_url){
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: " . $new_url); 
					exit();
				}
			}else if ( $count == 0 || $noindex === 'noindex' ){
				$wp_query->set_404();
	    		status_header(404);
	    		nocache_headers();

	    		// remove_action( 'genesis_archive_title_descriptions', 'genesis_do_archive_headings_open', 5, 3 );    		
	    		// add_action( 'genesis_archive_title_descriptions', 'caia_show_tag_title_404', 5, 3 );    		
	    		
			}
		}			
	}
}

add_filter( 'the_tags', 'caia_the_tags', 10, 5 );

function caia_the_tags($tag_list, $before, $sep, $after, $post_id) {
	global $post;

	if (!$post_id) $post_id = $post->ID;

	$tag_list = wp_get_post_terms($post_id, 'post_tag', array("fields" => "all"));	

	$a_arr = array();
	foreach ($tag_list as $key => $tag) {		
		$post_count = $tag->count;
		if ($post_count > 1){
			if (class_exists('WPSEO_Taxonomy_Meta')){
				$noindex = WPSEO_Taxonomy_Meta::get_term_meta( $tag->term_id, 'post_tag', 'noindex' ); // noindex, index, default
			}else{
				$noindex = 'index';
			}

			if ($noindex === 'default' && defined( 'CAIA_TAG_NOINDEX_DEFAULT' )){
				$noindex = 'noindex';
			}
			if ($noindex !== 'noindex'){				
				$tag_link = get_tag_link($tag->term_id);
				$tag_name = $tag->name;
				$a_arr[] = "<a href='{$tag_link}' target='_blank' class='tag_link'>{$tag_name}</a>";
			}
		}
	}



	if ($a_arr){
		
		$tag_a_list = implode($sep, $a_arr);		

		$res = $before . $tag_a_list . $after;

		return $res;

	}else{
		return '';
	}
  	
}






