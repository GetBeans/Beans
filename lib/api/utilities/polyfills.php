<?php
/**
 * PHP Polyfills
 *
 * @package     Beans\BeansFramework\API\Utilities
 * @since       1.0.0
 * @link        http://www.getbeans.io
 * @license     GNU-2.0+
 */

if ( ! function_exists( 'array_replace_recursive' ) ) {
	/**
	 * PHP 5.2 fallback.
	 *
	 * @ignore
	 */
	function array_replace_recursive( $base, $replacements ) {
		if ( ! is_array( $base ) || ! is_array( $replacements ) ) {
			return $base;
		}

		foreach ( $replacements as $key => $value ) {
			if ( is_array( $value ) && is_array( $from_base = beans_get( $key, $base ) ) ) {
				$base[ $key ] = array_replace_recursive( $from_base, $value );
			} else {
				$base[ $key ] = $value;
			}
		}

		return $base;
	}
}
