<?php
/**
 * Tests for _beans_uikit_enqueue_assets().
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitEnqueueAssets
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitEnqueueAssets extends UIkit_Test_Case {

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		remove_all_filters( 'beans_uikit_euqueued_styles' );
		remove_all_filters( 'beans_uikit_euqueued_scripts' );

		parent::tearDown();
	}

	/**
	 * Test _beans_uikit_enqueue_assets() should not compile when no hooks are registered to
	 * 'beans_uikit_enqueue_scripts'.
	 */
	public function test_should_not_compile_when_no_hooks_registered() {
		remove_all_actions( 'beans_uikit_enqueue_scripts' );

		// Make sure the compilers do not get called.
		Monkey\Functions\expect( 'beans_compile_less_fragments' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the test.
		$this->assertNull( _beans_uikit_enqueue_assets() );
	}

	/**
	 * Test _beans_uikit_enqueue_assets() should fire 'beans_uikit_enqueue_scripts'.
	 */
	public function test_should_fire_beans_uikit_enqueue_scripts() {
		// Needs a callback registered to run this function.
		add_action( 'beans_uikit_enqueue_scripts', '__return_false' );

		// Disable both scripts and styles to disable the compiler.
		add_filter( 'beans_uikit_euqueued_styles', '__return_empty_array' );
		add_filter( 'beans_uikit_euqueued_scripts', '__return_empty_array' );

		// Run the tests.
		_beans_uikit_enqueue_assets();
		$this->assertSame( 1, did_action( 'beans_uikit_enqueue_scripts' ) );
	}

	/**
	 * Test _beans_uikit_enqueue_assets() should compile the styles.
	 */
	public function test_should_compile_styles() {
		// Needs a callback registered to run this function.
		add_action( 'beans_uikit_enqueue_scripts', '__return_false' );

		// Register some components.
		beans_uikit_enqueue_components(
			[
				'alert',
				'button',
				'overlay',
			],
			'core',
			false
		);

		// Disable the scripts from running.
		add_filter( 'beans_uikit_euqueued_scripts', '__return_empty_array' );

		// Run the tests.
		$compiled_uikit_path = vfsStream::url( 'virtual-wp-content/uploads/beans/compiler/uikit/' );
		$this->assertEmpty( $this->get_compiled_filename( $compiled_uikit_path ) );
		_beans_uikit_enqueue_assets();
		$filename = $this->get_compiled_filename( $compiled_uikit_path );
		$this->assertFileExists( $compiled_uikit_path . $filename );
		$this->assertStringEndsWith( '.css', $filename );
	}

	/**
	 * Test _beans_uikit_enqueue_assets() should compile the scripts.
	 */
	public function test_should_compile_scripts() {
		// Needs a callback registered to run this function.
		add_action( 'beans_uikit_enqueue_scripts', '__return_false' );

		// Register some components.
		beans_uikit_enqueue_components(
			[
				'alert',
				'button',
			],
			'core',
			false
		);

		// Disable the styles from running.
		add_filter( 'beans_uikit_euqueued_styles', '__return_empty_array' );

		// Run the tests.
		$compiled_uikit_path = vfsStream::url( 'virtual-wp-content/uploads/beans/compiler/uikit/' );
		$this->assertEmpty( $this->get_compiled_filename( $compiled_uikit_path ) );
		_beans_uikit_enqueue_assets();
		$filename = $this->get_compiled_filename( $compiled_uikit_path );
		$this->assertFileExists( $compiled_uikit_path . $filename );
		$this->assertStringEndsWith( '.js', $filename );
	}
}
