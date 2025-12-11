<?php

// --------------------
// 06/10/21: fix thay doi jquery tu 1.x -> 3.x tu wp 5.5 tro len
// 29/11/21: fix thay doi jquery tu 1.x -> 3.x tu wp 5.5 tro len voi caldera form edit form

global $wp_version;

if ( version_compare($wp_version, '5.5') >= 0 && defined('CAIA_CALDERA_VER')){
	
	if ( is_admin() && isset($_GET['page']) && ($_GET['page'] === 'caldera-forms' || $_GET['page'] === 'caldera-forms-exend') ) {
		$GLOBALS['concatenate_scripts'] = false;
		add_action( 'wp_default_scripts', 'caia_replace_jquery_scripts', -1 );				
	}


	function caia_replace_jquery_scripts( $scripts ) {
		$uti_js_url = get_stylesheet_directory_uri() . '/lib/utilities/js/';

		// Set 'jquery-migrate' to 1.4.1.
		// caia_set_script( $scripts, 'jquery-migrate', $uti_js_url . 'jquery-migrate-1.4.1.min.js', array(), '1.4.1' );
		// Set 'jquery-core' to 1.12.4.
		caia_set_script( $scripts, 'jquery-core', $uti_js_url . 'jquery-1.12.4.min.js', array(), '1.12.4' );
		

		$deps = array( 'jquery-core' );
		// $deps[] = 'jquery-migrate';
		
		caia_set_script( $scripts, 'jquery', false, $deps, '1.12.4' );		
	}

	function caia_set_script( $scripts, $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
		$script = $scripts->query( $handle, 'registered' );

		if ( $script ) {
			// If already added
			$script->src  = $src;
			$script->deps = $deps;
			$script->ver  = $ver;
			$script->args = $in_footer;

			unset( $script->extra['group'] );

			if ( $in_footer ) {
				$script->add_data( 'group', 1 );
			}
		} else {
			// Add the script
			if ( $in_footer ) {
				$scripts->add( $handle, $src, $deps, $ver, 1 );
			} else {
				$scripts->add( $handle, $src, $deps, $ver );
			}
		}
	}
	
}
