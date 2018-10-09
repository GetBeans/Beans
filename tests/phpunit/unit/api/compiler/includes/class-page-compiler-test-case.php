<?php
/**
 * Test Case for Beans Page Compiler unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Mockery;

/**
 * Abstract Class Page_Compiler_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 */
abstract class Page_Compiler_Test_Case extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions(
			[
				'api/compiler/class-beans-page-compiler.php',
				'api/options/functions.php',
			]
		);
	}

	/**
	 * Get the deps mock.
	 *
	 * @since 1.5.0
	 *
	 * @param array $config Configuration parameters to set the properties.
	 *
	 * @return Mockery\MockInterface
	 */
	protected function get_deps_mock( array $config ) {
		$mock         = Mockery::mock( '_WP_Dependency' );
		$mock->handle = $config['handle'];
		$mock->src    = $config['src'];
		$mock->deps   = isset( $config['deps'] ) ? $config['deps'] : [];
		$mock->ver    = isset( $config['ver'] ) ? $config['ver'] : false;
		$mock->args   = isset( $config['args'] ) ? $config['args'] : 'all';
		$mock->extra  = isset( $config['extra'] ) ? $config['extra'] : [];

		return $mock;
	}
}
