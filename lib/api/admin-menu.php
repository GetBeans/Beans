<?php
/**
 * Beans admin page.
 *
 * @ignore
 */
final class _Beans_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menu' ), 150 );
		add_action( 'admin_init', array( $this, 'register' ), 20 );

	}

	/**
	 * Add beans menu.
	 */
	public function admin_menu() {

		add_theme_page( __( 'Settings', 'tm-beans' ), __( 'Settings', 'tm-beans' ), 'manage_options', 'beans_settings', array( $this, 'display_screen' ) );

	}

	/**
	 * Beans options page content.
	 */
	public function display_screen() {

		?>
		<div class="wrap">
			<h2><?php _e( 'Beans Settings', 'tm-beans' ); ?><span style="float: right; font-size: 10px; color: #888;"><?php _e( 'Version ', 'tm-beans' ); echo esc_attr( BEANS_VERSION ); ?></span></h2>
			<?php beans_options( 'beans_settings' ); ?>
		</div>
		<?php

	}

	/**
	 * Register options.
	 */
	public function register() {

		global $wp_meta_boxes;

		$fields = array(
			array(
				'id'             => 'beans_dev_mode',
				'checkbox_label' => __( 'Enable development mode', 'tm-beans' ),
				'type'           => 'checkbox',
				'description'    => __( 'This option should be enabled while your website is in development.', 'tm-beans' ),
			),
		);

		beans_register_options( $fields, 'beans_settings', 'mode_options', array(
			'title'   => __( 'Mode options', 'tm-beans' ),
			'context' => beans_get( 'beans_settings', $wp_meta_boxes ) ? 'column' : 'normal', // Check for other beans boxes.
		) );

	}
}

new _Beans_Admin();
