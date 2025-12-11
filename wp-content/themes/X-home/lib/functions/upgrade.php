<?php
/**
 * Handles CAIA updates.
 *
 * @category CAIA
 * @package  Updates
 * @author   CAIA
 */

/**
 * Pings API_URL asking if a new version of this theme is
 * available.
 *
 * If not, it returns false.
 *
 * If so, the external server passes serialized data back to this function,
 * which gets unserialized and returned for use.
 *
 * @since 1.0
 *
 * @return mixed Unserialized data, or false on failure
 */
function caia_update_check()
{
	global $wp_version;

	/** Get time of last update check */
	$caia_update = get_transient( 'caia-update' );

	/** If it has expired, do an update check */
	if ( ! $caia_update )
	{
		$url = 'http://api.caia.com/update/';
		$options = apply_filters(
			'caia_update_remote_post_options',
			array(
				'body' => array(
					'caia_version' 	  => CHILD_THEME_VERSION,
					'wp_version'      => $wp_version,
					'php_version'     => phpversion(),
					'uri'             => home_url(),
					'user-agent'      => "WordPress/$wp_version;",
				),
			)
		);

		$response = wp_remote_post( $url, $options );
		$caia_update = wp_remote_retrieve_body( $response );

		/** If an error occurred, return FALSE, store for 1 hour */
		if ( 'error' == $caia_update || is_wp_error( $caia_update ) || ! is_serialized( $caia_update ) || empty( $caia_update ) ) {
			set_transient( 'caia-update', array( 'new_version' => CHILD_THEME_VERSION ), 60 * 60 );
			return false;
		}

		/** Else, unserialize */
		$caia_update = maybe_unserialize( $caia_update );

		/** And store in transient for 24 hours */
		set_transient( 'caia-update', $caia_update, 60 * 60 * 24 );
	}

	/** If we're already using the latest version, return false */
	if ( version_compare( PARENT_THEME_VERSION, $caia_update['new_version'], '>=' ) )
		return false;

	return $caia_update;
}

add_action( 'admin_notices', 'caia_update_nag' );
/**
 * Displays the update nag at the top of the dashboard if there is a CAIA
 * update available.
 *
 * @since 1.0
 *
 * @return boolean Returns false if there is no available update, or user is not
 * a site administrator.
 */
function caia_update_nag() {

	$caia_update = caia_update_check();

	if ( ! is_super_admin() || ! $caia_update )
		return false;

	echo '<div id="update-nag">';
	printf(
		__( 'Caia %s is available. <a href="%s" onclick="return genesis_confirm(\'%s\');">update now</a>.', 'caia' ),
		esc_html( $caia_update['new_version'] ),
		wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=caia', 'upgrade-theme_caia' ),
		esc_js( __( 'Are you sure you want to upgrade?. "Cancel" to stop, "OK" to upgrade.', 'caia' ) )
	);
	echo '</div>';

}

add_action( 'admin_notices', 'caia_upgraded_notice' );
/**
 * Displays the notice that the theme settings were successfully updated to the
 * latest version.
 *
 * @since 1.0
 *
 * @return void
 */
function caia_upgraded_notice()
{
	if ( isset( $_REQUEST['upgraded'] ) && 'true' == $_REQUEST['upgraded'] )
		echo '<div id="message" class="updated highlight" id="message"><p><strong>' . sprintf( __( 'Congratulations! You are now on CAIA %s', 'caia' ), CHILD_THEME_VERSION ) . '</strong></p></div>';
}

add_filter( 'update_theme_complete_actions', 'caia_update_action_links', 10, 2 );
/**
 * Filters the action links at the end of an update.
 *
 * @since 1.0
 *
 * @param array $actions Existing array of action links
 * @param string $theme Theme name
 * @return string Removes all existing action links in favour of a single link.
 */
function caia_update_action_links( $actions, $theme )
{
	if ( 'caia' != $theme )
		return $actions;

	return sprintf( '<a href="%s">%s</a>', menu_page_url( 'caia-settings', 0 ), __( 'Click here to complete the upgrade', 'caia' ) );
}


add_filter( 'site_transient_update_themes', 'caia_update_push' );
add_filter( 'transient_update_themes', 'caia_update_push' );
/**
 * Integrate the CAIA update check into the WordPress update checks.
 *
 * This function filters the value that is returned when WordPress tries to pull
 * theme update transient data.
 *
 * It uses caia_update_check() to check to see if we need to do an update,
 * and if so, adds the proper array to the $value->response object. WordPress
 * handles the rest.
 *
 * @since 1.0
 *
 * @param object $value
 * @return object
 */
function caia_update_push( $value )
{
	$caia_update = caia_update_check();

	if ( $caia_update )
		$value->response['caia'] = $caia_update;

	return $value;
}

add_action( 'load-update-core.php', 'caia_clear_update_transient' );
add_action( 'load-themes.php', 'caia_clear_update_transient' );
/**
 * Delete CAIA update transient after updates or when viewing the themes page.
 *
 * The server will then do a fresh version check.
 *
 * It also disables the update nag on those pages as well.
 *
 * @since 1.0
 */
function caia_clear_update_transient()
{
	delete_transient( 'caia-update' );
	remove_action( 'admin_notices', 'caia_update_nag' );
}