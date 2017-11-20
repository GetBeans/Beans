<?php

namespace Beans\Framework\Tests\UnitTests\API\Utilities;

use Beans\Framework\Tests\UnitTests\Test_Case;

/**
 * Class Tests_Beans_Multi_Array_Key_Exists
 *
 * @package Beans\Framework\Tests\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Multi_Array_Key_Exists extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	/**
	 * Test beans_multi_array_key_exists() should throw an error for non-array data type.
	 */
	public function test_should_throws_error_for_non_array() {
		$this->expectException( \TypeError::class );
		$this->assertFalse( beans_multi_array_key_exists( 0, 'bar' ) );
		$this->assertFalse( beans_multi_array_key_exists( 'foo', 10 ) );
		$this->assertFalse( beans_multi_array_key_exists( 'bar', new \stdClass() ) );
	}

	/**
	 * Test beans_multi_array_key_exists() should return true when key does exist.
	 */
	public function test_should_return_true_when_key_exists() {
		$this->assertTrue( beans_multi_array_key_exists( 'oof', array( 'oof' => 'found me' ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 1, array( 10, 'bar', 'baz' ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 'blue', array( 'green' => 'grass', 'blue' => 'sky' ) ) );
	}

	/**
	 * Test beans_multi_array_key_exists() should return false when key does not exist.
	 */
	public function test_should_return_false_when_key_does_not_exist() {
		$this->assertFalse( beans_multi_array_key_exists( 'foo', array( 'oof' => 'found me' ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'bar', array( 10, 'bar', 'baz' ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'red', array( 'green' => 'grass', 'blue' => 'sky' ) ) );
	}

	/**
	 * Test beans_multi_array_key_exists() should return true when key exists within a multi-dimensional array.
	 */
	public function test_should_return_true_when_key_exists_multidimensional() {
		$this->assertTrue( beans_multi_array_key_exists( 'zab', array( 'bar', array( 'zab' => 'foo' ) ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 'wordpress', array(
				'bar',
				'skill' => array(
					'javascript' => true,
					'php'        => true,
					'sql'        => true,
					'wordpress'  => 'rocks',
				),
			) )
		);
	}

	/**
	 * Test beans_multi_array_key_exists() should return true when key exists within a multi-dimensional array.
	 */
	public function test_should_return_false_when_key_does_not_exist_multidimensional() {
		$this->assertFalse( beans_multi_array_key_exists( 'foo', array( 'bar', array( 'zab' => 'foo' ) ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'rocks', array(
				'bar',
				'skill' => array(
					'javascript' => true,
					'php'        => true,
					'sql'        => true,
					'wordpress'  => 'rocks',
				),
			) )
		);
	}
}
