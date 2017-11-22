<?php
/**
 * Deprecated utility functions.
 *
 * @package Beans\Framework\API\Utilities
 */

/**
 * Deprecated. Sanitize HTML attributes from array to string.
 *
 * This functions has been replaced with {@see beans_esc_attributes()}.
 *
 * @since 1.0.0
 * @deprecated 1.3.1
 *
 * @param array $attributes The array key defines the attribute name and the array value define the
 *                          attribute value.
 *
 * @return string The sanitized attributes.
 */
function beans_sanatize_attributes( $attributes ) {
	_deprecated_function( __FUNCTION__, '1.3.1', 'beans_esc_attributes()' );

	return beans_esc_attributes( $attributes );
}

/**
 * Deprecated. Count recursive array.
 *
 * This function is unused in Beans.
 *
 * @since 1.0.0
 * @deprecated 1.5.0
 *
 * @param string   $array        The array.
 * @param int|bool $depth        Optional. Depth until which the entries should be counted.
 * @param bool     $count_parent Optional. Whether the parent should be counted or not.
 *
 * @return int Number of entries found.
 */
function beans_count_recursive( $array, $depth = false, $count_parent = true ) {
	_deprecated_function( __FUNCTION__, '1.5.0', 'beans_count_recursive()' );

	if ( ! is_array( $array ) ) {
		return 0;
	}

	if ( 1 === $depth ) {
		return count( $array );
	}

	if ( ! is_numeric( $depth ) ) {
		return count( $array, COUNT_RECURSIVE );
	}

	$count = $count_parent ? count( $array ) : 0;

	foreach ( $array as $_array ) {

		if ( is_array( $_array ) ) {
			$count += beans_count_recursive( $_array, $depth - 1, $count_parent );
		} else {
			$count ++;
		}
	}

	return $count;
}
