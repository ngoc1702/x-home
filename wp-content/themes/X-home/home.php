<?php
/**
 * Handle the display of homepage.
 *
 * @category CAIA
 * @package  Template
 * @author   HoangLT
 */

remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'caia_do_home_loop' );

/**
 * Handle the content of homepage base on settings of CAIA Design Settings in admin area.
 *
 * @since 1.0
 */
function caia_do_home_loop()
{
	$home_blocks = caia_get_design_option( 'homepage' );	
	if( ! empty( $home_blocks ) && is_array( $home_blocks ) )
	{
		unset( $home_blocks['__i__'] );
		foreach( $home_blocks as $num => $home_block )
		{
			if(class_exists($home_block['__class_name'])){
				$block                 = new $home_block['__class_name'];
				$block->number         = $num;
				$block->settings_field = CAIA_DESIGN_SETTINGS_FIELD;
				$block->options_group  = 'homepage';
				$block->set_options($home_block);				
				$block->show();
			}
		}
	}
}

// Allow subchild theme modify
if ( file_exists( CAIA_CUSTOM_DIR . '/home.php' ) )
{
	require( CAIA_CUSTOM_DIR . '/home.php' );
}


genesis();

