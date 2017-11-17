<?php
/**
 * The Beans Utilities is a set of tools to ease building applications.
 *
 * Since these functions are used throughout the Beans framework and are therefore required, they are
 * loaded automatically when the Beans framework is included.
 *
 * @package Beans\BeansFramework\API\Utilities
 *
 * @since   1.5.0
 */

/**
 * Calls function given by the first parameter and passes the remaining parameters as arguments.
 *
 * The main purpose of this function is to store the content echoed by a function in a variable.
 *
 * @since 1.0.0
 *
 * @param Callable $callback The callback to be called.
 * @param mixed    $args,... Optional. Additional parameters to be passed to the callback.
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
 * @param Callable $callback The callback to be called.
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
 * @return bool Returns true if the directory was removed; else, return false.
 */
function beans_remove_dir( $dir_path ) {

	if ( ! is_dir( $dir_path ) ) {
		return false;
	}

	$items = scandir( $dir_path );
	unset( $items[0], $items[1] );

	foreach ( $items as $needle => $item ) {
		$path = $dir_path . '/' . $item;

		if ( is_dir( $path ) ) {
			beans_remove_dir( $path );
		} else {
			@unlink( $path ); // phpcs:disable Generic.PHP.NoSilencedErrors.Discouraged - This is a valid use case.
		}
	}

	return @rmdir( $dir_path ); // phpcs:disable Generic.PHP.NoSilencedErrors.Discouraged - This is a valid use case.
}

/**
 * Convert internal path to a url.
 *
 * This function must only be used with internal paths.
 *
 * @since 1.5.0
 *
 * @param string $path          Path to be converted. Accepts absolute and relative internal paths.
 * @param bool   $force_rebuild Optional. Forces the rebuild of the root url and path.
 *
 * @return string Url.
 */
function beans_path_to_url( $path, $force_rebuild = false ) {
	static $root_path, $root_url;

	// Stop here if it is already a url or data format.
	if ( preg_match( '#^(http|https|\/\/|data)#', $path ) ) {
		return $path;
	}

	// Standardize backslashes.
	$path = wp_normalize_path( $path );

	// Set root and host if it isn't cached.
	if ( ! $root_path || true === $force_rebuild ) {

		// Standardize backslashes set host.
		$root_path = wp_normalize_path( untrailingslashit( ABSPATH ) );
		$root_url  = untrailingslashit( site_url() );

		// Remove subfolder if necessary.
		$subfolder = parse_url( $root_url, PHP_URL_PATH );

		if ( $subfolder && '/' !== $subfolder ) {
			$pattern   = '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#';
			$root_path = preg_replace( $pattern, '', $root_path );
			$root_url  = preg_replace( $pattern, '', $root_url );
		}

		// If it's a multisite and not the main site, then add the site's path.
		if ( ! is_main_site() ) {
			$blogdetails = get_blog_details( get_current_blog_id() );

			if ( $blogdetails && ( ! defined( 'WP_SITEURL' ) || ( defined( 'WP_SITEURL' ) && WP_SITEURL === site_url() ) ) ) {
				$root_url = untrailingslashit( $root_url ) . $blogdetails->path;
			}
		}

		// Maybe re-add tilde from host.
		$maybe_tilde = beans_get( 0, explode( '/', trailingslashit( ltrim( $subfolder, '/' ) ) ) );

		if ( false !== stripos( $maybe_tilde, '~' ) ) {
			$root_url = trailingslashit( $root_url ) . $maybe_tilde;
		}
	}

	// Remove root if necessary.
	if ( false !== stripos( $path, $root_path ) ) {
		$path = str_replace( $root_path, '', $path );
	} elseif ( false !== stripos( $path, beans_get( 'DOCUMENT_ROOT', $_SERVER ) ) ) {
		$path = str_replace( beans_get( 'DOCUMENT_ROOT', $_SERVER ), '', $path );
	}

	return trailingslashit( $root_url ) . ltrim( $path, '/' );
}

/**
 * Convert internal url to a path.
 *
 * This function must only be used with internal urls.
 *
 * @since 1.5.0
 *
 * @param string $url           Url to be converted. Accepts only internal urls.
 * @param bool   $force_rebuild Optional. Forces the rebuild of the root url and path.
 *
 * @return string Absolute path.
 */
function beans_url_to_path( $url, $force_rebuild = false ) {
	static $root_path, $blogdetails;
	$site_url = site_url();

	if ( true === $force_rebuild ) {
		$root_path   = '';
		$blogdetails = '';
	}

	// Fix protocol. It isn't needed to set SSL as it is only used to parse the URL.
	if ( ! parse_url( $url, PHP_URL_SCHEME ) ) {
		$original_url = $url;
		$url          = 'http://' . ltrim( $url, '/' );
	}

	// It's not an internal URL. Bail out.
	if ( false === stripos( parse_url( $url, PHP_URL_HOST ), parse_url( $site_url, PHP_URL_HOST ) ) ) {
		return isset( $original_url ) ? $original_url : $url;
	}

	// Parse url and standardize backslashes.
	$url  = parse_url( $url, PHP_URL_PATH );
	$path = wp_normalize_path( $url );

	// Maybe remove tilde from path.
	$trimmed_path = trailingslashit( ltrim( $path, '/' ) );
	$maybe_tilde  = beans_get( 0, explode( '/', $trimmed_path ) );

	if ( false !== stripos( $maybe_tilde, '~' ) ) {
		$ends_with_slash = substr( $path, - 1 ) === '/';
		$path            = preg_replace( '#\~[^/]*\/#', '', $trimmed_path );

		if ( $path && ! $ends_with_slash ) {
			$path = rtrim( $path, '/' );
		}
	}

	// Set root if it isn't cached yet.
	if ( ! $root_path ) {
		// Standardize backslashes and remove windows drive for local installs.
		$root_path = wp_normalize_path( untrailingslashit( ABSPATH ) );
		$set_root  = true;
	}

	/*
	 * If the subfolder exists for the root URL, then strip it off of the root path.
	 * Why? We don't want a double subfolder in the final path.
	 */
	$subfolder = parse_url( $site_url, PHP_URL_PATH );

	if ( isset( $set_root ) && $subfolder && '/' !== $subfolder ) {
		$root_path = preg_replace( '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#', '', $root_path );

		// Add an extra step which is only used for extremely rare case.
		if ( defined( 'WP_SITEURL' ) ) {
			$subfolder = parse_url( WP_SITEURL, PHP_URL_PATH );

			if ( '' !== $subfolder ) {
				$root_path = preg_replace( '#' . untrailingslashit( preg_quote( $subfolder ) ) . '$#', '', $root_path );
			}
		}
	}

	// Remove the blog path for multisites.
	if ( ! is_main_site() ) {

		// Set blogdetails if it isn't cached.
		if ( ! $blogdetails ) {
			$blogdetails = get_blog_details( get_current_blog_id() );
		}

		$path = preg_replace( '#^(\/?)' . trailingslashit( preg_quote( ltrim( $blogdetails->path, '/' ) ) ) . '#', '', $path );
	}

	// Remove Windows drive for local installs if the root isn't cached yet.
	if ( isset( $set_root ) ) {
		$root_path = beans_sanitize_path( $root_path );
	}

	// Add root of it doesn't exist.
	if ( false === strpos( $path, $root_path ) ) {
		$path = trailingslashit( $root_path ) . ltrim( $path, '/' );
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
 * @param mixed  $haystack Optional. The target to search. If false, $_GET is set to be the $haystack.
 * @param mixed  $default  Optional. Value to return if the needle isn't found.
 *
 * @return string Returns the value if found; else $default is returned.
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
 * @param mixed  $default Optional. Value to return if the needle isn't found.
 *
 * @return string Returns the value if found; else $default is returned.
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
 * @param mixed  $default Optional. Value to return if the needle isn't found.
 *
 * @return string Returns the value if found; else $default is returned.
 */
function beans_get_or_post( $needle, $default = null ) {
	$get = beans_get( $needle );

	if ( $get ) {
		return $get;
	}

	$post = beans_post( $needle );

	if ( $post ) {
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
			$count ++;
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
 * @return bool Returns true if needle is found in the array; else, false is returned.
 */
function beans_in_multi_array( $needle, $haystack, $strict = false ) {

	if ( in_array( $needle, $haystack, $strict ) ) {
		return true;
	}

	foreach ( (array) $haystack as $value ) {

		if ( is_array( $value ) && beans_in_multi_array( $needle, $value ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Checks if a key or index exists in a multi-dimensional array.
 *
 * @since 1.5.0
 *
 * @param string $needle   The key to search for within the haystack.
 * @param array  $haystack The array to be searched.
 *
 * @return bool Returns true if needle is found in the array; else, false is returned.
 */
function beans_multi_array_key_exists( $needle, array $haystack ) {

	if ( array_key_exists( $needle, $haystack ) ) {
		return true;
	}

	foreach ( $haystack as $value ) {

		if ( is_array( $value ) && beans_multi_array_key_exists( $needle, $value ) ) {
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
 * @param string $content  Content containing the shortcode(s) delimited with curly brackets (e.g. {key}).
 *                        Shortcode(s) correspond to the searched array key and will be replaced by the array
 *                        value if found.
 * @param array  $haystack The associative array used to replace shortcode(s).
 *
 * @return string Content with shortcodes filtered out.
 */
function beans_array_shortcodes( $content, $haystack ) {

	if ( preg_match_all( '#{(.*?)}#', $content, $matches ) ) {

		foreach ( $matches[1] as $needle ) {
			$sub_keys = explode( '.', $needle );
			$value    = false;

			foreach ( $sub_keys as $sub_key ) {
				$search = $value ? $value : $haystack;
				$value  = beans_get( $sub_key, $search );
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
 * @global    $menu
 *
 * @param int $position The desired position.
 *
 * @return bool Valid position.
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

		if ( null === $value ) {
			continue;
		}

		$method = beans_get( $attribute, $methods );

		if ( $method ) {
			$value = call_user_func( $method, $value );
		} else {
			$value = esc_attr( $value );
		}

		$string .= $attribute . '="' . $value . '" ';
	}

	return trim( $string );
}
