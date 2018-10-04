<?php
/**
 * Tests for the register() method of _Beans_Options.
 *
 * @package Beans\Framework\Tests\Integration\API\Options
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Options;

use _Beans_Options;
use Beans\Framework\Tests\Integration\API\Options\Includes\Options_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansOptions_Register
 *
 * @package Beans\Framework\Tests\Integration\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_BeansOptions_Register extends Options_Test_Case {

	/**
	 * Test _Beans_Options::register() should register the callback to the 'admin_enqueue_scripts' hook.
	 */
	public function test_should_register_callback_to_admin_enqueue_scripts_hook() {
		$instance = new _Beans_Options();

		foreach ( static::$test_data as $options ) {
			// Register the option.
			$instance->register( $options['section'], $options['args'] );

			// Check that the callback is registered to the hook.
			$this->assertEquals( 10, has_action( 'admin_enqueue_scripts', [ $instance, 'enqueue_assets' ] ) );

			// Clean up.
			remove_action( 'admin_enqueue_scripts', [ $instance, 'enqueue_assets' ] );
		}
	}

	/**
	 * Test _Beans_Options::register() should register the metabox with WordPress.
	 */
	public function test_should_register_metabox_with_wp() {
		$instance = new _Beans_Options();
		$this->go_to_settings_page();

		foreach ( static::$test_data as $option ) {
			// Register the option.
			$instance->register( $option['section'], $option['args'] );

			// Check that the metabox is registered with WordPress.
			global $wp_meta_boxes;
			$this->assertArrayHasKey( 'themesphppagebeans_settings', $wp_meta_boxes );
			$this->assertArrayHasKey( $option['args']['context'], $wp_meta_boxes['themesphppagebeans_settings'] );
			$this->assertArrayHasKey( 'default', $wp_meta_boxes['themesphppagebeans_settings'][ $option['args']['context'] ] );
			$this->assertArrayHasKey( $option['section'], $wp_meta_boxes['themesphppagebeans_settings'][ $option['args']['context'] ]['default'] );
			$this->assertSame(
				[
					'id'       => $option['section'],
					'title'    => $option['args']['title'],
					'callback' => [ $instance, 'render_metabox' ],
					'args'     => null,
				],
				$wp_meta_boxes['themesphppagebeans_settings'][ $option['args']['context'] ]['default'][ $option['section'] ]
			);

			// Clean up.
			remove_action( 'admin_enqueue_scripts', [ $instance, 'enqueue_assets' ] );
		}
	}
}
