<?php
/**
 * Tests for the add() method of _Beans_WP_Customize.
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\WP_Customize;

use Beans\Framework\Tests\Integration\API\WP_Customize\Includes\WP_Customize_Test_Case;
use _Beans_WP_Customize;

require_once dirname( __DIR__ ) . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_BeansWPCustomize_Add
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansWPCustomize_Add extends WP_Customize_Test_Case {

	/**
	 * Test _Beans_WP_Customize::add() should add section, settings and controls to the WP Customizer.
	 */
	public function test_should_add_section_settings_and_controls_to_wp_customizer() {
		$test_data = static::$test_data['single_fields'];

		beans_register_fields( $test_data['fields'], 'wp_customize', $test_data['section'] );

		$customizer = new _Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add        = $this->get_reflective_method( 'add', '_Beans_WP_Customize' );

		$this->assertNull( $add->invoke( $customizer ) );

		$this->assertNotEmpty( $this->wp_customize->get_section( 'tm-beans-test-customizer' ) );
		$this->assertNotEmpty( $this->wp_customize->get_control( 'beans_customizer_layout' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_customizer_layout' ) );
		$this->assertNotEmpty( $this->wp_customize->get_control( 'beans_customizer_checkbox' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_customizer_checkbox' ) );
		$this->assertNotEmpty( $this->wp_customize->get_control( 'beans_customizer_text' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_customizer_text' ) );
	}

	/**
	 * Test _Beans_WP_Customize::add() should add section and settings of grouped fields to the WP Customizer.
	 */
	public function test_should_add_section_and_settings_of_grouped_fields_to_wp_customizer() {
		$test_data = static::$test_data['group'];

		beans_register_fields( $test_data['fields'], 'wp_customize', $test_data['section'] );

		$customizer = new _Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add        = $this->get_reflective_method( 'add', '_Beans_WP_Customize' );

		$this->assertNull( $add->invoke( $customizer ) );

		$this->assertEmpty( $this->wp_customize->get_section( 'tm-beans-test-customizer' ) );
		$this->assertNotEmpty( $this->wp_customize->get_section( 'tm-beans-group-test-customizer' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_compile_all_scripts' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_compile_all_scripts_mode' ) );
		$this->assertNotEmpty( $this->wp_customize->get_setting( 'beans_checkbox_test' ) );
	}
}
