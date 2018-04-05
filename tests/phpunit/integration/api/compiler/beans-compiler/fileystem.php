<?php
/**
 * Tests the filesystem method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey\Functions;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_Filesystem
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_Filesystem extends Compiler_Test_Case {

	/**
	 * Test filesystem() should render a report and die when no filesystem is selected.
	 */
	public function test_should_render_report_and_die_when_no_filesystem_selected() {
		$compiler = new _Beans_Compiler( array() );

		// Let's just make sure we start without the WP Filesystem being initialized.
		unset( $GLOBALS['wp_filesystem'] );
		remove_filter( 'filesystem_method', array( $compiler, 'modify_filesystem_method' ) );
		add_filter( 'filesystem_method', __NAMESPACE__ . '\set_filesystem_method_to_base' );

		// Set up the mocks.
		Functions\expect( __NAMESPACE__ . '\set_filesystem_method_to_base' )
			->once()
			->andReturn( 'base' );
		Functions\expect( __NAMESPACE__ . '\set_wp_die_handler' )
			->once()
			->andReturn( __NAMESPACE__ . '\mock_wp_die_handler' );
		Functions\when( __NAMESPACE__ . '\mock_wp_die_handler' )
			->alias( function( $message ) {
				$this->assertContains( 'Beans cannot work its magic', $message );
			} );

		add_filter( 'wp_die_handler', __NAMESPACE__ . '\set_wp_die_handler' );

		// Initialize the WP Filesystem.
		$this->assertNull( $compiler->filesystem() );

		// Clean up.
		unset( $GLOBALS['wp_filesystem'] );
		remove_filter( 'wp_die_handler', __NAMESPACE__ . '\set_wp_die_handler' );
		remove_filter( 'filesystem_method', __NAMESPACE__ . '\set_filesystem_method_to_base' );
	}

	/**
	 * Test filesystem() should initialize the WP Filesystem.
	 */
	public function test_should_init_wp_filesystem() {
		$compiler = new _Beans_Compiler( array() );

		add_filter( 'filesystem_method', array( $compiler, 'modify_filesystem_method' ) );

		// Initialize the WP Filesystem.
		$this->assertTrue( $compiler->filesystem() );

		// Check that it was initialized.
		$this->assertTrue( function_exists( 'WP_Filesystem' ) );
		$this->assertTrue( class_exists( 'WP_Filesystem_Direct' ) );
		$this->assertArrayHasKey( 'wp_filesystem', $GLOBALS );
		$this->assertInstanceOf( 'WP_Filesystem_Direct', $GLOBALS['wp_filesystem'] );

		remove_filter( 'filesystem_method', array( $compiler, 'modify_filesystem_method' ) );
	}

	/**
	 * Test filesystem() should set WP_Filesystem_Direct when not set as WP_Filesystem method.
	 */
	public function test_should_set_wp_filesystem_direct() {
		// First, set something else as the WP_Filesystem method.
		Functions\expect( __NAMESPACE__ . '\set_filesystem_method_to_base' )
			->once()
			->andReturn( 'base' );

		add_filter( 'filesystem_method', __NAMESPACE__ . '\set_filesystem_method_to_base' );
		WP_Filesystem();
		$this->assertInstanceOf( 'WP_Filesystem_Base', $GLOBALS['wp_filesystem'] );
		remove_filter( 'filesystem_method', __NAMESPACE__ . '\set_filesystem_method_to_base' );

		// Next, let's run our Compiler's filesystem and check that it did initialize WP_Filesystem_Direct.
		$compiler = new _Beans_Compiler( array() );

		add_filter( 'filesystem_method', array( $compiler, 'modify_filesystem_method' ) );
		$this->assertTrue( $compiler->filesystem() );
		$this->assertInstanceOf( 'WP_Filesystem_Direct', $GLOBALS['wp_filesystem'] );
		remove_filter( 'filesystem_method', array( $compiler, 'modify_filesystem_method' ) );
	}
}
