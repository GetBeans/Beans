<?php
/**
 * Page assets compiler.
 *
 * @ignore
 *
 * @package API\Compiler
 */
final class _Beans_Page_Compiler {

	/**
	 * Compiler dequeued scripts.
	 *
	 * @ignore
	 *
	 * @type array
	 */
	private $dequeued_scripts = array();

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'compile_page_styles' ), 9999 );
		add_action( 'wp_enqueue_scripts', array( $this, 'compile_page_scripts' ), 9999 );

	}

	/**
	 * Enqueue compiled wp styles.
	 */
	public function compile_page_styles() {

		if ( ! beans_get_component_support( 'wp_styles_compiler' ) || ! get_option( 'beans_compile_all_styles', false ) || _beans_is_compiler_dev_mode() ) {
			return;
		}

		if ( $styles = $this->compile_enqueued( 'style' ) ) {
			beans_compile_css_fragments( 'beans', $styles, array( 'version' => null ) );
		}

	}

	/**
	 * Enqueue compiled wp scripts.
	 */
	public function compile_page_scripts() {

		if ( ! beans_get_component_support( 'wp_scripts_compiler' ) || ! get_option( 'beans_compile_all_scripts', false ) || _beans_is_compiler_dev_mode() ) {
			return;
		}

		if ( $scripts = $this->compile_enqueued( 'script' ) ) {
			beans_compile_js_fragments( 'beans', $scripts, array(
				'in_footer' => ( 'aggressive' === get_option( 'beans_compile_all_scripts_mode', 'aggressive' ) ) ? true : false,
				'version'   => null,
			) );
		}

	}

	/**
	 * Compile all wp enqueued assets.
	 */
	private function compile_enqueued( $type, $depedencies = false ) {

		if ( ! $assets = beans_get( "wp_{$type}s", $GLOBALS ) ) {
			return array();
		}

		if ( 'script' == $type ) {
			add_action( 'wp_print_scripts', array( $this, 'dequeue_scripts' ), 9999 );
		}

		if ( ! $depedencies ) {
			$depedencies = $assets->queue;
		}

		$fragments = array();

		foreach ( $depedencies as $id ) {

			// Don't compile admin bar assets.
			if ( in_array( $id, array( 'admin-bar', 'open-sans', 'dashicons' ) ) ) {
				continue;
			}

			if ( ! $args = beans_get( $id, $assets->registered ) ) {
				continue;
			}

			if ( $args->deps ) {

				foreach ( $this->compile_enqueued( $type, $args->deps ) as $dep_id => $dep_src ) {

					if ( ! empty( $dep_src ) ) {
						$fragments[ $dep_id ] = $dep_src;
					}
				}
			}

			if ( 'style' == $type ) {

				// Add compiler media query if set.
				if ( 'all' != $args->args ) {
					$args->src = add_query_arg( array( 'beans_compiler_media_query' => $args->args ), $args->src );
				}

				$assets->done[] = $id;

			} elseif ( 'script' == $type ) {

				$this->dequeued_scripts[ $id ] = $args->src;

			}

			$fragments[ $id ] = $args->src;

		}

		return $fragments;

	}

	/**
	 * Dequeue scripts which have been compiled, grab localized
	 * data and add it inline.
	 */
	public function dequeue_scripts() {

		global $wp_scripts;

		if ( empty( $this->dequeued_scripts ) ) {
			return;
		}

		$localized = '';

		// Fetch the localized content and dequeue script.
		foreach ( $this->dequeued_scripts as $id => $src ) {

			if ( ! $args = beans_get( $id, $wp_scripts->registered ) ) {
				continue;
			}

			if ( isset( $args->extra['data'] ) ) {
				$localized .= $args->extra['data'] . "\n";
			}

			$wp_scripts->done[] = $id;

		}

		// Stop here if there isn't any content to add.
		if ( empty( $localized ) ) {
			return;
		}

		// Add localized content since it was removed with dequeue scripts.
		printf( "<script type='text/javascript'>\n%s\n</script>\n", $localized );

	}
}

new _Beans_Page_Compiler();
