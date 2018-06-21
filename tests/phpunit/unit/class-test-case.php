<?php
/**
 * Test Case for the unit tests.
 *
 * @package Beans\Framework\Tests\Unit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit;

use function Beans\Framework\Tests\reset_beans;
use Beans\Framework\Tests\Test_Case_Trait;
use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Unit
 */
abstract class Test_Case extends TestCase {

	use Test_Case_Trait;

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = false;

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'wp_normalize_path' )->alias( function( $path ) {

			if ( true === $this->just_return_path ) {
				return $path;
			}

			$path = str_replace( '\\', '/', $path );
			$path = preg_replace( '|(?<=.)/+|', '/', $path );

			if ( ':' === substr( $path, 1, 1 ) ) {
				$path = ucfirst( $path );
			}

			return $path;
		} );

		Functions\when( 'wp_json_encode' )->alias( function( $array ) {
			return json_encode( $array ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Required as part of our mock.
		} );
	}

	/**
	 * Set up the stubs for the common WordPress escaping and internationalization functions.
	 */
	protected function setup_common_wp_stubs() {
		// Common escaping functions.
		Monkey\Functions\stubs( [
			'esc_attr',
			'esc_html',
			'esc_textarea',
			'esc_url',
			'wp_kses_post',
		] );

		// Common internationalization functions.
		Monkey\Functions\stubs( [
			'__',
			'esc_html__',
			'esc_html_x',
			'esc_attr_x',
		] );

		foreach ( [ 'esc_attr_e', 'esc_html_e', '_e' ] as $wp_function ) {
			Monkey\Functions\when( $wp_function )->echoArg();
		}
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		reset_beans();

		Monkey\tearDown();
		parent::tearDown();
	}
}
