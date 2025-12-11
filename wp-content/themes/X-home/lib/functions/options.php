<?php
/**
 * Helper functions for accessing CAIA settings.
 *
 * @category CAIA
 * @package  Options
 * @author   HoangLT
 */

/**
 * Return the theme option from option database or cache.
 *
 * @sincee 1.0
 *
 * @param string $option
 *
 * @return mixed
 */
function caia_get_option( $option )
{
	return genesis_get_option( $option, CAIA_SETTINGS_FIELD );
}

/**
 * Echo the theme option.
 *
 * @since 1.0
 *
 * @param string $option
 */
function caia_option( $option )
{
	genesis_option( $option, CAIA_SETTINGS_FIELD );
}

/**
 * Return the layout option from database or cache.
 *
 * @since 1.0
 *
 * @param $option
 *
 * @return mixed
 */
function caia_get_layout_option( $option )
{
	return genesis_get_option( $option, CAIA_LAYOUT_SETTINGS_FIELD );
}

/**
 * Echo the layout option.
 *
 * @since 1.0
 *
 * @param string $option
 */
function caia_layout_option( $option )
{
	genesis_option( $option, CAIA_LAYOUT_SETTINGS_FIELD );
}

/**
 * Return the design option from database or cache.
 *
 * @since 1.0
 *
 * @param $option
 *
 * @return mixed
 */
function caia_get_design_option( $option )
{
	return genesis_get_option( $option, CAIA_DESIGN_SETTINGS_FIELD );
}

/**
 * Echo the design option.
 *
 * @since 1.0
 *
 * @param string $option
 */
function caia_design_option( $option )
{
	genesis_option( $option, CAIA_DESIGN_SETTINGS_FIELD );
}

/**
 * Retrive id of option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @param  string $settings_field
 * 
 * @return string
 */
function caia_get_field_id( $option, $settings_field = '' )
{
	$settings_field = empty( $settings_field ) ? CAIA_SETTINGS_FIELD : $settings_field;

	// return apply_filters( 'caia_get_field_id', sprintf( '%s[%s]', $settings_field, $option ), $option, $settings_field );
	
	return sprintf( '%s[%s]', $settings_field, $option );
}

/**
 * Echo the id of option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @param  string $settings_field
 * 
 * @return string
 */
function caia_field_id( $option, $settings_field = '' )
{
	echo caia_get_field_id( $option, $settings_field );
}

/**
 * Retrive the name of option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @param  string $settings_field
 * 
 * @return string
 */
function caia_get_field_name( $option, $settings_field = '' )
{
	$settings_field = empty( $settings_field ) ? CAIA_SETTINGS_FIELD : $settings_field;

	// return apply_filters( 'caia_get_field_name', sprintf( '%s[%s]', $settings_field, $option ), $option, $settings_field );

	return sprintf( '%s[%s]', $settings_field, $option );
}

/**
 * Echo the name of option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @param  string $settings_field
 * 
 * @return string
 */
function caia_field_name( $option, $settings_field = '' )
{
	echo caia_get_field_name( $option, $settings_field );
}

/**
 * Retrive field id of layout setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_get_layout_field_id( $option )
{
	return caia_get_field_id( $option, CAIA_LAYOUT_SETTINGS_FIELD );
}

/**
 * Echo field id of layout setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_layout_field_id( $option )
{
	caia_field_id( $option, CAIA_LAYOUT_SETTINGS_FIELD );
}

/**
 * Retrive field name of layout setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_get_layout_field_name( $option )
{
	return caia_get_field_name( $option, CAIA_LAYOUT_SETTINGS_FIELD );
}

/**
 * Echo field name of layout setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_layout_field_name( $option )
{
	caia_field_name( $option, CAIA_LAYOUT_SETTINGS_FIELD );
}



/**
 * Retrive field id of design setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_get_design_field_id( $option )
{
	return caia_get_field_id( $option, CAIA_DESIGN_SETTINGS_FIELD );
}

/**
 * Echo field id of design setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_design_field_id( $option )
{
	caia_field_id( $option, CAIA_DESIGN_SETTINGS_FIELD );
}

/**
 * Retrive field name of design setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_get_design_field_name( $option )
{
	return caia_get_field_name( $option, CAIA_DESIGN_SETTINGS_FIELD );
}

/**
 * Echo field name of design setting option
 *
 * @since  1.0.4
 * 
 * @param  string $option
 * @return string
 */
function caia_design_field_name( $option )
{
	caia_field_name( $option, CAIA_DESIGN_SETTINGS_FIELD );
}
