<?php
/**
 * Options and Actions used by Beans Compiler.
 *
 * @ignore
 *
 * @package API\Compiler
 */
class _Beans_Compiler_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_init', array( $this, 'flush' ) , -1 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		add_action( 'beans_field_flush_cache', array( $this, 'option' ) );
		add_action( 'beans_field_description_beans_compile_all_styles_append_markup', array( $this, 'maybe_disable_style_notice' ) );
		add_action( 'beans_field_description_beans_compile_all_scripts_append_markup', array( $this, 'maybe_disable_scripts_notice' ) );

	}


	/**
	 * Register options.
	 */
	public function register() {

		$fields = array(
			array(
				'id' => 'beans_compiler_items',
				'type' => 'flush_cache',
				'description' => __( 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.', 'tm-beans' )
			)
		);

		// Add styles compiler option only if supported
		if ( beans_get_component_support( 'wp_styles_compiler' ) )
			$fields = array_merge( $fields, array(
				array(
					'id' => 'beans_compile_all_styles',
					'label' => false,
					'checkbox_label' => __( 'Compile all WordPress styles', 'tm-beans' ),
					'type' => 'checkbox',
					'default' => false,
					'description' => __( 'Compile and cache all the CSS files that have been enqueued to the WordPress head.', 'tm-beans' )
				)
			) );

		// Add scripts compiler option only if supported
		if ( beans_get_component_support( 'wp_scripts_compiler' ) )
			$fields = array_merge( $fields, array(
				array(
					'id' => 'beans_compile_all_scripts',
					'label' => false,
					'checkbox_label' => __( 'Compile all WordPress scripts', 'tm-beans' ),
					'type' => 'checkbox',
					'default' => false,
					'description' => __( 'Compile and cache all the Javascript files that have been enqueued to the WordPress head.', 'tm-beans' )
				)
			) );

		beans_register_options( $fields, 'beans_settings', 'compiler_options', array(
			'title' => __( 'Compiler options', 'tm-beans' ),
			'context' => 'normal'
		) );

	}


	/**
	 * Flush images for all folders set.
	 */
	public function flush() {

		if ( !beans_post( 'beans_flush_compiler_cache' ) )
			return;

		beans_remove_dir( beans_get_compiler_dir() );

	}


	/**
	 * Cache cleaner notice.
	 */
	public function admin_notice() {

		if ( !beans_post( 'beans_flush_compiler_cache' ) )
			return;

		echo '<div id="message" class="updated"><p>' . __( 'Cache flushed successfully!', 'tm-beans' ) . '</p></div>' . "\n";

	}


	/**
	 * Add button used to flush cache.
	 */
	public function option( $field ) {

		if ( $field['id'] !== 'beans_compiler_items' )
			return;

		echo '<input type="submit" name="beans_flush_compiler_cache" value="' . __( 'Flush assets cache', 'tm-beans' ) . '" class="button-secondary" />';

	}


	/**
	 * Maybe show disabled notice.
	 */
	public function maybe_disable_style_notice() {

		if ( get_option( 'beans_compile_all_styles' ) && _beans_is_compiler_dev_mode() )
			echo '<br /><span style="color: #d85030;">' . __( 'Styles are not compiled in development mode.', 'tm-beans' ) . '</span>';

	}

	/**
	 * Maybe show disabled notice.
	 */
	public function maybe_disable_scripts_notice() {

		if ( get_option( 'beans_compile_all_scripts' ) && _beans_is_compiler_dev_mode() )
			echo '<br /><span style="color: #d85030;">' . __( 'Scripts are not compiled in development mode.', 'tm-beans' ) . '</span>';

	}

}

new _Beans_Compiler_Options();
