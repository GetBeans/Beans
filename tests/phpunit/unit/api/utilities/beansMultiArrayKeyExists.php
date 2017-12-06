<?php
/**
 * Tests for beans_multi_array_key_exists()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansMultiArrayKeyExists
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansMultiArrayKeyExists extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_multi_array_key_exists() should throw an error for non-array data type.
	 */
	public function test_should_throw_error_for_non_array() {
		$args = array(
			0     => 'bar',
			'foo' => 10,
			'bar' => new \stdClass(),
		);

		foreach ( $args as $arg1 => $arg2 ) {
			try {
				beans_multi_array_key_exists( $arg1, $arg2 );
			} catch ( \Throwable $t ) {
				$catch = $t;
			} catch ( \Exception $e ) {
				$catch = $e;
			}

			$this->assertNotEmpty( $catch );
			unset( $catch );
		}
	}

	/**
	 * Test beans_multi_array_key_exists() should return true when key does exist.
	 */
	public function test_should_return_true_when_key_exists() {
		$data = array(
			'oof' => 'found me',
		);
		$this->assertTrue( beans_multi_array_key_exists( 'oof', $data ) );
		$this->assertTrue( beans_multi_array_key_exists( 1, array( 10, 'bar', 'baz' ) ) );
		$data = array(
			'green' => 'grass',
			'blue'  => 'sky',
		);
		$this->assertTrue( beans_multi_array_key_exists( 'blue', $data ) );
	}

	/**
	 * Test beans_multi_array_key_exists() should return false when key does not exist.
	 */
	public function test_should_return_false_when_key_does_not_exist() {
		$data = array(
			'oof' => 'found me',
		);
		$this->assertFalse( beans_multi_array_key_exists( 'foo', $data ) );
		$this->assertFalse( beans_multi_array_key_exists( 'bar', array( 10, 'bar', 'baz' ) ) );
		$data = array(
			'green' => 'grass',
			'blue'  => 'sky',
		);
		$this->assertFalse( beans_multi_array_key_exists( 'red', $data ) );
	}

	/**
	 * Test beans_multi_array_key_exists() should return true when key exists within a multi-dimensional array.
	 */
	public function test_should_return_true_when_key_exists_multidimensional() {
		$data = array(
			'bar',
			array(
				'zab' => 'foo',
			),
		);
		$this->assertTrue( beans_multi_array_key_exists( 'zab', $data ) );

		$data = array(
			'bar',
			'skill' => array(
				'javascript' => true,
				'php'        => true,
				'sql'        => true,
				'beans'      => 'rocks',
			),
		);
		$this->assertTrue( beans_multi_array_key_exists( 'beans', $data ) );
	}

	/**
	 * Test beans_multi_array_key_exists() should return false when key does not exist within a multi-dimensional array.
	 */
	public function test_should_return_false_when_key_does_not_exist_multidimensional() {
		$data = array(
			'bar',
			array(
				'zab' => 'foo',
			),
		);
		$this->assertFalse( beans_multi_array_key_exists( 'foo', $data ) );
		$data = array(
			'bar',
			'skill' => array(
				'javascript' => true,
				'php'        => true,
				'sql'        => true,
				'beans'      => 'rocks',
			),
		);
		$this->assertFalse( beans_multi_array_key_exists( 'rocks', $data ) );
	}
}
