<?php
/**
 * PHP Polyfills
 *
 * @package     Beans\Framework\API\Utilities
 * @since       1.5.0
 * @link        https://www.getbeans.io
 * @license     GNU-2.0+
 */

if ( ! function_exists( 'array_replace_recursive' ) ) {
	/**
	 * Replaces elements from passed arrays into the first array recursively.
	 *
	 * It replaces the values of array1 with the same values from all the following arrays.
	 * If a key from the first array exists in the second array, its value will be replaced by the value from the
	 * second array. If the key exists in the second array, and not the first, it will be created in the first array.
	 * If a key only exists in the first array, it will be left as is. If several arrays are passed for replacement,
	 * they will be processed in order, the later array overwriting the previous values.
	 *
	 * array_replace_recursive() is recursive : it will recurse into arrays and apply the same process to the inner value.
	 *
	 * When the value in array1 is scalar, it will be replaced by the value in array2, may it be scalar or array.
	 * When the value in array1 and array2 are both arrays, array_replace_recursive() will replace their respective
	 * value recursively.
	 *
	 * @see   PHP Manual
	 * @link  http://php.net/manual/en/function.array-replace-recursive.php
	 *
	 * Note: This function became available in PHP 5.3.0.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array1 The array in which elements are replaced.
	 * @param array $array2 The array from which elements will be extracted.
	 *
	 * @return array Returns an array, or NULL if an error occurs.
	 */
	function array_replace_recursive( array $array1, array $array2 ) {

		foreach ( $array2 as $key => $value ) {
			$from_base = beans_get( $key, $array1 );

			if ( is_array( $value ) && is_array( $from_base ) ) {
				$array1[ $key ] = array_replace_recursive( $from_base, $value ); // @codingStandardsIgnoreLine - PHPCompatibility.PHP.NewFunctions.array_replace_recursiveFound) - Sniffer is not picking up the polyfill.
			} else {
				$array1[ $key ] = $value;
			}
		}

		return $array1;
	}
}
