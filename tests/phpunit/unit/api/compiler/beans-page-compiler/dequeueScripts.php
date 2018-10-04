<?php
/**
 * Tests for the dequeue_scripts() method of _Beans_Page_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use _Beans_Page_Compiler;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Page_Compiler_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-page-compiler-test-case.php';

/**
 * Class Tests_BeansPageCompiler_DequeueScripts
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansPageCompiler_DequeueScripts extends Page_Compiler_Test_Case {

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should not dequeue scripts when there are no scripts.
	 */
	public function test_should_not_dequeue_scripts_when_no_scripts() {
		Monkey\Functions\expect( 'beans_get' )->never();

		// Run the test.
		$this->assertNull( ( new _Beans_Page_Compiler() )->dequeue_scripts() );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should not dequeue when no scripts are registered.
	 */
	public function test_should_not_dequeue_when_no_scripts_registered() {
		$assets   = $this->get_mock_assets( [ 'uikit' ] );
		$compiler = new _Beans_Page_Compiler();

		// Set the scripts to be dequeued.
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue( $compiler, [ 'uikit' => $assets->registered['uikit']->src ] );

		// Set up the asset mocks.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( null );

		// Run the tests.
		$this->assertNull( $compiler->dequeue_scripts() );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should dequeue scripts.
	 */
	public function test_should_dequeue_scripts() {
		$assets   = $this->get_mock_assets( [ 'debug-bar', 'uikit' ] );
		$compiler = new _Beans_Page_Compiler();

		// Set the scripts to be dequeued.
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue(
			$compiler,
			[
				'debug-bar' => $assets->registered['debug-bar']->src,
				'uikit'     => $assets->registered['uikit']->src,
			]
		);

		// Set up the asset mocks.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'debug-bar', $assets->registered )
			->andReturn( $assets->registered['debug-bar'] )
			->andAlsoExpectIt()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] );

		// Run the tests.
		$this->assertNull( $compiler->dequeue_scripts() );

		global $wp_scripts;
		$this->assertSame( [ 'debug-bar', 'uikit' ], $wp_scripts->done );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should not print the inline localization when no scripts have
	 * localized data.
	 */
	public function test_should_not_print_inline_when_no_scripts_have_localized_data() {
		$assets   = $this->get_mock_assets( [ 'debug-bar', 'uikit' ] );
		$compiler = new _Beans_Page_Compiler();

		// Set the scripts to be dequeued.
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue(
			$compiler,
			[
				'debug-bar' => $assets->registered['debug-bar']->src,
				'uikit'     => $assets->registered['uikit']->src,
			]
		);

		// Set up the asset mocks.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'debug-bar', $assets->registered )
			->andReturn( $assets->registered['debug-bar'] )
			->andAlsoExpectIt()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] );

		// Run the tests.
		ob_start();
		$compiler->dequeue_scripts();
		$this->assertSame( '', ob_get_clean() );
	}

	/**
	 * Test _Beans_Page_Compiler::dequeue_scripts() should print the inline localization content.
	 */
	public function test_should_print_inline_localization_content() {
		$assets   = $this->get_mock_assets( [ 'debug-bar', 'uikit' ], true );
		$compiler = new _Beans_Page_Compiler();

		// Set the scripts to be dequeued.
		$dequeued_scripts = $this->get_reflective_property( 'dequeued_scripts', '_Beans_Page_Compiler' );
		$dequeued_scripts->setValue(
			$compiler,
			[
				'debug-bar' => $assets->registered['debug-bar']->src,
				'uikit'     => $assets->registered['uikit']->src,
			]
		);

		// Set up the asset mocks.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'debug-bar', $assets->registered )
			->andReturn( $assets->registered['debug-bar'] )
			->andAlsoExpectIt()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] );

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

	/**
	 * Get the mocked assets.
	 *
	 * @since 1.5.0
	 *
	 * @param array $queue       Array of assets to build.
	 * @param bool  $with_extras When true, adds the extras for localization. Default is false.
	 *
	 * @return \Mockery\MockInterface
	 */
	protected function get_mock_assets( array $queue, $with_extras = false ) {
		$wp_scripts_mock        = Mockery::mock( 'WP_Scripts' );
		$wp_scripts_mock->queue = $queue;
		$registered             = [
			'jquery'         => $this->get_deps_mock(
				[
					'handle' => 'jquery',
					'src'    => false,
					'deps'   => [ 'jquery-core', 'jquery-migrate' ],
					'ver'    => '1.12.4',
				]
			),
			'jquery-core'    => $this->get_deps_mock(
				[
					'handle' => 'jquery-core',
					'src'    => '/wp-includes/js/jquery/jquery.js',
					'ver'    => '1.12.4',
				]
			),
			'jquery-migrate' => $this->get_deps_mock(
				[
					'handle' => 'jquery-migrate',
					'src'    => '/wp-includes/js/jquery/jquery-migrate.js',
					'ver'    => '1.4.1',
				]
			),
		];

		if ( in_array( 'debug-bar', $queue, true ) ) {
			$registered['debug-bar'] = $this->get_deps_mock(
				[
					'handle' => 'debug-bar',
					'src'    => '/wp-content/plugins/debug-bar/js/debug-bar.dev.js',
					'deps'   => [ 'jquery' ],
					'ver'    => '20170515',
					'extra'  => $with_extras ? [ 'data' => 'var testParams = "hello-beans";' ] : [],
				]
			);
		}

		if ( in_array( 'uikit', $queue, true ) ) {
			$registered['uikit'] = $this->get_deps_mock(
				[
					'handle' => 'uikit',
					'src'    => 'wp-content/uploads/beans/compiler/uikit/3b2a958-921f3e0.js',
					'deps'   => [ 'jquery' ],
				]
			);
		}

		$wp_scripts_mock->registered = $registered;

		global $wp_scripts;
		$wp_scripts = $wp_scripts_mock; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case to load mocks for tests.

		return $wp_scripts_mock;
	}
}
