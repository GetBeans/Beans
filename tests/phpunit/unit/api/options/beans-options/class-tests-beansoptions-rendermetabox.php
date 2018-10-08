<?php
/**
 * Tests for the render_metabox() method of Beans_Options.
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
 * Class Tests_BeansOptions_RenderMetabox
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_BeansOptions_RenderMetabox extends Options_Test_Case {

	/**
	 * Test Beans_Options::render_metabox() should return null when the section does not have fields registered.
	 */
	public function test_should_return_null_when_no_fields_registered() {
		$instance = new Beans_Options();

		foreach ( static::$test_data as $option ) {
			Monkey\Functions\expect( 'beans_get' )->with( 'page' )->once()->andReturn( 'beans_settings' );
			Monkey\Functions\expect( 'beans_get_fields' )->with( 'option', $option['section'] )->once()->andReturn( false );

			// Register the option.
			$instance->register( $option['section'], $option['args'] );

			// Run the test.
			$this->assertNull( $instance->render_metabox() );
		}
	}

	/**
	 * Test Beans_Options::render_metabox() should render the registered fields.  For this test, we'll not render but rather check that
	 * each function is called as expected.
	 */
	public function test_should_render_registered_fields() {
		$instance = new Beans_Options();

		foreach ( static::$test_data as $option ) {
			Monkey\Functions\expect( 'beans_get' )->with( 'page' )->once()->andReturn( 'beans_settings' );
			Monkey\Functions\expect( 'beans_get_fields' )->with( 'option', $option['section'] )->once()->andReturn( $option['fields'] );

			// Register the option.
			$instance->register( $option['section'], $option['args'] );

			// Run the tests.
			Monkey\Functions\expect( 'beans_field' )->times( count( $option['fields'] ) )->andReturn( null );
			$this->assertNull( $instance->render_metabox() );
		}
	}
}
