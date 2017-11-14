<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Beans\BeansFramework\Tests\BeansTestCase;

class BRenderFunctionArrayTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testRenderFunctionArrayBailsForNonCallable() {
		$this->assertNull( beans_render_function_array( 'this-callback-doesnot-exist' ) );
	}

	public function testRenderFunctionArray() {
		$this->assertSame(
			'Hey, no args passed to me.',
			beans_render_function_array( array( $this, 'renderFunctionArrayCallback' ) )
		);
		$this->assertSame(
			'foo',
			beans_render_function_array( array( $this, 'renderFunctionArrayCallback' ), array( 'foo' ) )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function_array( array( $this, 'renderFunctionArrayCallback' ), array( 'foo', 'bar', 'baz' ) )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function_array( array( $this, 'renderFunctionArrayCallback' ), array(
				array( 'foo', 'bar' ),
				'baz',
			) )
		);

		$this->assertSame(
			'Hey, no args passed to me.',
			beans_render_function_array( __NAMESPACE__ . '\renderFunctionArrayCallback' )
		);
		$this->assertSame(
			'foo',
			beans_render_function_array( __NAMESPACE__ . '\renderFunctionArrayCallback', array( 'foo' ) )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function_array( __NAMESPACE__ . '\renderFunctionArrayCallback', array( 'foo', 'bar', 'baz' ) )
		);
		$this->assertSame(
			'foo, bar, baz',
			beans_render_function_array( __NAMESPACE__ . '\renderFunctionArrayCallback', array(
				array( 'foo', 'bar' ),
				'baz',
			) )
		);
	}

	/**************************************
	 * Helpers
	 *************************************/

	public function renderFunctionArrayCallback() {
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

function renderFunctionArrayCallback() {
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
