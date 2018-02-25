<?php
/**
 * This class provides the means to set the options and actions of the Beans Compiler.
 *
 * @package Beans\Framework\API\Compiler
 *
 * @since   1.0.0
 */

/**
 * Options and Actions used by Beans Compiler.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Compiler
 */
final class _Beans_Compiler_Options {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'admin_init', array( $this, 'flush' ), -1 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		add_action( 'beans_field_flush_cache', array( $this, 'option' ) );
		add_action( 'beans_field_description_beans_compile_all_styles_append_markup', array( $this, 'maybe_disable_style_notice' ) );
		add_action( 'beans_field_description_beans_compile_all_scripts_group_append_markup', array( $this, 'maybe_disable_scripts_notice' ) );
	}

	/**
	 * Register options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		$fields = array(
			array(
				'id'          => 'beans_compiler_items',
				'type'        => 'flush_cache',
				'description' => __( 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.', 'tm-beans' ),
			),
		);

		// Add the styles compiler option only if it is supported.
		if ( beans_get_component_support( 'wp_styles_compiler' ) ) {
			$fields = array_merge( $fields, array(
				array(
					'id'             => 'beans_compile_all_styles',
					'label'          => false,
					'checkbox_label' => __( 'Compile all WordPress styles', 'tm-beans' ),
					'type'           => 'checkbox',
					'default'        => false,
					'description'    => __( 'Compile and cache all the CSS files that have been enqueued to the WordPress head.', 'tm-beans' ),
				),
			) );
		}

		// Add the scripts compiler option only if it is supported.
		if ( beans_get_component_support( 'wp_scripts_compiler' ) ) {
			$fields = array_merge( $fields, array(
				array(
					'id'          => 'beans_compile_all_scripts_group',
					'label'       => __( 'Compile all WordPress scripts', 'tm-beans' ),
					'type'        => 'group',
					'fields'      => array(
						array(
							'id'      => 'beans_compile_all_scripts',
							'type'    => 'activation',
							'default' => false,
						),
						array(
							'id'         => 'beans_compile_all_scripts_mode',
							'type'       => 'select',
							'default'    => 'aggressive',
							'attributes' => array( 'style' => 'margin: -3px 0 0 -8px;' ),
							'options'    => array(
								'aggressive' => __( 'Aggressive', 'tm-beans' ),
								'standard'   => __( 'Standard', 'tm-beans' ),
							),
						),
					),
					'description' => __( 'Compile and cache all the Javascript files that have been enqueued to the WordPress head.<!--more-->JavaSript is outputted in the footer if the level is set to <strong>Aggressive</strong> and might conflict with some third party plugins which are not following WordPress standards.', 'tm-beans' ),
				),
			) );
		}

		beans_register_options( $fields, 'beans_settings', 'compiler_options', array(
			'title'   => __( 'Compiler options', 'tm-beans' ),
			'context' => 'normal',
		) );
	}

	/**
	 * Flush images for all folders set.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function flush() {

		if ( ! beans_post( 'beans_flush_compiler_cache' ) ) {
			return;
		}

		beans_remove_dir( beans_get_compiler_dir() );
	}

	/**
	 * Cache cleaner notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function admin_notice() {

		if ( ! beans_post( 'beans_flush_compiler_cache' ) ) {
			return;
		}

		?>
		<div id="message" class="updated"><p><?php esc_html_e( 'Cache flushed successfully!', 'tm-beans' ); ?></p></div>
		<?php
	}

	/**
	 * Add button used to flush cache.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Registered options.
	 *
	 * @return void
	 */
	public function option( $field ) {

		if ( 'beans_compiler_items' !== $field['id'] ) {
			return;
		}

		?>
		<input type="submit" name="beans_flush_compiler_cache" value="<?php esc_html_e( 'Flush assets cache', 'tm-beans' ); ?>" class="button-secondary" />
		<?php
	}

	/**
	 * Maybe show disabled notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_disable_style_notice() {

		if ( get_option( 'beans_compile_all_styles' ) && _beans_is_compiler_dev_mode() ) {
			?>
			<br />
			<span style="color: #d85030;"><?php esc_html_e( 'Styles are not compiled in development mode.', 'tm-beans' ); ?></span>
			<?php
		}
	}

	/**
	 * Maybe show disabled notice.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function maybe_disable_scripts_notice() {

		if ( get_option( 'beans_compile_all_scripts' ) && _beans_is_compiler_dev_mode() ) {
			?>
			<br />
			<span style="color: #d85030;"><?php esc_html_e( 'Scripts are not compiled in development mode.', 'tm-beans' ); ?></span>
			<?php
		}
	}
}

new _Beans_Compiler_Options();
