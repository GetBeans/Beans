<?php
/**
 * Tests for beans_register_wp_customize_options()
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WP_Customize;

use Beans\Framework\Tests\Unit\API\WP_Customize\Includes\WP_Customize_Test_Case;
use Brain\Monkey;
use Mockery;

require_once __DIR__ . '/includes/class-wp-customize-test-case.php';

/**
 * Class Tests_BeansRegisterWPCustomizeOptions
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize
 * @group   api
 * @group   api-wp-customize
 */
class Tests_BeansRegisterWPCustomizeOptions extends WP_Customize_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		Monkey\Functions\when( '_beans_pre_standardize_fields' )->returnArg();
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when not in the WP Customizer.
	 */
	public function test_should_return_false_when_no_customizer() {
		Monkey\Functions\expect( 'is_customize_preview' )
			->withNoArgs()
			->once()
			->ordered()
			->andReturn( false )
			->andAlsoExpectIt()
			->withNoArgs()
			->once()
			->ordered()
			->andReturn( false );

		Monkey\Functions\expect( 'beans_register_fields' )->never();

		$this->assertFalse( beans_register_wp_customize_options( [], '', [] ) );
		$this->assertFalse( beans_register_wp_customize_options( [], 'post_meta', [ 1, 2, 3 ] ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should return false when there are no options.
	 */
	public function test_should_return_false_when_no_options() {
		Monkey\Functions\expect( 'is_customize_preview' )
			->withNoArgs()
			->once()
			->ordered()
			->andReturn( true )
			->andAlsoExpectIt()
			->withNoArgs()
			->once()
			->ordered()
			->andReturn( true );

		Monkey\Functions\expect( 'beans_register_fields' )
			->with( [], 'wp_customize', '' )
			->once()
			->ordered()
			->andReturn( false )
			->andAlsoExpectIt()
			->with( [], 'wp_customize', 'post_meta' )
			->once()
			->ordered()
			->andReturn( false );

		$this->assertFalse( beans_register_wp_customize_options( [], '', [] ) );
		$this->assertFalse( beans_register_wp_customize_options( [], 'post_meta', [ 1, 2, 3 ] ) );
	}

	/**
	 * Test beans_register_wp_customize_options() should register the fields.
	 */
	public function test_should_register_fields() {
		$test_data = static::$test_data['single_fields'];

		$mocked_wp_customize = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $mocked_wp_customize; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Limited to test function scope.
		$mocked_wp_customize->shouldReceive( 'get_section' )->andReturn( true );

		Monkey\Functions\expect( 'is_customize_preview' )
			->withNoArgs()
			->once()
			->ordered()
			->andReturn( true );

		Monkey\Functions\expect( 'beans_get_fields' )
			->with( 'wp_customize', $test_data['section'] )
			->once()
			->ordered()
			->andReturn( [] );

		Monkey\Functions\expect( 'beans_add_attribute' )
			->with( 'beans_field_label', 'class', 'customize-control-title' )
			->once()
			->ordered()
			->andReturn( [] );

		Monkey\Functions\expect( 'beans_register_fields' )
			->with( $test_data['fields'], 'wp_customize', $test_data['section'] )
			->once()
			->ordered()
			->andReturn( true );

		beans_register_wp_customize_options( $test_data['fields'], $test_data['section'], $test_data['args'] );

		// Placeholder for PHPUnit, as it requires an assertion.  The real test is the "expect" above.
		$this->assertTrue( true );
	}
}
