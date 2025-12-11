<?php
/**
 * Helper functions for posts.
 *
 * @category   CAIA
 * @package    Functions
 * @subpackage Posts
 * @author     CAIA
 */

/**
 * Get the dropdown list of registed post types then return or echo it.
 *
 * @since          1.0
 *
 * @param array $args
 *
 * @return string | voide
 */
function caia_dropdown_post_types( $args = array() )
{
	$defaults = array(
		'name'           => 'post_type',
		'class'          => 'post-type-dropdown',
		'selected'       => '',
		'public'         => true,
		'show_ui'        => true,
		'show_all'		 => false,
		'show_all_value' => array( 'post', 'page' ),
		'show_all_text'  => __( 'All post types', 'caia' ),
		'echo'           => true
	);

	extract( wp_parse_args( $args, $defaults ) );

	$post_types = get_post_types(
		array(
			'public'   => $public,
			'show_ui'  => $show_ui
		),
		'objects'
	);

	$html = sprintf( '<select name="%s" class="%s" >' . "\n\t", $name, $class );

	if ( $show_all )
	{
		$selected_option = selected( $show_all_value, $selected, false );
		$html .= sprintf( '<option value="%s" %s>%s</option>' . "\n\t", $show_all_value, $selected_option, $show_all_text );
	}

	if ( is_array( $post_types ) && ! empty( $post_types ) )
	{
		foreach ( $post_types as $post_type => $post_type_obj )
		{
			$selected_option = selected( $post_type, $selected, false );
			$html .= sprintf( '<option value="%s" %s>%s</option>' . "\n\t", $post_type, $selected_option, $post_type_obj->labels->singular_name );
		}
	}


	$html .= '</select>';

	if ( ! $echo )
	{
		return $html;
	}

	echo $html;
}

/**
 * Tests if any of a post's assigned categories are descendants of target categories
 *
 * @since  1.0.4
 * 
 * @param int|array $cats The target categories. Integer ID or array of integer IDs
 * @param int|object $_post The post. Omit to test the current post in the Loop or main query
 *
 * @see get_term_by() You can get a category by name or slug, then pass ID to this function
 * @uses get_term_children() Passes $cats
 * @uses in_category() Passes $_post (can be empty)
 * @version 2.7
 * @link http://codex.wordpress.org/Function_Reference/in_category#Testing_if_a_post_is_in_a_descendant_category
 *
 * @return bool True if at least 1 of the post's categories is a descendant of any of the target categories
 */
if ( ! function_exists( 'post_is_in_descendant_category' ) )
{
	function post_is_in_descendant_category( $cats, $_post = null ) {
		foreach ( (array) $cats as $cat ) {
			// get_term_children() accepts integer ID only
			$descendants = get_term_children( (int) $cat, 'category' );
			if ( $descendants && in_category( $descendants, $_post ) )
				return true;
		}
		return false;
	}
}
