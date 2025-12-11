<?php
/**
 * Helper functions for media type.
 *
 * @category   CAIA
 * @package    Functions
 * @subpackage Media
 * @author     CAIA
 */

/**
 * Get the dropdown list of image sizes.
 *
 * @param array $args
 *
 * @return string
 */
function caia_dropdown_image_sizes( $args = array() )
{
	$defaults = array(
		'name' => 'image_size',
		'id' => '',
		'class' => 'image-size',
		'selected' => '',
		'echo' => true
	);

	extract( wp_parse_args( $args, $defaults ) );
	$sizes = genesis_get_additional_image_sizes();
	$html = "<select id='$id' name='$name' class='$class'>\n\t";
	$html .= '<option value="thumbnail">thumbnail (' . get_option( 'thumbnail_size_w' ) . 'x' . get_option( 'thumbnail_size_h' ) . ')</option>'. "\n";

	foreach ( (array) $sizes as $name => $size )
	{
		$html .= '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $selected, FALSE ) . '>' . esc_html( $name ) . ' ( ' . $size['width'] . 'x' . $size['height'] . ' )</option>' . "\n";
	}

	$html .= '</select>';

	if( ! $echo )
		return $html;

	echo $html;
}

/**
 * Get img attribute like alt or title or both
 * Must be used within loop
 *
 * @since    1.0
 *
 * @param null $attr
 * @param null $_post
 *
 * @internal param $string 'title' or 'alt' or null | default is null
 * @return array of image alt and title if $attr is null or string ( alt or title ) if $attr is set
 */
function caia_get_image_attr( $attr = null, $_post = null )
{
	// global $post;

	// $_post = empty( $_post ) ? $post : $_post;

	// if ( function_exists( 'seo_friendly_images_get_attr' ) ) // => cause performance problem 
	// {
	// 	return seo_friendly_images_get_attr( $attr );
	// }
	// else
	// {
	// 	return get_the_title( $_post->ID );
	// }
	return get_the_title();
}

/** Use default post thumbnail if there are no post thumbnail */
if ( caia_get_option( 'use_default_thumbnail' ) )
	add_filter( 'genesis_get_image', 'caia_default_image_fallback', 10, 2 );

/**
 *
 *
 * @param $output
 * @param $args
 *
 * @return string
 */
function caia_default_image_fallback( $output, $args )
{
	global $post;
	$thumb = NO_THUMB_IMG;

	if ( ! $output && $args['format'] == 'url' )
	{
		$output = $thumb;
	}
	elseif ( ! $output && $args['format'] == 'html' )
	{
		global $_wp_additional_image_sizes;
		$output = '<img src="' . $thumb . '" alt="' . caia_get_image_attr( 'alt', $post ) . '" title="' . caia_get_image_attr( 'title', $post ) . '" width="' . $_wp_additional_image_sizes[$args['size']]['width'] . '" height="' . $_wp_additional_image_sizes[$args['size']]['height'] . '" />';
	}

	return $output;
}