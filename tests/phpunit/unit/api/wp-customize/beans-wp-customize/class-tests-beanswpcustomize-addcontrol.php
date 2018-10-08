<?php
/**
 * Tests for the add_control() method of Beans_WP_Customize.
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WP_Customize;

use Beans\Framework\Tests\Unit\API\WP_Customize\Includes\WP_Customize_Test_Case;
use Beans_WP_Customize;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_BeansWPCustomize_AddControl
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansWPCustomize_AddControl extends WP_Customize_Test_Case {

	/**
	 * Test Beans_WP_Customize::add_control() should call Beans_WP_Customize_Control and WP_Customize_Manager.
	 */
	public function test_should_call_beans_wp_customize_control_and_wp_customize_manager() {
		$test_data = static::$test_data['single_fields'];

		Monkey\Functions\expect( 'beans_get_fields' )
			->with( 'wp_customize', $test_data['section'] )
			->once()
			->ordered()
			->andReturn( [] );

		Monkey\Functions\expect( 'beans_add_attribute' )
			->with( 'beans_field_label', 'class', 'customize-control-title' )
			->once()
			->andReturn( true );

		$this->wp_customize_mock->shouldReceive( 'get_section', 'add_setting', 'add_section', 'add_control' )->andReturn( true );

		$mocked_beans_customize_control = Mockery::mock( 'Beans_WP_Customize_Control' );

		$customizer  = new Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_control = $this->get_reflective_method( 'add_control', 'Beans_WP_Customize' );

		$this->assertNull( $add_control->invoke( $customizer, $this->wp_customize_mock, $test_data['fields'] ) );
	}
}
