<?php
/**
 * Controls the CAIA admin menu.
 *
 * @category CAIA
 * @package  Admin
 * @author   CAIA
 */

add_action( 'genesis_admin_menu', 'caia_add_admin_menus' );

/**
 * Adds CAIA menus are the submenu items under Genesis item in admin menu.
 *
 * @since 1.0
 */
function caia_add_admin_menus()
{
	/** Do nothing, if not viewing the admin */
	if ( ! is_admin() )
		return;

	global $_caia_admin_theme_settings, $_caia_admin_layout_settings, $_caia_admin_design_settings;

	$_caia_admin_design_settings = new CAIA_Design;

	$_caia_admin_theme_settings = new CAIA_Theme_Settings;

	$_caia_admin_layout_settings = new CAIA_Layout;

	
}