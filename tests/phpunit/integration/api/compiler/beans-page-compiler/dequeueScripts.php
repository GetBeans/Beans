<?php
/**
 * Tests for the dequeue_scripts() method of _Beans_Page_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Page_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Page_Compiler_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-page-compiler-test-case.php';

/**
 * Class Tests_BeansPageCompiler_DequeueScripts
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansPageCompiler_DequeueScripts extends Page_Compiler_Test_Case {

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		foreach ( [ 'test-compiler-js', 'test-uikit-js' ] as $handle ) {
			wp_dequeue_script( $handle );
			unset( $GLOBALS['wp_scripts']->registered[ $handle ] );
		}

		$GLOBALS['wp_scripts']->done = [];

		parent::tearDown();
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should not dequeue scripts when there are no scripts.
	 */
	public function test_should_not_dequeue_scripts_when_no_scripts() {
		$this->assertNull( ( new _Beans_Page_Compiler() )->dequeue_scripts() );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should not dequeue when no scripts are registered.
	 */
	public function test_should_not_dequeue_when_no_scripts_registered() {
		$compiler         = new _Beans_Page_Compiler();
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue( $compiler, [ 'test-compiler-js', 'test-uikit-js' ] );

		$this->assertNull( $compiler->dequeue_scripts() );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should dequeue scripts.
	 */
	public function test_should_dequeue_scripts() {
		$compiler         = new _Beans_Page_Compiler();
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue( $compiler, [
			'test-compiler-js' => '/foo/tests/compiler.js',
			'test-uikit-js'    => '/foo/tests/uikit.js',
		] );

		// Set up the scripts.
		wp_enqueue_script( 'test-compiler-js', '/foo/tests/compiler.js' );
		wp_enqueue_script( 'test-uikit-js', '/foo/tests/uikit.js' );

		// Run the tests.
		$this->assertNull( $compiler->dequeue_scripts() );
		$this->assertSame( [ 'test-compiler-js', 'test-uikit-js' ], $GLOBALS['wp_scripts']->done );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should not print the inline localization when no scripts have
	 * localized data.
	 */
	public function test_should_not_print_inline_when_no_scripts_have_localized_data() {
		$compiler         = new _Beans_Page_Compiler();
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue( $compiler, [
			'test-compiler-js' => '/foo/tests/compiler.js',
			'test-uikit-js'    => '/foo/tests/uikit.js',
		] );

		// Set up the scripts.
		wp_enqueue_script( 'test-compiler-js', '/foo/tests/compiler.js' );
		wp_enqueue_script( 'test-uikit-js', '/foo/tests/uikit.js' );

		// Run the test.
		ob_start();
		$compiler->dequeue_scripts();
		$this->assertSame( '', ob_get_clean() );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should print the inline localization content.
	 */
	public function test_should_print_inline_localization_content() {
		$compiler         = new _Beans_Page_Compiler();
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue( $compiler, [
			'test-compiler-js' => '/foo/tests/compiler.js',
			'test-uikit-js'    => '/foo/tests/uikit.js',
		] );

		// Set up the scripts.
		wp_enqueue_script( 'test-compiler-js', '/foo/tests/compiler.js' );
		wp_localize_script( 'test-compiler-js', 'testParams', 'hello-beans' );
		wp_enqueue_script( 'test-uikit-js', '/foo/tests/uikit.js' );

		// Run the tests.
		ob_start();
		$compiler->dequeue_scripts();
		$inline_script = ob_get_clean();

		$expected = <<<EOB
<script type='text/javascript'>
	var testParams = "hello-beans";
</script>

EOB;

		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $inline_script ) );
	}
}
