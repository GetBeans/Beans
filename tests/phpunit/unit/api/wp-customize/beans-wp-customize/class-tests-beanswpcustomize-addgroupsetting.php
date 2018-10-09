<?php
/**
 * Tests for the add_group_setting() method of Beans_WP_Customize.
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
 * Class Tests_BeansWPCustomize_AddGroupSetting
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansWPCustomize_AddGroupSetting extends WP_Customize_Test_Case {

	/**
	 * Test Beans_WP_Customize::add_group_setting() should call beans_get() when there are grouped fields.
	 */
	public function test_should_call_beans_get_when_grouped_fields() {
		$test_data = static::$test_data['group'];

		Monkey\Functions\expect( 'beans_get_fields' )
			->with( 'wp_customize', $test_data['section'] )
			->once()
			->ordered()
			->andReturn( [] );

		Monkey\Functions\expect( 'beans_add_attribute' )
			->with( 'beans_field_label', 'class', 'customize-control-title' )
			->once()
			->andReturn( true );

		Monkey\Functions\expect( 'beans_get' )
			->times( 3 )
			->andReturn( 'default' );

		$this->wp_customize_mock->shouldReceive( 'get_section', 'add_setting', 'add_section' )->andReturn( true );

		$customizer        = new Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_group_setting = $this->get_reflective_method( 'add_group_setting', 'Beans_WP_Customize' );

		$this->assertNull( $add_group_setting->invoke( $customizer, $this->wp_customize_mock, $test_data['fields'] ) );
	}

	/**
	 * Test Beans_WP_Customize::add_group_setting() should not call beans_get() when there are no grouped fields.
	 */
	public function test_should_not_call_beans_get_when_no_grouped_fields() {
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

		$this->wp_customize_mock->shouldReceive( 'get_section', 'add_setting', 'add_section' )->andReturn( true );

		$customizer        = new Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_group_setting = $this->get_reflective_method( 'add_group_setting', 'Beans_WP_Customize' );

		$this->assertNull( $add_group_setting->invoke( $customizer, $this->wp_customize_mock, $test_data['fields'] ) );
	}
}
