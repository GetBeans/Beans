<?php

namespace Beans\Framework\Tests\UnitTests\API\Utilities;

use Beans\Framework\Tests\UnitTests\Test_Case;

/**
 * Class Tests_Beans_Render_Function_Array
 *
 * @package Beans\Framework\Tests\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_Beans_Render_Function_Array extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	/**
	 * Test beans_render_function_array() should bail out when receiving a non-callable.
	 */
	public function test_should_bail_when_noncallable() {
		$this->assertEmpty( beans_render_function_array( 'this-callback-does-not-exist' ) );
	}

	/**
	 * Test beans_render_function_array() should work when there no arguments.
	 */
	public function test_should_work_when_no_arguments() {
		$this->assertEquals( 'You called me!', beans_render_function_array( function () {
			echo 'You called me!';
		} ) );
	}

	/**
	 * Test beans_render_function_array() should work with arguments.
	 */
	public function test_should_work_with_arguments() {

		$callback = function ( $foo, $bar, $baz ) {
			echo "{$foo} {$bar} {$baz}";
		};
		$this->assertSame(
			'foo bar baz',
			beans_render_function_array( $callback, array( 'foo', 'bar', 'baz' ) )
		);

		$callback = function ( $array, $baz ) {
			$this->assertCount( 2, $array );
			echo join( ' ', $array ) . ' ' . $baz;
		};
		$this->assertSame(
			'foo bar baz',
			beans_render_function_array( $callback, array( array( 'foo', 'bar' ), 'baz' ) )
		);

		$callback = function ( $array1, $array2 ) {
			$this->assertCount( 2, $array1 );
			$this->assertArrayHasKey( 'bar', $array1 );
			$this->assertCount( 1, $array2 );
			$this->assertArrayHasKey( 'baz', $array2 );
			echo $array1['foo'];
		};
		$this->assertSame(
			'oof',
			beans_render_function_array(
				$callback,
				array(
					array(
						'foo' => 'oof',
						'bar' => 'rab',
					),
					array( 'baz' => 'zab' ),
				)
			)
		);
	}
}
