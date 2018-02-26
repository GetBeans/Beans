<?php
/**
 * This class handles the Beans WP Customize workflow.
 *
 * @package Beans\Framework\Api\WP_Customize
 *
 * @since 1.0.0
 */

/**
 * Handle the Beans WP Customize workflow.
 *
 * @since 1.0.0
 * @ignore
 * @access private
 *
 * @package Beans\Framework\API\WP_Customize
 */
final class _Beans_WP_Customize {

	/**
	 * Metabox arguments
	 *
	 * @var array
	 */
	private $args = array();

	/**
	 * Field section.
	 *
	 * @var string
	 */
	private $section;

	/**
	 * Constructor.
	 *
	 * @param string $section Field section.
	 * @param array  $args Metabox arguments.
	 */
	public function __construct( $section, $args ) {
		$defaults = array(
			'title'       => __( 'Undefined', 'tm-beans' ),
			'priority'    => 30,
			'description' => false,
		);

		$this->section = $section;
		$this->args    = array_merge( $defaults, $args );

		// Add section, settings and controls.
		$this->add();

		beans_add_attribute( 'beans_field_label', 'class', 'customize-control-title' );
	}

	/**
	 * Add section, settings and controls.
	 *
	 * @since 1.0.0
	 * @ignore
	 * @access private
	 *
	 * @return void
	 */
	private function add() {
		global $wp_customize;
		$this->add_section( $wp_customize );
		$fields = beans_get_fields( 'wp_customize', $this->section );

		foreach ( $fields as $field ) {

			if ( 'group' === $field['type'] ) {
				foreach ( $field['fields'] as $_field ) {
					$this->add_setting( $wp_customize, $_field );
				}
			}

			$this->add_setting( $wp_customize, $field );
			$this->add_control( $wp_customize, $field );
		}
	}

	/**
	 * Add Section.
	 *
	 * @since 1.0.0
	 * @ignore
	 * @access private
	 *
	 * @param WP_Customize_Manager $wp_customize WP Customizer Manager object.
	 *
	 * @return void
	 */
	private function add_section( $wp_customize ) {

		if ( $wp_customize->get_section( $this->section ) ) {
			return;
		}

		$wp_customize->add_section(
			$this->section,
			array(
				'title'       => $this->args['title'],
				'priority'    => $this->args['priority'],
				'description' => $this->args['description'],
			)
		);
	}

	/**
	 * Add setting.
	 *
	 * @since 1.0.0
	 * @ignore
	 * @access private
	 *
	 * @param WP_Customize_Manager $wp_customize WP Customizer Manager object.
	 * @param array                $field Metabox settings.
	 *
	 * @return void
	 */
	private function add_setting( $wp_customize, $field ) {
		$defaults = array(
			'db_type'    => 'theme_mod',
			'capability' => 'edit_theme_options',
			'transport'  => 'refresh',
		);

		$field = array_merge( $defaults, $field );

		$wp_customize->add_setting(
			$field['name'],
			array(
				'default'           => beans_get( 'default', $field ),
				'type'              => $field['db_type'],
				'capability'        => $field['capability'],
				'transport'         => $field['transport'],
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);
	}

	/**
	 * Add Control.
	 *
	 * @since 1.0.0
	 * @ignore
	 * @access private
	 *
	 * @param WP_Customize_Manager $wp_customize WP Customizer Manager object.
	 * @param array                $field Metabox settings.
	 *
	 * @return void
	 */
	private function add_control( $wp_customize, $field ) {
		$class = '_Beans_WP_Customize_Control';

		if ( $field['type'] !== $class && class_exists( $field['type'] ) ) {
			$class = $field['type'];
		}

		$wp_customize->add_control(
			new $class(
				$wp_customize,
				$field['name'],
				array(
					'label'   => $field['label'],
					'section' => $this->section,
				),
				$field
			)
		);
	}

	/**
	 * Sanatize value.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value.
	 *
	 * @return mixed
	 */
	public function sanitize( $value ) {
		return $value;
	}
}

if ( class_exists( 'WP_Customize_Control' ) ) :
	/**
	 * Render Beans fields content for WP Customize.
	 *
	 * @since 1.0.0
	 *
	 * @ignore
	 * @access private
	 */
	class _Beans_WP_Customize_Control extends WP_Customize_Control { // @codingStandardsIgnoreLine - Generic.Files.OneClassPerFile.MultipleFound.

		/**
		 * Field data.
		 *
		 * @var string
		 */
		private $beans_field;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$args = func_get_args();
			call_user_func_array( array( 'parent', '__construct' ), $args );
			$this->beans_field = end( $args );
		}

		/**
		 * Field content.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function render_content() {
			beans_field( $this->beans_field );
		}
	}

endif;
