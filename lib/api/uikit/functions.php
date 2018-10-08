<?php
/**
 * The Beans UIkit component integrates the awesome {@link https://getuikit.com/v2/ UIkit 2 framework}.
 *
 * Only the selected components are compiled into a single cached file and can be different on a per page basis.
 * UIkit default or custom themes can be enqueued to the UIkit compiler. All UIkit LESS variables are accessible
 * and overwritable via custom themes.
 *
 * When development mode is enabled, file changes will automatically be detected. This makes it very easy
 * to style UIkit themes using LESS.
 *
 * @package Beans\Framework\API\UIkit
 *
 * @since   1.0.0
 */

/**
 * Enqueue UIkit components.
 *
 * Enqueued components will be compiled into a single file. Refer to
 * {@link https://getuikit.com/v2/ UIkit 2} to learn more about the available components.
 *
 * When development mode is enabled, file changes will automatically be detected. This makes it very easy
 * to style UIkit themes using LESS.
 *
 * This function must be called in the 'beans_uikit_enqueue_scripts' action hook.
 *
 * @since 1.0.0
 *
 * @param string|array|bool $components Name of the component(s) to include as an indexed array. The name(s) must be
 *                                      the UIkit component filename without the extension (e.g. 'grid'). Set to true
 *                                      to load all components.
 * @param string            $type       Optional. Type of UIkit components ('core' or 'add-ons').
 * @param bool              $autoload   Optional. Automatically include components dependencies.
 *
 * @return void
 */
function beans_uikit_enqueue_components( $components, $type = 'core', $autoload = true ) {
	global $_beans_uikit_enqueued_items;

	// Get all uikit components.
	if ( true === $components ) {
		$components = beans_uikit_get_all_components( $type );
	} elseif ( $autoload ) {
		_beans_uikit_autoload_dependencies( $components );
	}

	// Add components into the registry.
	$_beans_uikit_enqueued_items['components'][ $type ] = beans_join_arrays_clean( (array) $_beans_uikit_enqueued_items['components'][ $type ], (array) $components );
}

/**
 * Dequeue UIkit components.
 *
 * Dequeued components are removed from the UIkit compiler. Refer to
 * {@link https://getuikit.com/v2/ UIkit 2} to learn more about the available components.
 *
 * When development mode is enabled, file changes will automatically be detected. This makes it very easy
 * to style UIkit themes using LESS.
 *
 * This function must be called in the 'beans_uikit_enqueue_scripts' action hook.
 *
 * @since 1.0.0
 *
 * @param string|array $components Name of the component(s) to exclude as an indexed array. The name(s) must be
 *                                 the UIkit component filename without the extention (e.g. 'grid'). Set to true
 *                                 to exclude all components.
 * @param string       $type       Optional. Type of UIkit components ('core' or 'add-ons').
 *
 * @return void
 */
function beans_uikit_dequeue_components( $components, $type = 'core' ) {
	global $_beans_uikit_enqueued_items;

	// When true, remove all of the components from the registry.
	if ( true === $components ) {
		$_beans_uikit_enqueued_items['components'][ $type ] = array();

		return;
	}

	// Remove components.
	$_beans_uikit_enqueued_items['components'][ $type ] = array_diff( (array) $_beans_uikit_enqueued_items['components'][ $type ], (array) $components );
}

/**
 * Register a UIkit theme.
 *
 * The theme must not contain sub-folders. Component files in the theme will be automatically combined to
 * the UIkit compiler if that component is used.
 *
 * This function must be called in the 'beans_uikit_enqueue_scripts' action hook.
 *
 * @since 1.0.0
 *
 * @param string $id   A unique string used as a reference. Similar to the WordPress scripts $handle argument.
 * @param string $path Absolute path to the UIkit theme folder.
 *
 * @return bool False on error or if already exists, true on success.
 */
function beans_uikit_register_theme( $id, $path ) {
	global $_beans_uikit_registered_items;

	// Stop here if already registered.
	if ( beans_get( $id, $_beans_uikit_registered_items['themes'] ) ) {
		return true;
	}

	if ( ! $path ) {
		return false;
	}

	if ( beans_str_starts_with( $path, 'http' ) ) {
		$path = beans_url_to_path( $path );
	}

	$_beans_uikit_registered_items['themes'][ $id ] = trailingslashit( $path );

	return true;
}

/**
 * Enqueue a UIkit theme.
 *
 * The theme must not contain sub-folders. Component files in the theme will be automatically combined to
 * the UIkit compiler if that component is used.
 *
 * This function must be called in the 'beans_uikit_enqueue_scripts' action hook.
 *
 * @since 1.0.0
 *
 * @param string $id   A unique string used as a reference. Similar to the WordPress scripts $handle argument.
 * @param string $path Optional. Path to the UIkit theme folder if the theme isn't yet registered.
 *
 * @return bool False on error, true on success.
 */
function beans_uikit_enqueue_theme( $id, $path = false ) {

	// Make sure it is registered, if not, try to do so.
	if ( ! beans_uikit_register_theme( $id, $path ) ) {
		return false;
	}

	global $_beans_uikit_enqueued_items;

	$_beans_uikit_enqueued_items['themes'][ $id ] = _beans_uikit_get_registered_theme( $id );

	return true;
}

/**
 * Dequeue a UIkit theme.
 *
 * This function must be called in the 'beans_uikit_enqueue_scripts' action hook.
 *
 * @since 1.0.0
 *
 * @param string $id The id of the theme to dequeue.
 *
 * @return void
 */
function beans_uikit_dequeue_theme( $id ) {
	global $_beans_uikit_enqueued_items;
	unset( $_beans_uikit_enqueued_items['themes'][ $id ] );
}

/**
 * Get all of the UIkit components for the given type, i.e. for core or add-ons.
 *
 * @since 1.5.0
 *
 * @param string $type Optional. Type of UIkit components ('core' or 'add-ons').
 *
 * @return array
 */
function beans_uikit_get_all_components( $type = 'core' ) {
	$uikit = new Beans_Uikit();

	return $uikit->get_all_components( $type );
}

/**
 * Get all of the UIkit dependencies for the given component(s).
 *
 * @since 1.5.0
 *
 * @param string|array $components Name of the component(s) to process. The name(s) must be
 *                                 the UIkit component filename without the extension (e.g. 'grid').
 *
 * @return array
 */
function beans_uikit_get_all_dependencies( $components ) {
	$uikit = new Beans_Uikit();

	return $uikit->get_autoload_components( (array) $components );
}

/**
 * Autoload all the component dependencies.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param string|array $components Name of the component(s) to include as an indexed array. The name(s) must be
 *                                 the UIkit component filename without the extension (e.g. 'grid').
 *
 * @return void
 */
function _beans_uikit_autoload_dependencies( $components ) {

	foreach ( beans_uikit_get_all_dependencies( $components ) as $type => $autoload ) {
		beans_uikit_enqueue_components( $autoload, $type, false );
	}
}

/**
 * Initialize registered UIkit items global.
 *
 * @ignore
 * @access private
 */
global $_beans_uikit_registered_items;

if ( ! isset( $_beans_uikit_registered_items ) ) {
	$_beans_uikit_registered_items = array(
		'themes' => array(
			'default'         => BEANS_API_PATH . 'uikit/src/themes/default',
			'almost-flat'     => BEANS_API_PATH . 'uikit/src/themes/almost-flat',
			'gradient'        => BEANS_API_PATH . 'uikit/src/themes/gradient',
			'wordpress-admin' => BEANS_API_PATH . 'uikit/themes/wordpress-admin',
		),
	);
}

/**
 * Initialize enqueued UIkit items global.
 *
 * @ignore
 * @access private
 */
global $_beans_uikit_enqueued_items;

if ( ! isset( $_beans_uikit_enqueued_items ) ) {
	$_beans_uikit_enqueued_items = array(
		'components' => array(
			'core'    => array(),
			'add-ons' => array(),
		),
		'themes'     => array(),
	);
}

/**
 * Get the path for the given theme ID, if the theme is registered.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param string $id The theme ID to get.
 *
 * @return string|bool Returns false if the theme is not registered.
 */
function _beans_uikit_get_registered_theme( $id ) {
	global $_beans_uikit_registered_items;

	return beans_get( $id, $_beans_uikit_registered_items['themes'], false );
}

add_action( 'wp_enqueue_scripts', '_beans_uikit_enqueue_assets', 7 );
/**
 * Enqueue UIkit assets.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return void
 */
function _beans_uikit_enqueue_assets() {

	if ( ! has_action( 'beans_uikit_enqueue_scripts' ) ) {
		return;
	}

	/**
	 * Fires when UIkit scripts and styles are enqueued.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_uikit_enqueue_scripts' );

	// Compile everything.
	$uikit = new Beans_Uikit();
	$uikit->compile();
}

add_action( 'admin_enqueue_scripts', '_beans_uikit_enqueue_admin_assets', 7 );
/**
 * Enqueue UIkit admin assets.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @return void
 */
function _beans_uikit_enqueue_admin_assets() {

	if ( ! has_action( 'beans_uikit_admin_enqueue_scripts' ) ) {
		return;
	}

	/**
	 * Fires when admin UIkit scripts and styles are enqueued.
	 *
	 * @since 1.0.0
	 */
	do_action( 'beans_uikit_admin_enqueue_scripts' );

	// Compile everything.
	$uikit = new Beans_Uikit();
	$uikit->compile();
}
