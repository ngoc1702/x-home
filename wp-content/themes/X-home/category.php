<?php
/**
 * Handle the display of category page.
 *
 * @category CAIA
 * @package
 * @author   HoangLT
 */

// Allow subchild theme modify
if ( file_exists( CAIA_CUSTOM_DIR . '/category.php' ) )
{
	require( CAIA_CUSTOM_DIR . '/category.php' );
}

genesis();