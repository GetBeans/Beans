<?php
/**
 * Tests for the add_section() method of _Beans_WP_Customize.
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WP_Customize;

use Beans\Framework\Tests\Unit\API\WP_Customize\Includes\WP_Customize_Test_Case;
use _Beans_WP_Customize;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_BeansWPCustomize_AddSection
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansWPCustomize_AddSection extends WP_Customize_Test_Case {

	/**
	 * Test _Beans_WP_Customize::add_section() should call WP_Customize_Manager.
	 */
	public function test_should_call_wp_customize_manager() {
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

		$this->wp_customize_mock->shouldReceive( 'get_section' )->andReturn( true );

		$customizer  = new _Beans_WP_Customize( $test_data['section'], $test_data['args'] );
		$add_section = $this->get_reflective_method( 'add_section', '_Beans_WP_Customize' );

		$this->assertNull( $add_section->invoke( $customizer, $this->wp_customize_mock ) );
	}
}
