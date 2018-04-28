<?php
/**
 * Tests for beans_register_wp_customize_options()
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\WP_Customize;

use Beans\Framework\Tests\Integration\API\WP_Customize\Includes\WP_Customize_Test_Case;
use _Beans_Fields;

require_once __DIR__ . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_BeansRegisterWPCustomizeOptions
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansRegisterWPCustomizeOptions extends WP_Customize_Test_Case {

	/**
	 * Test beans_register_wp_customize_options() should return false when not in the WP Customizer .
	 */
	public function test_should_return_null_when_no_customizer() {
		$test_data = static::$test_data['single_fields'];

		$this->assertFalse( beans_register_wp_customize_options( array(), '', array() ) );
		$this->assertFalse( beans_register_wp_customize_options( array(), 'post_meta', array( 1, 2, 3 ) ) );
		$this->assertFalse( beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when there are no options.
	 */
	public function test_should_return_false_when_no_options() {
		$this->wp_customize->start_previewing_theme();

		$this->assertFalse( beans_register_wp_customize_options( array(), '', array() ) );
		$this->assertFalse( beans_register_wp_customize_options( array(), 'post_meta', array( 1, 2, 3 ) ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should register the fields.
	 */
	public function test_should_register_fields() {
		$this->wp_customize->start_previewing_theme();

		$test_data = static::$test_data['single_fields'];

		beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] );

		// Check what was registered.
		$registered_property = $this->get_reflective_property( 'registered', '_Beans_Fields' );
		$registered          = $registered_property->getValue( new _Beans_Fields() );
		$this->assertArrayHasKey( $test_data['section'], $registered['wp_customize'] );

		foreach ( $test_data['fields'] as $index => $field ) {
			$expected = $this->merge_field_with_default( $field );
			$this->assertSame( $expected, $registered['wp_customize']['tm-beans-test-customizer'][ $index ] );
		}
	}

	/**
	 * Test beans_register_wp_customize_options() should add section, settings and controls to the WP Customizer.
	 */
	public function test_should_add_section_settings_and_controls() {
		$this->wp_customize->start_previewing_theme();

		$test_data = static::$test_data['single_fields'];

		beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] );

		$this->assertNotEmpty( $this->wp_customize->get_section( 'tm-beans-test-customizer' ) );
		$this->assertNotEmpty( $this->wp_customize->get_control( 'beans_customizer_layout' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_customizer_layout' ) );
		$this->assertNotEmpty( $this->wp_customize->get_control( 'beans_customizer_checkbox' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_customizer_checkbox' ) );
		$this->assertNotEmpty( $this->wp_customize->get_control( 'beans_customizer_text' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_customizer_text' ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should add section and settings for grouped fields to the WP Customizer.
	 */
	public function test_should_add_section_and_settings_for_grouped_fields() {
		$this->wp_customize->start_previewing_theme();

		$test_data = static::$test_data['group'];

		beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] );

		$this->assertEmpty( $this->wp_customize->get_section( 'tm-beans-test-customizer' ) );
		$this->assertNotEmpty( $this->wp_customize->get_section( 'tm-beans-group-test-customizer' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_compile_all_scripts' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_compile_all_scripts_mode' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_checkbox_test' ) );
	}
}
