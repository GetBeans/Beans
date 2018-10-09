<?php
/**
 * Tests for beans_join_arrays()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansJoinArrays
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 * @group   api-uikit
 */
class Tests_BeansJoinArrays extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_join_arrays() should do nothing when both arrays are empty.
	 */
	public function test_should_do_nothing_when_both_arrays_are_empty() {
		$array1 = [];
		$array2 = [];
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( [], $array1 );
	}

	/**
	 * Test beans_join_arrays() should not change array 1 when array 2 is empty.
	 */
	public function test_should_not_change_array1_when_array2_is_empty() {
		// Check with associative array.
		$array1   = [
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		];
		$array2   = [];
		$expected = $array1;
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );

		// Check with indexed array.
		$array1   = [ 'foo', 'bar' ];
		$array2   = [];
		$expected = $array1;
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );
	}

	/**
	 * Test beans_join_arrays() should set array1 to array2 when array1 is empty.
	 */
	public function test_should_set_array1_to_array2_when_array1_is_empty() {
		// Check with associative array.
		$array1   = [];
		$array2   = [
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		];
		$expected = $array2;
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );

		// Check with indexed array.
		$array1   = [];
		$array2   = [ 'foo', 'bar' ];
		$expected = $array2;
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );
	}

	/**
	 * Test beans_join_arrays() should merge the arrays when both are not empty.
	 */
	public function test_should_merge_the_arrays_when_both_are_not_empty() {
		$array1   = [
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		];
		$array2   = [
			'foo' => 'bar',
			'baz' => 'Hello',
		];
		$expected = array_merge( $array1, $array2 );
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );

		// Check with indexed array.
		$array1   = [ 'foo', 'bar' ];
		$array2   = [ 'baz', 98 ];
		$expected = array_merge( $array1, $array2 );
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );

		// Check with mixed arrays.
		$array1   = [
			'post_type'       => 'foo',
			'number_of_posts' => 5,
		];
		$array2   = [
			5,
			'bar' => 'foo',
		];
		$expected = array_merge( $array1, $array2 );
		$this->assertNull( beans_join_arrays( $array1, $array2 ) );
		$this->assertSame( $expected, $array1 );
	}
}
