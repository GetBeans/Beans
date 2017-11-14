<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Beans\BeansFramework\Tests\BeansTestCase;

class ARenderFunctionTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testRenderFunctionBailsForNonCallable() {
		$this->assertNull( beans_render_function( 'this-callback-doesnot-exist' ) );
	}

	public function testRenderFunction() {
		$this->assertSame(
			'Hey, no args passed to me.',
			beans_render_function( array( $this, 'renderFunctionCallback' ) )
		);
		$this->assertSame(
			'foo',
			beans_render_function( array( $this, 'renderFunctionCallback' ), 'foo' )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function( array( $this, 'renderFunctionCallback' ), 'foo', 'bar', 'baz' )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function( array( $this, 'renderFunctionCallback' ), array( 'foo', 'bar' ), 'baz' )
		);

		$this->assertSame(
			'Hey, no args passed to me.',
			beans_render_function( __NAMESPACE__ . '\renderFunctionCallback' )
		);
		$this->assertSame(
			'foo',
			beans_render_function( __NAMESPACE__ . '\renderFunctionCallback', 'foo' )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function( __NAMESPACE__ . '\renderFunctionCallback', 'foo', 'bar', 'baz' )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function( __NAMESPACE__ . '\renderFunctionCallback', array( 'foo', 'bar' ), 'baz' )
		);
	}

	/**************************************
	 * Helpers
	 *************************************/

	public function renderFunctionCallback() {
		$args = func_get_args();
		if ( ! $args ) {
			echo 'Hey, no args passed to me.';

			return;
		}

		if ( count( $args ) === 1 && is_string( $args[0] ) ) {
			echo $args[0];

			return;
		}

		echo join( ', ', array_flatten_into_dots( $args ) );
	}
}

function renderFunctionCallback() {
	$args = func_get_args();
	if ( ! $args ) {
		echo 'Hey, no args passed to me.';

		return;
	}

	if ( count( $args ) === 1 && is_string( $args[0] ) ) {
		echo $args[0];

		return;
	}

	echo join( ', ', array_flatten_into_dots( $args ) );
}