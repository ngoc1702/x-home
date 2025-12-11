<?php
/**
 * Helper functions for export data from child theme settings.
 *
 * @category CAIA
 * @package  Functions
 * @author   HoangLT
 */


add_filter( 'genesis_export_options', 'caia_export_options' );

/**
 * Return array of child theme export options and their arguments.
 *
 * @since  1.0.1
 * 
 * @param  array $options The Genesis default options
 * @return array     
 */
function caia_export_options( $options )
{
	// Caia Settings
	$options['caia_settings'] = array(
		'label'          => __( 'CAIA Settings', 'caia' ),
		'settings-field' => CAIA_SETTINGS_FIELD,
	);
	
	// Caia layout settings
	$options['caia_layout'] = array(
		'label' => __( 'CAIA Layout', 'caia' ),
		'settings-field' => CAIA_LAYOUT_SETTINGS_FIELD,
	);

	// Caia design settings
	$options['caia_design'] = array(
		'label' => __( 'CAIA Design', 'caia' ),
		'settings-field' => CAIA_DESIGN_SETTINGS_FIELD,
	);

	return $options;
}