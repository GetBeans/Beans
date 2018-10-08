<?php
/**
 * Tests for the filesystem() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_Filesystem
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_Filesystem extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::filesystem() should return true when the WP Filesystem is initialized to
	 * WP_Filesystem_Direct.
	 */
	public function test_should_return_true_when_wp_filesystem_is_init() {
		// Initialize the wp_filesystem global variable.
		Mockery::mock( 'WP_Filesystem_Direct' );
		$GLOBALS['wp_filesystem'] = new \WP_Filesystem_Direct(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case as we are mocking the filesystem.

		Monkey\Functions\when( 'WP_Filesystem' )->justReturn( true );

		// Test that the WP Filesystem is not loaded yet.
		$compiler = $this->create_compiler();
		$this->assertTrue( $compiler->filesystem() );

		unset( $GLOBALS['wp_filesystem'] );
	}
}
