<?php

namespace Beans\Framework\Tests\UnitTests\API\Utilities;

use Beans\Framework\Tests\UnitTests\Test_Case;

/**
 * Class Tests_Beans_Count_Recursive
 *
 * @package Beans\Framework\Tests\API\Utilities
 *
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Count_Recursive extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	/**
	 * Test beans_count_recursive() when non-array data type.
	 */
	public function test_should_return_zero_for_non_array() {
		$this->assertEquals( 0, beans_count_recursive( 'not-an-array' ) );
		$this->assertEquals( 0, beans_count_recursive( new \stdClass() ) );
		$this->assertEquals( 0, beans_count_recursive( 10 ) );
	}

	/**
	 * Test beans_count_recursive() when specifying a depth of 1.
	 */
	public function test_should_count_depth_of_one() {
		$this->assertEquals( 2, beans_count_recursive( array( 'green' => 'grass', 'blue' => 'sky' ), 1 ) );
		$this->assertEquals( 3, beans_count_recursive( array( 10, 'bar', 'baz' ), 1 ) );
		$this->assertEquals( 1, beans_count_recursive( array( 'hi there' ), 1 ) );
	}

	/**
	 * Test beans_count_recursive() when no depth or non-numeric depth is specified.
	 */
	public function test_should_count_first_depth_when_nonnumeric_depth() {
		$this->assertEquals( 1, beans_count_recursive( array( 'oof' => 'found me' ), 'hi' ) );
		$this->assertEquals( 1, beans_count_recursive( array( 'oof' => 'found me' ), 'hi' ), '10' );
		$this->assertEquals( 3, beans_count_recursive( array( 10, 'bar', 'baz' ), null ) );
		$this->assertEquals( 2, beans_count_recursive( array( 'green' => 'grass', 'blue' => 'sky' ) ) );
		$this->assertEquals( 10, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ) ) );
	}

	/**
	 * Test beans_count_recursive() should counts all different depth levels.
	 */
	public function test_should_count_all_depths() {
		$this->assertEquals( 6, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 1 ) );
		$this->assertEquals( 9, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 2, false ) );
		$this->assertEquals( 10, beans_count_recursive( array(
			array( 1, 2, ),
			3,
			array(
				4,
				5,
				array( 6, 7, 8, 9, array( 10, 11 ) ),
			),
		), 3, false ) );
		$this->assertEquals( 11, beans_count_recursive( array(
			array( 1, 2, ),
			3,
			array(
				4,
				5,
				array( 6, 7, 8, 9, array( 10, 11 ) ),
			),
		), 4, false ) );

		// This test fails.  See Issue #68
//		$this->assertEquals( 9, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 2 ) );
	}
}
