<?php
/**
 * Tests for the register() method of Beans_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Options;

use Beans_Options;
use Beans\Framework\Tests\Unit\API\Options\Includes\Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansOptions_Register
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_BeansOptions_Register extends Options_Test_Case {

	/**
	 * Test Beans_Options::register() should merge the default arguments and set the property.
	 */
	public function test_should_merge_default_args_and_set_property() {
		$property = $this->get_reflective_property( 'args', 'Beans_Options' );

		$instance = new Beans_Options();
		$instance->register( 'foo', [] );
		$this->assertSame(
			[
				'title'   => 'Undefined',
				'context' => 'normal',
			],
			$property->getValue( $instance )
		);

		$instance = new Beans_Options();
		$instance->register( 'beans_test', [ 'title' => 'Beans Tests' ] );
		$this->assertSame(
			[
				'title'   => 'Beans Tests',
				'context' => 'normal',
			],
			$property->getValue( $instance )
		);
	}

	/**
	 * Test Beans_Options::register() should register the callback to the 'admin_enqueue_scripts' hook.
	 */
	public function test_should_register_callback_to_admin_enqueue_scripts_hook() {
		$instance = new Beans_Options();
		$instance->register( 'beans_test', [ 'title' => 'Beans Tests' ] );

		$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $instance, 'enqueue_assets' ] ) );
	}

	/**
	 * Test Beans_Options::register() should register the metabox with WordPress.
	 */
	public function test_should_register_metabox_with_wp() {
		$instance = new Beans_Options();

		foreach ( static::$test_data as $option ) {
			Monkey\Functions\expect( 'beans_get' )->with( 'page' )->once()->andReturn( 'beans_settings' );

			// Register the option.
			$instance->register( $option['section'], $option['args'] );

			// Check that the metabox is registered with WordPress.
			global $wp_meta_boxes;
			$this->assertArrayHasKey( 'beans_settings', $wp_meta_boxes );
			$this->assertArrayHasKey( $option['args']['context'], $wp_meta_boxes['beans_settings'] );
			$this->assertArrayHasKey( 'default', $wp_meta_boxes['beans_settings'][ $option['args']['context'] ] );
			$this->assertArrayHasKey( $option['section'], $wp_meta_boxes['beans_settings'][ $option['args']['context'] ]['default'] );
			$this->assertSame(
				[
					'id'       => $option['section'],
					'title'    => $option['args']['title'],
					'callback' => [ $instance, 'render_metabox' ],
					'args'     => null,
				],
				$wp_meta_boxes['beans_settings'][ $option['args']['context'] ]['default'][ $option['section'] ]
			);

			// Clean up.
			remove_action( 'admin_enqueue_scripts', [ $instance, 'enqueue_assets' ] );
		}
	}
}
