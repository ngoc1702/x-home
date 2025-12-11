<?php
/**
 * Initializes the core function and hook for CAIA theme.
 *
 * @category CAIA
 * @package  Initializes
 * @author   HoangLT, TuanNM
 */


/** Define Theme Info Constants */
// define( 'CAIA_VERSION', '2.3' ); // Deprecated from version 1.0.3
define( 'CHILD_THEME_VERSION', '3.0' );
define( 'CHILD_THEME_NAME', 'CAIA' );

/** Define Directory Location Constants */
define( 'CAIA_DIR', get_stylesheet_directory() );
define( 'CAIA_CUSTOM_DIR', CAIA_DIR . '/custom' );
define( 'CAIA_LIB_DIR', CAIA_DIR . '/lib' );
define( 'CAIA_IMAGES_DIR', CAIA_DIR . '/images' );
define( 'CAIA_ADMIN_DIR', CAIA_LIB_DIR . '/admin' );
define( 'CAIA_FUNCTIONS_DIR', CAIA_LIB_DIR . '/functions' );
define( 'CAIA_WIDGETS_DIR', CAIA_LIB_DIR . '/widgets' );
define( 'CAIA_UTILITIES_DIR', CAIA_LIB_DIR . '/utilities' ); // from 1.0.7

/** Define Settings Field Constants (for DB storage) */
define( 'CAIA_SETTINGS_FIELD', 'caia-settings' );
define( 'CAIA_LAYOUT_SETTINGS_FIELD', 'caia-layout-settings' );
define( 'CAIA_DESIGN_SETTINGS_FIELD', 'caia-design-settings' );

/** Load theme */
// require_once( CAIA_LIB_DIR . '/theme.php' );

/** Load admin  */
if ( is_admin() ) :
	require( CAIA_ADMIN_DIR . '/caia-settings.php' );
	require( CAIA_ADMIN_DIR . '/caia-layout.php' );
	require( CAIA_ADMIN_DIR . '/caia-design.php' );
	require( CAIA_ADMIN_DIR . '/menu.php' );
	
endif;

/** Load Functions & some utilities of admin */
require( CAIA_FUNCTIONS_DIR . '/options.php' );
require( CAIA_FUNCTIONS_DIR . '/general.php' );
require( CAIA_FUNCTIONS_DIR . '/media.php' );
require( CAIA_FUNCTIONS_DIR . '/post.php' );
if ( is_admin() ) :
	// some functions only run in admin
	require( CAIA_FUNCTIONS_DIR . '/ajax.php' ); // since 2.0
	require( CAIA_FUNCTIONS_DIR . '/export.php' );
endif;

/** Load utilities */
require( CAIA_UTILITIES_DIR . '/utilities.php' ); // since 1.0.7

/** Load Widgets */
require( CAIA_WIDGETS_DIR . '/widgets.php' );