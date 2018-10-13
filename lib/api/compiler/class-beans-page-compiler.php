<?php
/**
 * This class compiles and minifies CSS, LESS and JS on pages.
 *
 * @package Beans\Framework\API\Compiler
 *
 * @since   1.0.0
 */

/**
 * Page assets compiler.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Compiler
 */
final class _Beans_Page_Compiler {

	/**
	 * Compiler dequeued scripts.
	 *
	 * @var array
	 */
	private $dequeued_scripts = array();

	/**
	 * An array of assets not to compile.
	 *
	 * @var array
	 */
	private $handles_not_to_compile = array( 'admin-bar', 'open-sans', 'dashicons' );

	/**
	 * An array of the handles that have been processed.
	 *
	 * @var array
	 */
	private $processed_handles = array();

	/**
	 * Initialize the hooks.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'compile_page_styles' ), 9999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'compile_page_scripts' ), 9999 );
	}

	/**
	 * Enqueue the compiled WP styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function compile_page_styles() {

		if ( $this->do_not_compile_styles() ) {
			return;
		}

		$this->processed_handles = array();
		$styles                  = $this->compile_enqueued( 'style' );

		if ( empty( $styles ) ) {
			return;
		}

		beans_compile_css_fragments( 'beans', $styles, array( 'version' => null ) );
	}

	/**
	 * Checks if the page's styles should not be compiled.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	private function do_not_compile_styles() {
		return ! beans_get_component_support( 'wp_styles_compiler' ) || ! get_option( 'beans_compile_all_styles', false ) || _beans_is_compiler_dev_mode();
	}

	/**
	 * Enqueue the compiled WP scripts.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function compile_page_scripts() {

		if ( $this->do_not_compile_scripts() ) {
			return;
		}

		$this->processed_handles = array();
		$scripts                 = $this->compile_enqueued( 'script' );

		if ( empty( $scripts ) ) {
			return;
		}

		$this->dequeued_scripts = $scripts;
		add_action( 'wp_print_scripts', array( $this, 'dequeue_scripts' ), 9999 );

		beans_compile_js_fragments(
			'beans',
			$scripts,
			array(
				'in_footer' => 'aggressive' === get_option( 'beans_compile_all_scripts_mode', 'aggressive' ),
				'version'   => null,
			)
		);
	}

	/**
	 * Checks if the page's scripts should not be compiled.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	private function do_not_compile_scripts() {
		return ! beans_get_component_support( 'wp_scripts_compiler' ) || ! get_option( 'beans_compile_all_scripts', false ) || _beans_is_compiler_dev_mode();
	}

	/**
	 * Compile all of the enqueued assets, i.e. all assets that are registered with WordPress.
	 *
	 * @since  1.0.0
	 * @ignore
	 * @access private
	 *
	 * @param string       $type         Type of asset, e.g. style or script.
	 * @param string|array $dependencies Optional. The asset's dependency(ies). Default is an empty string.
	 *
	 * @return array
	 */
	private function compile_enqueued( $type, $dependencies = '' ) {
		$assets = beans_get( "wp_{$type}s", $GLOBALS );

		if ( ! $assets ) {
			return array();
		}

		if ( ! $dependencies ) {
			$dependencies = $assets->queue;
		}

		$fragments = array();

		foreach ( $dependencies as $handle ) {

			if ( $this->do_not_compile_asset( $handle ) ) {
				continue;
			}

			if ( $this->did_handle( $handle ) ) {
				continue;
			}

			$asset = beans_get( $handle, $assets->registered );

			if ( ! $asset ) {
				continue;
			}

			$this->get_deps_to_be_compiled( $type, $asset, $fragments );

			if ( empty( $asset->src ) ) {
				continue;
			}

			if ( 'style' === $type ) {
				$this->maybe_add_media_query_to_src( $asset );

				$assets->done[] = $handle;
			}

			$fragments[ $handle ] = $asset->src;
		}

		return $fragments;
	}

	/**
	 * Checks if the handle has already been processed.  If no, it stores the handle.
	 *
	 * Note: This check eliminates processing dependencies that are in more than one asset.  For example, if more than
	 * one script requires 'jquery', then this check ensures we only process jquery's dependencies once.
	 *
	 * @since 1.5.0
	 *
	 * @param string $handle The asset's handle.
	 *
	 * @return bool
	 */
	private function did_handle( $handle ) {
		if ( in_array( $handle, $this->processed_handles, true ) ) {
			return true;
		}

		$this->processed_handles[] = $handle;

		return false;
	}

	/**
	 * When the args are not set to "all," adds the media query to the asset's src.
	 *
	 * @since 1.5.0
	 *
	 * @param _WP_Dependency $asset The given asset.
	 *
	 * @return void
	 */
	private function maybe_add_media_query_to_src( $asset ) {
		// Add compiler media query if set.
		if ( 'all' === $asset->args ) {
			return;
		}

		$asset->src = add_query_arg( array( 'beans_compiler_media_query' => $asset->args ), $asset->src );
	}

	/**
	 * Checks the given asset's handle to determine if it should not be compiled.
	 *
	 * @since 1.5.0
	 *
	 * @param string $handle The asset handle to check.
	 *
	 * @return bool
	 */
	private function do_not_compile_asset( $handle ) {
		return in_array( $handle, $this->handles_not_to_compile, true );
	}

	/**
	 * Get the asset's dependencies to be compiled.
	 *
	 * @since 1.5.0
	 *
	 * @param string         $type  Type of asset.
	 * @param _WP_Dependency $asset Instance of the asset.
	 * @param array          $srcs  Array of compiled asset srcs to be compiled. Passed by reference.
	 *
	 * @return void
	 */
	private function get_deps_to_be_compiled( $type, $asset, array &$srcs ) {

		if ( empty( $asset->deps ) ) {
			return;
		}

		foreach ( $this->compile_enqueued( $type, $asset->deps, true ) as $dep_handle => $dep_src ) {

			if ( empty( $dep_src ) ) {
				continue;
			}

			$srcs[ $dep_handle ] = $dep_src;
		}
	}

	/**
	 * Dequeue scripts which have been compiled, grab localized
	 * data and add it inline.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function dequeue_scripts() {

		if ( empty( $this->dequeued_scripts ) ) {
			return;
		}

		global $wp_scripts;
		$localized = '';

		// Fetch the localized content and dequeue script.
		foreach ( $this->dequeued_scripts as $handle => $src ) {
			$script = beans_get( $handle, $wp_scripts->registered );

			if ( ! $script ) {
				continue;
			}

			if ( isset( $script->extra['data'] ) ) {
				$localized .= $script->extra['data'] . "\n";
			}

			$wp_scripts->done[] = $handle;
		}

		if ( empty( $localized ) ) {
			return;
		}

		// Add localized content since it was removed with dequeue scripts.
		require dirname( __FILE__ ) . '/views/localized-content.php';
	}
}
