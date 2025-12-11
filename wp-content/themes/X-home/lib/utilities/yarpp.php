<?php

define('CAIA_YARPP_VERSION', 2.0);

/*
* 3/7/2019: nâng cấp ưu tiên trọng số cho tag cao hơn taxonomy khác
* 25/5/2021: Sửa phần 'orderby' => $args['orderby'] thay 'post__in' để setting lấy nội dung
* 17/07/2021: fix lỗi related_post_id_key = empty - line 103
*/

if (! function_exists('yarpp_related')){
	function yarpp_related( $args = array(), $reference_ID = null, $echo = true )	{
		// global $wp_query;

		// caia_log('yarpp', 'call', $args);

		 $output = '';

		$related_query = caia_get_related_post( $reference_ID, $args );
		if ( $related_query === false){
			return false;
		}		

		$related_count = $related_query->post_count;
				
		if ( $echo && isset($args['template']) && file_exists(STYLESHEETPATH . '/' . $args['template']) )  {	
			add_filter( 'the_title', 'related_the_title', 10, 2 );

			$output = '<div class="yarpp-related">';				
			ob_start();
			include(STYLESHEETPATH . '/' .$args['template']);
			$output .= ob_get_contents();
			ob_end_clean();		
			$output .= '</div>';

			remove_filter( 'the_title', 'related_the_title', 10, 2 );
		}
		
		if ($echo) echo $output;
		
		
		return $output;
				
	}
}

$related_post_meta = null;

function related_the_title($title, $post_id){	
	global $related_post_meta;

	if ( empty($related_post_meta) ){
		return $title;
	}else{		
		foreach ($related_post_meta as $value) {
			$tmp = array_values($value);
			if ($post_id == $tmp[0]){
				return empty(trim($tmp[1])) ? $title : $tmp[1];
			}
		}
	}
	return $title;
}

// thay the cho wp-query lay bai viet lien quan
function caia_get_related_post($p_id = null, $args = array()){	

	global $post;	
	global $related_post_meta;	
	
	$defaults = array(
			'wp_query' 				=> array(),
			'manual_related_post'	=> true,
			'related_post_meta'		=> 'postyarpp', // do meta bv lien quan
			'related_post_id_key'	=> 'getpost', // do meta bv lien quan
			'limit' 				=> 5,
			);		
	
	$args = array_merge($defaults, $args);

	if (is_numeric($p_id) && $p_id){
		$post_id = $p_id;	
		$post_type = get_post_type($p_id);		
	}else{
		global $post;
		$post_id = $post->ID;
		$post_type = $post->post_type;
	}
	if (! $post_type ) return false;

	if ( isset($args['wp_query']) && $args['wp_query']){
		$args['wp_query']['post_type'] = isset($args['post_type'])? $args['post_type'] : $post_type;
		$args['wp_query']['posts_per_page'] = $args['limit'];
		$my_query = new WP_Query($args['wp_query']);	
	}else{
					
		$related_ids = array();
		// lay tu manual relate truoc
		$related_posts = get_post_meta($post_id, $args['related_post_meta'], array());	
		// print_r($related_posts);	
		if( isset($related_posts[0]) ){
			$related_post_meta = $related_posts[0];
			foreach ($related_posts[0] as $_post) {
				$mpost_id = $_post[$args['related_post_id_key']];
				if ($mpost_id && !in_array($mpost_id, $related_ids)) 
					$related_ids[] = $mpost_id;
			}
		}
		// print_r($related_ids);

		// lay danh sach id from nhom co cung term
		global $wpdb;
		$exclude_ids = $related_ids;
		$exclude_ids[] = $post_id;
		$exclude_str = implode(',', $exclude_ids);
		$sql = "select object_id from (select object_id, sum(weight) as count from {$wpdb->term_relationships} TR
			inner join (select term_taxonomy_id, if( locate( '_tag', taxonomy) > 0, 3, 1) as weight from {$wpdb->term_taxonomy}) TT
			on TR.term_taxonomy_id = TT.term_taxonomy_id
   			where TR.term_taxonomy_id in (select term_taxonomy_id from {$wpdb->term_relationships} where object_id = {$post_id}) and object_id not in ({$exclude_str})
   			group by object_id   
			order by count desc, object_id desc ) OB
			limit 10";


		$related2_ids = $wpdb->get_col($sql);
		$related_ids = array_merge($related_ids, $related2_ids);

		$args2 =array(
			'post_type'		=> isset($args['post_type']) ? $args['post_type'] : $post_type,			
			'post__in' 		=> $related_ids,
			'posts_per_page' => $args['limit'],
			'orderby'		=> isset($args['orderby']) ? $args['orderby'] : 'post__in',
		);

		// print_r($args2);

		$my_query = new WP_Query($args2);
	}
	

	return $my_query;
}


