<?php
/**
 * Test Case for the unit tests.
 *
 * @package Beans\Framework\Tests\Unit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Unit
 */
abstract class Test_Case extends TestCase {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'wp_normalize_path' )->alias( function ( $path ) {
			$path = str_replace( '\\', '/', $path );
			$path = preg_replace( '|(?<=.)/+|', '/', $path );

			if ( ':' === substr( $path, 1, 1 ) ) {
				$path = ucfirst( $path );
			}

			return $path;
		} );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}
}
