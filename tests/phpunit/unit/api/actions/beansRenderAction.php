<?php
/**
 * Tests for _beans_render_action()
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey\Actions;

/**
 * Class Tests_BeansRenderAction
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansRenderAction extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/actions/functions.php';
		require_once __DIR__ . '/stubs/class-action-stub.php';
		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test _beans_render_action() should return false when a callback is not registered to the hook/sub-hook.
	 */
	public function test_should_return_false_when_a_callback_is_not_registered() {
		$this->assertFalse( _beans_render_action( 'foo' ) );
		$this->assertFalse( _beans_render_action( 'foo', 'bar' ) );
		$this->assertFalse( _beans_render_action( 'foo', 'bar', array( 'baz' => 'zab' ) ) );
		$this->assertFalse( _beans_render_action( 'foo_bar' ) );
		$this->assertFalse( _beans_render_action( 'foo.bar' ) );
		$this->assertFalse( _beans_render_action( 'beans_footer_before_markup' ) );
		$this->assertFalse( _beans_render_action( 'beans_footer_after_markup' ) );
	}

	/**
	 * Test _beans_render_action() should return after calling the hook with no sub-hook.
	 */
	public function test_should_return_after_calling_hook_no_subhook() {
		// Testing with a closure.
		$expected_args = array(
			array( 'foo' ),
			array( 'foo', 'bar' ),
			array( 'foo', 'bar', 'baz' ),
		);
		$callback      = function() use ( $expected_args ) {
			$args = func_get_args();
			$this->assertTrue( doing_action( 'beans_stub' ) );
			$this->assertEquals( $expected_args[ $args[0] ], $args[1] );

			// Let's echo out to ensure this callback will was called.
			echo $args[1][0];  // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - reason: we are not testing escaping functionality.
		};
		add_action( 'beans_stub', $callback );
		$this->assertTrue( has_action( 'beans_stub' ) );
		Actions\expectDone( 'beans_stub' )->whenHappen( $callback );
		foreach ( $expected_args as $index => $args ) {
			$this->assertEquals( $args[0], _beans_render_action( 'beans_stub', $index, $args ) );
		}

		// Testing with a stubbed method.
		$stub    = new Actions_Stub();
		$message = 'Beans rocks!';
		add_action( 'beans_stub_with_object', array( $stub, 'echo_static' ) );
		$this->assertTrue( has_action( 'beans_stub_with_object' ) );
		Actions\expectDone( 'beans_stub_with_object' )->whenHappen( array( $stub, 'echo_static' ) );
		$this->assertEquals( $message, _beans_render_action( 'beans_stub_with_object', $message ) );

		// Testing with a stubbed static method.
		$stub = Actions_Stub::class;
		add_action( 'beans_stub_with_static_method', array( $stub, 'echo_static' ) );
		$this->assertTrue( has_action( 'beans_stub_with_static_method' ) );
		Actions\expectDone( 'beans_stub_with_static_method' )->times( 3 )->whenHappen( array( $stub, 'echo_static' ) );

		$message = 'Calling the static method...and Beans rocks!';
		$this->assertEquals( $message, _beans_render_action( 'beans_stub_with_static_method', $message ) );
		$message = 'Yippee, it worked again!';
		$this->assertEquals( $message, _beans_render_action( 'beans_stub_with_static_method', $message ) );
		$this->assertEquals( 5, _beans_render_action( 'beans_stub_with_static_method', 5 ) );
	}

	/**
	 * Test _beans_render_action() should return null for a sub-hook(s) when no callback is registered.
	 */
	public function test_should_return_null_for_subhook_when_no_callback_registered() {
		$this->assertNull( _beans_render_action( 'beans_stub[beans_subhook_stub]' ) );
		$this->assertNull( _beans_render_action( 'beans_stub[subhook][subhook]' ) );
		$this->assertNull( _beans_render_action( 'beans_stub[beans_subhook_stub]_after_test' ) );
	}

	/**
	 * Test _beans_render_action() should render the base hook and not the sub-hooks.
	 * Why? No callbacks are registered to the sub-hooks.
	 */
	public function test_should_render_base_hook() {
		$stub = Actions_Stub::class;

		// Test with a single sub-hook.
		add_action( 'foo', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo' )->whenHappen( array( $stub, 'echo_static' ) );
		Actions\expectDone( 'bar' )->never();
		$this->assertEquals( 'Called foo.', _beans_render_action( 'foo[bar]', 'Called foo.' ) );

		// Test with a suffix.
		add_action( 'foo_bar', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo_bar' )->whenHappen( array( $stub, 'echo_static' ) );
		Actions\expectDone( 'baz_bar' )->never();
		$this->assertEquals( 'Called foo_bar.', _beans_render_action( 'foo[baz]_bar', 'Called foo_bar.' ) );

		// Test with multiple sub-hooks.
		add_action( 'beans_stub', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'beans_stub' )->whenHappen( array( $stub, 'echo_static' ) );
		Actions\expectDone( 'beans_stub[_pre]' )->never();
		Actions\expectDone( 'beans_stub[_before]' )->never();
		Actions\expectDone( 'beans_stub[_pre][_before]' )->never();
		$this->assertEquals( 'Beans rocks!', _beans_render_action( 'beans_stub[_pre][_before]', 'Beans rocks!' ) );

		// Test with multiple sub-hooks.
		add_action( 'beans_stub_after', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'beans_stub_after' )->whenHappen( array( $stub, 'echo_static' ) );
		Actions\expectDone( 'beans_stub[_pre]_after' )->never();
		Actions\expectDone( 'beans_stub[_before]_after' )->never();
		Actions\expectDone( 'beans_stub[_pre][_before]_after' )->never();
		$this->assertEquals( 'Beans rocks!', _beans_render_action( 'beans_stub[_subhook][_subsubhook]_after', 'Beans rocks!' ) );
	}

	/**
	 * Test _beans_render_action() should render one level of sub-hooks.
	 */
	public function test_should_render_one_level_of_sub_hooks() {
		$stub    = Actions_Stub::class;
		$hook    = 'foo[bar]';
		$message = 'Called me. ';

		// The root hook renders.
		add_action( 'foo', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected = $message;

		// The 1st sub-hook renders.
		add_action( 'foo[bar]', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo[bar]' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected .= $message;

		// Run the test.
		$this->assertEquals( $expected, _beans_render_action( $hook, $message ) );
	}

	/**
	 * Test _beans_render_action() should render two levels of sub-hooks, but not the original.
	 * Why? A callback is not registered to the original hook.
	 */
	public function test_should_render_two_levels_of_sub_hooks_but_not_original() {
		$stub    = Actions_Stub::class;
		$hook    = 'foo[bar][baz]';
		$message = 'Called me. ';

		// The root hook renders.
		add_action( 'foo', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected = $message;

		// The 1st sub-hook renders.
		add_action( 'foo[bar]', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo[bar]' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected .= $message;

		// These hooks will not render as they are not registered.
		Actions\expectDone( 'foo[baz]' )->never();
		Actions\expectDone( 'foo[bar][baz]' )->never();

		// Run the test.
		$this->assertEquals( $expected, _beans_render_action( $hook, $message ) );
	}

	/**
	 * Test _beans_render_action() should render the base hook and all sub-hooks.
	 */
	public function test_should_render_hook_and_all_subhooks() {
		$stub    = Actions_Stub::class;
		$hook    = 'foo[bar][baz]';
		$message = 'Called me. ';

		// The root hook renders.
		add_action( 'foo', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected = $message;

		// The 1st sub-hook renders.
		add_action( 'foo[bar]', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo[bar]' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected .= $message;

		// The 2nd sub-hook renders.
		add_action( 'foo[baz]', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo[baz]' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected .= $message;

		// The original hook renders.
		add_action( 'foo[bar][baz]', array( $stub, 'echo_static' ) );
		Actions\expectDone( 'foo[bar][baz]' )->once()->whenHappen( array( $stub, 'echo_static' ) );
		$expected .= $message;

		// Run the test.
		$this->assertEquals( $expected, _beans_render_action( $hook, $message ) );
	}
}
