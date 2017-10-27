<?php
/**
 * The Beans Utilities is a set of tools to ease building applications.
 *
 * Since these functions are used throughout the Beans framework and are therefore required, they are
 * loaded automatically when the Beans framework is included.
 *
 * @package API\Utilities
 */

/**
 * Calls function given by the first parameter and passes the remaining parameters as arguments.
 *
 * The main purpose of this function is to store the content echoed by a function in a variable.
 *
 * @since 1.0.0
 *
 * @param callback $callback The callback to be called.
 * @param mixed    $var      Additional parameters to be passed to the callback.
 *
 * @return string The callback content.
 */
function beans_render_function( $callback ) {

	if ( ! is_callable( $callback ) ) {
		return;
	}

	$args = func_get_args();

	ob_start();

		call_user_func_array( $callback, array_slice( $args, 1 ) );

	return ob_get_clean();

}

/**
 * Calls function given by the first parameter and passes the remaining parameters as arguments.
 *
 * The main purpose of this function is to store the content echoed by a function in a variable.
 *
 * @since 1.0.0
 *
 * @param callback $callback The callback to be called.
 * @param array    $params   Optional. The parameters to be passed to the callback, as an indexed array.
 *
 * @return string The callback content.
 */
function beans_render_function_array( $callback, $params = array() ) {

	if ( ! is_callable( $callback ) ) {
		return;
	}

	ob_start();

		call_user_func_array( $callback, $params );

	return ob_get_clean();

}

/**
 * Remove a directory and its files.
 *
 * @since 1.0.0
 *
 * @param string $dir_path Path to directory to remove.
 *
 * @return bool Will always return true.
 */
function beans_remove_dir( $dir_path ) {

	if ( ! is_dir( $dir_path ) ) {
		return false;
	}

	$items = scandir( $dir_path );
	unset( $items[0], $items[1] );

	foreach ( $items as $needle => $item ) {

		$path = $dir_path . '/' . $item;

		if ( 'dir' === filetype( $dir_path . '/' . $item ) ) {
			beans_remove_dir( $path );
		} else {
			@unlink( $path );
		}

		unset( $items[ $needle ] );

	}

	@rmdir( $dir_path );

	return true;

}

/**
 * Convert internal path to a url.
 *
 * This function must only be used with internal paths.
 *
 * @since 1.0.0
 *
 * @param string $path Path to be converted. Accepts absolute and relative internal paths.
 *
 * @return string Url.
 */
function beans_path_to_url( $path ) {

	static $root, $host;

	// Stop here if it is already a url or data format.
	if ( true == preg_match( '#^(http|https|\/\/|data)#', $path ) ) {
		return $path;
	}

	// Standardize backslashes.
	$path = wp_normalize_path( $path );

	// Set root and host if it isn't cached.
	if ( ! $root ) {

		// Standardize backslashes set host.
		$root = wp_normalize_path( untrailingslashit( ABSPATH ) );
		$host = untrailingslashit( site_url() );

		// Remove subfolder if necessary.
		if ( '' !== ( $subfolder = parse_url( $host, PHP_URL_PATH ) ) ) {

			$root = preg_replace( '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#', '', $root );
			$host = preg_replace( '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#', '', $host );

			// Add the blog path for multsites.
			if ( ! is_main_site() && ( $blogdetails = get_blog_details( get_current_blog_id() ) ) ) {
				if ( ! ( defined( 'WP_SITEURL' ) ) || ( defined( 'WP_SITEURL' ) && WP_SITEURL == site_url() ) ) {
					$host = untrailingslashit( $host ) . $blogdetails->path;
				}
			}
		}

		$explode = beans_get( 0, explode( '/' , trailingslashit( ltrim( $subfolder, '/' ) ) ) );

		// Maybe re-add tilde from host.
		if ( false !== stripos( $explode, '~' ) ) {
			$host = trailingslashit( $host ) . $explode;
		}
	}

	// Remove root if necessary.
	if ( false !== stripos( $path, $root ) ) {
		$path = str_replace( $root, '', $path );
	} elseif ( false !== stripos( $path, beans_get( 'DOCUMENT_ROOT', $_SERVER ) ) ) {
		$path = str_replace( beans_get( 'DOCUMENT_ROOT', $_SERVER ), '', $path );
	}

	return trailingslashit( $host ) . ltrim( $path, '/' );

}

/**
 * Convert internal url to a path.
 *
 * This function must only be used with internal urls.
 *
 * @since 1.0.0
 *
 * @param string $url Url to be converted. Accepts only internal urls.
 *
 * @return string Absolute path.
 */
function beans_url_to_path( $url ) {

	static $root, $blogdetails;

	// Stop here if it is not an internal url.
	if ( false === stripos( $url, parse_url( site_url(), PHP_URL_HOST ) ) ) {
		// If it is not an internal url we are having relative url instead.
		// Add ./ in front of the relative url to make it relative path.
		// After sanitize it will be converted to absolute path.
		return beans_sanitize_path( './' . $url );
	}

	// Fix protocole. It isn't needed to set SSL as it is only used to parse the URL.
	if ( preg_match( '#^(\/\/)#', $url ) ) {
		$url = 'http:' . $url;
	}

	// Parse url and standardize backslashes.
	$url = parse_url( $url, PHP_URL_PATH );
	$path = wp_normalize_path( $url );
	$explode = beans_get( 0, explode( '/' , trailingslashit( ltrim( $path, '/' ) ) ) );

	// Maybe remove tilde from path.
	if ( false !== stripos( $explode, '~' ) ) {
		$path = preg_replace( '#\~[^/]*\/#', '', $path );
	}

	// Set root if it isn't cached yet.
	if ( ! $root ) {

		// Standardize backslashes and remove windows drive for local installs.
		$root = wp_normalize_path( untrailingslashit( ABSPATH ) );
		$set_root = true;

	}

	// Remove subfolder if necessary.
	if ( '' !== ( $subfolder = parse_url( site_url(), PHP_URL_PATH ) ) ) {

		// Set root if it isn't cached.
		if ( isset( $set_root ) ) {

			// Remove subfolder.
			$root = preg_replace( '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#', '', $root );

			// Add an extra step which is only used for extremely rare case.
			if ( defined( 'WP_SITEURL' ) && '' !== ( $subfolder = parse_url( WP_SITEURL, PHP_URL_PATH ) ) ) {
				$root = preg_replace( '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#', '', $root );
			}
		}

		// Remove the blog path for multsites.
		if ( ! is_main_site() ) {

			// Set blogdetails if it isn't cached.
			if ( ! $blogdetails ) {
				$blogdetails = get_blog_details( get_current_blog_id() );
			}

			$path = preg_replace( '#^(\/?)' . trailingslashit( preg_quote( ltrim( $blogdetails->path, '/' ) ) ) . '#', '', $path );

		}
	}

	// Remove Windows drive for local installs if the root isn't cached yet.
	if ( isset( $set_root ) ) {
		$root = beans_sanitize_path( $root );
	}

	// Add root of it doesn't exist.
	if ( false === strpos( $path, $root ) ) {
		$path = trailingslashit( $root ) . ltrim( $path, '/' );
	}

	return beans_sanitize_path( $path );

}

/**
 * Sanitize path.
 *
 * @since 1.2.1
 *
 * @param string $path Path to be sanitize. Accepts absolute and relative internal paths.
 *
 * @return string Sanitize path.
 */
function beans_sanitize_path( $path ) {

	// Try to convert it to real path.
	if ( false !== realpath( $path ) ) {
		$path = realpath( $path );
	}

	// Remove Windows drive for local installs if the root isn't cached yet.
	$path = preg_replace( '#^[A-Z]\:#i', '', $path );

	return wp_normalize_path( $path );

}

/**
 * Get value from $_GET or defined $haystack.
 *
 * @since 1.0.0
 *
 * @param string $needle   Name of the searched key.
 * @param string $haystack Optional. Associative array. If false, $_GET is set to be the $haystack.
 * @param mixed  $default  Optional. Value returned if the searched key isn't found.
 *
 * @return string Value if found, $default otherwise.
 */
function beans_get( $needle, $haystack = false, $default = null ) {

	if ( false === $haystack ) {
		$haystack = $_GET;
	}

	$haystack = (array) $haystack;

	if ( isset( $haystack[ $needle ] ) ) {
		return $haystack[ $needle ];
	}

	return $default;

}

/**
 * Get value from $_POST.
 *
 * @since 1.0.0
 *
 * @param string $needle  Name of the searched key.
 * @param mixed  $default Optional. Value returned if the searched key isn't found.
 *
 * @return string Value if found, $default otherwise.
 */
function beans_post( $needle, $default = null ) {

	return beans_get( $needle, $_POST, $default );

}

/**
 * Get value from $_GET or $_POST superglobals.
 *
 * @since 1.0.0
 *
 * @param string $needle  Name of the searched key.
 * @param mixed  $default Optional. Value returned if the searched key isn't found.
 *
 * @return string Value if found, $default otherwise.
 */
function beans_get_or_post( $needle, $default = null ) {

	if ( $get = beans_get( $needle ) ) {
		return $get;
	}

	if ( $post = beans_post( $needle ) ) {
		return $post;
	}

	return $default;

}

/**
 * Count recursive array.
 *
 * This function is able to count a recursive array. The depth can be defined as well as if the parent should be
 * counted. For instance, if $depth is defined and $count_parent is set to false, only the level of the
 * defined depth will be counted.
 *
 * @since 1.0.0
 *
 * @param string   $array        The array.
 * @param int|bool $depth        Optional. Depth until which the entries should be counted.
 * @param bool     $count_parent Optional. Whether the parent should be counted or not.
 *
 * @return int Number of entries found.
 */
function beans_count_recursive( $array, $depth = false, $count_parent = true ) {

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
			$count += 1;
		}
	}

	return $count;

}

/**
 * Checks if a value exists in a multi-dimensional array.
 *
 * @since 1.0.0
 *
 * @param string $needle   The searched value.
 * @param array  $haystack The multi-dimensional array.
 * @param bool   $strict   If the third parameter strict is set to true, the beans_in_multi_array()
 *                         function will also check the types of the needle in the haystack.
 *
 * @return bool True if needle is found in the array, false otherwise.
 */
function beans_in_multi_array( $needle, $haystack, $strict = false ) {

	if ( in_array( $needle, $haystack, $strict ) ) {
		return true;
	}

	foreach ( (array) $haystack as $value ) {
		if ( is_array( $value ) && beans_in_multi_array( $needle , $value ) ) {
			return true;
		}
	}

	return false;

}

/**
 * Checks if a key or index exists in a multi-dimensional array.
 *
 * @since 1.0.0
 *
 * @param string $needle   The searched value.
 * @param array  $haystack The multi-dimensional array.
 *
 * @return bool True if needle is found in the array, False otherwise.
 */
function beans_multi_array_key_exists( $needle, $haystack ) {

	if ( array_key_exists( $needle, $haystack ) ) {
		return true;
	}

	foreach ( $haystack as $value ) {
		if ( is_array( $value ) && beans_multi_array_key_exists( $needle , $value ) ) {
			return true;
		}
	}

	return false;

}

/**
 * Search content for shortcodes and filter shortcodes through their hooks.
 *
 * Shortcodes must be delimited with curly brackets (e.g. {key}) and correspond to the searched array key.
 *
 * @since 1.0.0
 *
 * @param string $content Content containing the shortcode(s) delimited with curly brackets (e.g. {key}).
 *                        Shortcode(s) correspond to the searched array key and will be replaced by the array
 *                        value if found.
 * @param array $haystack The associative array used to replace shortcode(s).
 *
 * @return string Content with shortcodes filtered out.
 */
function beans_array_shortcodes( $content, $haystack ) {

	if ( preg_match_all( '#{(.*?)}#', $content, $matches ) ) {

		foreach ( $matches[1] as $needle ) {

			$sub_keys = explode( '.', $needle );
			$value = false;

			foreach ( $sub_keys as $sub_key ) {

				$search = $value ? $value : $haystack;
				$value = beans_get( $sub_key, $search );

			}

			if ( $value ) {
				$content = str_replace( '{' . $needle . '}', $value, $content );
			}
		}
	}

	return $content;

}

/**
 * Make sure the menu position is valid.
 *
 * If the menu position is unavailable, it will loop through the positions until one is found that is available.
 *
 * @since 1.0.0
 *
 * @param int $position The desired position.
 *
 * @return bool Valid postition.
 */
function beans_admin_menu_position( $position ) {

	global $menu;

	if ( ! is_array( $position ) ) {
		return $position;
	}

	if ( array_key_exists( $position, $menu ) ) {
		return beans_admin_menu_position( $position + 1 );
	}

	return $position;

}

/**
 * Sanitize HTML attributes from array to string.
 *
 * @since 1.0.0
 *
 * @param array $attributes The array key defines the attribute name and the array value define the
 *                          attribute value.
 *
 * @return string The escaped attributes.
 */
function beans_esc_attributes( $attributes ) {

	/**
	 * Filter attributes escaping methods.
	 *
	 * For all unspecified selectors, values are automatically escaped using
	 * {@link http://codex.wordpress.org/Function_Reference/esc_attr esc_attr()}.
	 *
	 * @since 1.3.1
	 *
	 * @param array $method Associative array of selectors as keys and escaping method as values.
	 */
	$methods = apply_filters( 'beans_escape_attributes_methods', array(
		'href'     => 'esc_url',
		'src'      => 'esc_url',
		'itemtype' => 'esc_url',
		'onclick'  => 'esc_js',
	) );

	$string = '';

	foreach ( (array) $attributes as $attribute => $value ) {

		if ( null !== $value ) {

			if ( $method = beans_get( $attribute, $methods ) ) {
				$value = call_user_func( $method, $value );
			} else {
				$value = esc_attr( $value );
			}

			$string .= $attribute . '="' . $value . '" ';

		}
	}

	return trim( $string );

}

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
