<?php
/**
 * Tests for beans_render_function()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey\Functions;

/**
 * Class Tests_BeansRenderFunction
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansRenderFunction extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_render_function() should bail out when receiving a non-callable.
	 */
	public function test_should_bail_when_noncallable() {
		$this->assertNull( beans_render_function( 'this-callback-does-not-exist' ) );
	}

	/**
	 * Test beans_render_function() should work when there are no arguments.
	 */
	public function test_should_work_when_no_arguments() {
		$this->assertEquals( 'You called me!', beans_render_function( function () {
			echo 'You called me!';
		} ) );
	}

	/**
	 * Test beans_render_function() should work with arguments.
	 */
	public function test_should_work_with_arguments() {

		Functions\when( 'callback_for_render_function' )
			->justEcho( 'foo' );
		$this->assertSame( 'foo', beans_render_function( 'callback_for_render_function', 'foo' ) );

		$callback = function ( $foo, $bar, $baz ) {
			echo "{$foo} {$bar} {$baz}"; // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - reason: we are not testing escaping functionality.
		};
		$this->assertSame( 'foo bar baz', beans_render_function( $callback, 'foo', 'bar', 'baz' ) );

		$callback = function ( $array, $baz ) {
			echo join( ' ', $array ) . ' ' . $baz; // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - reason: we are not testing escaping functionality.
		};
		$this->assertSame(
			'foo bar baz',
			beans_render_function( $callback, array( 'foo', 'bar' ), 'baz' )
		);

		$callback = function ( $object ) {
			$this->assertObjectHasAttribute( 'foo', $object );
			echo $object->foo; // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - reason: we are not testing escaping functionality.
		};
		$this->assertSame(
			'beans',
			beans_render_function( $callback, (object) array( 'foo' => 'beans' ) )
		);
	}
}
