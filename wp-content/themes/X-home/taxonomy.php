<?php
/**
 * Handle the display of taxonomy page.
 *
 * @category CAIA
 * @package  Template
 * @author   HoangLT
 */

// Allow subchild theme modify
if ( file_exists( CAIA_CUSTOM_DIR . '/taxonomy.php' ) )
{
	require( CAIA_CUSTOM_DIR . '/taxonomy.php' );
}


genesis();