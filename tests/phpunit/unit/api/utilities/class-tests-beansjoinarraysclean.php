<?php
/**
 * Tests for beans_join_arrays_clean().
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansJoinArraysClean
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 * @group   api-uikit
 */
class Tests_BeansJoinArraysClean extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_join_arrays_clean() should do nothing when both arrays are empty.
	 */
	public function test_should_do_nothing_when_both_arrays_are_empty() {
		$array1 = [];
		$array2 = [];
		$this->assertSame( [], beans_join_arrays_clean( $array1, $array2 ) );
	}

	/**
	 * Test beans_join_arrays_clean() should return clean array1 when array 2 is empty.
	 */
	public function test_should_clean_array1_when_array2_is_empty() {
		$array1 = [
			'post_type'       => 'foo',
			''                => '',
			'number_of_posts' => 5,
			'',
		];
		$array2 = [];
		$this->assertSame( [ 'foo', 5 ], beans_join_arrays_clean( $array1, $array2 ) );
		$this->assertSame(
			[
				'post_type'       => 'foo',
				'number_of_posts' => 5,
			],
			beans_join_arrays_clean( $array1, $array2, false )
		);

		$array1 = [ '', 'foo', 'bar', '', 'baz' ];
		$array2 = [];
		$this->assertSame( [ 'foo', 'bar', 'baz' ], beans_join_arrays_clean( $array1, $array2 ) );
		$this->assertSame(
			[
				1 => 'foo',
				2 => 'bar',
				4 => 'baz',
			],
			beans_join_arrays_clean( $array1, $array2, false )
		);
	}

	/**
	 * Test beans_join_arrays_clean() should clean array2 when array1 is empty.
	 */
	public function test_should_clean_array2_when_array1_is_empty() {
		// Check with associative array.
		$array1 = [];
		$array2 = [
			'post_type'       => 'foo',
			''                => '',
			'number_of_posts' => 5,
			'bar'             => null,
		];
		$this->assertSame( [ 'foo', 5 ], beans_join_arrays_clean( $array1, $array2 ) );
		$this->assertSame(
			[
				'post_type'       => 'foo',
				'number_of_posts' => 5,
			],
			beans_join_arrays_clean( $array1, $array2, false )
		);

		// Check with indexed array.
		$array1 = [];
		$array2 = [ '', 'foo', 'bar', '', 'baz' ];
		$this->assertSame( [ 'foo', 'bar', 'baz' ], beans_join_arrays_clean( $array1, $array2 ) );
		$this->assertSame(
			[
				1 => 'foo',
				2 => 'bar',
				4 => 'baz',
			],
			beans_join_arrays_clean( $array1, $array2, false )
		);
	}

	/**
	 * Test beans_join_arrays_clean() should join and clean the arrays when both are not empty.
	 */
	public function test_should_join_and_clean_the_arrays_when_both_are_not_empty() {
		$array1 = [
			'foo'             => 0,
			'baz'             => '',
			'post_type'       => 'foo',
			3                 => '',
			'bar'             => null,
			'number_of_posts' => 5,
			5                 => 0,
		];
		$array2 = [
			'foo' => 'bar',
			'baz' => 'Hello',
		];
		$this->assertSame( [ 'bar', 'Hello', 'foo', 5 ], beans_join_arrays_clean( $array1, $array2 ) );
		$this->assertSame(
			[
				'foo'             => 'bar',
				'baz'             => 'Hello',
				'post_type'       => 'foo',
				'number_of_posts' => 5,
			],
			beans_join_arrays_clean( $array1, $array2, false )
		);
	}
}
