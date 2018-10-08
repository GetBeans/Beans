<?php
/**
 * Tests for the compile_page_scripts() method of Beans_Page_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans_Page_Compiler;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Page_Compiler_Test_Case;
use Brain\Monkey;
use Mockery;

require_once dirname( __DIR__ ) . '/includes/class-page-compiler-test-case.php';

/**
 * Class Tests_BeansPageCompiler_CompilePageScripts
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansPageCompiler_CompilePageScripts extends Page_Compiler_Test_Case {

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when the scripts compiler is not supported.
	 */
	public function test_should_not_compile_when_scripts_compiler_not_supported() {
		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_scripts_compiler' )
			->andReturn( false );
		Monkey\Functions\expect( 'get_option' )->never();
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when the "compile all scripts" option is not
	 * set.
	 */
	public function test_should_not_compile_when_option_is_not_set() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_scripts', false )
			->andReturn( false );
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when in dev mode.
	 */
	public function test_should_not_compile_when_in_dev_mode() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when there are no scripts.
	 */
	public function test_should_not_compile_when_there_are_no_scripts() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_action' )->never();
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Check that beans_get() only gets called once.
		Monkey\Functions\expect( 'beans_get' )->once()->andReturn( null );

		// Check that beans_compile_js_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when assets are admin bar only.
	 */
	public function test_should_not_compile_when_assets_are_admin_bar_only() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets = $this->get_mock_assets( [ 'admin-bar', 'open-sans', 'dashicons' ] );

		// Check that beans_get() only gets called once.
		Monkey\Functions\expect( 'beans_get' )->once()->andReturn( $assets );

		// Check that beans_compile_js_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when the script is not registered.
	 */
	public function test_should_not_compile_when_style_is_not_registered() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets                      = $this->get_mock_assets( [ 'admin-bar', 'uikit' ] );
		$assets->registered['uikit'] = null;

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_scripts', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] );

		// Check that beans_compile_js_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when the script has no src.
	 */
	public function test_should_not_compile_when_script_has_no_src() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets                            = $this->get_mock_assets( [ 'admin-bar', 'uikit' ] );
		$assets->registered['uikit']->src  = '';
		$assets->registered['uikit']->deps = [];

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_scripts', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] );

		// Check that beans_compile_js_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should compile when the script has src but no dependencies.
	 */
	public function test_should_compile_when_script_has_src_but_no_deps() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\expect( 'get_option' )
			->ordered()
			->once()
			->with( 'beans_compile_all_scripts', false )
			->andReturn( true )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'beans_compile_all_scripts_mode', 'aggressive' )
			->andReturn( 'aggressive' );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets                                = $this->get_mock_assets( [ 'admin-bar', 'uikit', 'debug-bar' ] );
		$assets->registered['uikit']->deps     = [];
		$assets->registered['debug-bar']->deps = [];

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_scripts', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'debug-bar', $assets->registered )
			->andReturn( $assets->registered['debug-bar'] );

		// Mock how beans_compile_js_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )
			->once()
			->with(
				'beans',
				[
					'uikit'     => $assets->registered['uikit']->src,
					'debug-bar' => $assets->registered['debug-bar']->src,
				],
				[
					'in_footer' => true,
					'version'   => null,
				]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should compile scripts and dependencies.
	 */
	public function test_should_compile_scripts_and_deps() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\expect( 'get_option' )
			->ordered()
			->once()
			->with( 'beans_compile_all_scripts', false )
			->andReturn( true )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'beans_compile_all_scripts_mode', 'aggressive' )
			->andReturn( 'aggressive' );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets = $this->get_mock_assets( [ 'admin-bar', 'debug-bar', 'uikit' ] );

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_scripts', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			// Handle debug-bar and its dependencies.
			->ordered()
			->once()
			->with( 'debug-bar', $assets->registered )
			->andReturn( $assets->registered['debug-bar'] )
			->andAlsoExpectIt()
			// Recursive stack for dependencies.
			->ordered()
			->once()
			->with( 'wp_scripts', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'jquery', $assets->registered )
			->andReturn( $assets->registered['jquery'] )
			->andAlsoExpectIt()
			// Recursive stack for jquery's dependencies.
			->ordered()
			->once()
			->with( 'wp_scripts', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'jquery-core', $assets->registered )
			->andReturn( $assets->registered['jquery-core'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'jquery-migrate', $assets->registered )
			->andReturn( $assets->registered['jquery-migrate'] )
			->andAlsoExpectIt()
			// Handle uikit and its dependencies.
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] )
			->andAlsoExpectIt();

		// Mock how beans_compile_js_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )
			->once()
			->with(
				'beans',
				[
					'jquery-core'    => $assets->registered['jquery-core']->src,
					'jquery-migrate' => $assets->registered['jquery-migrate']->src,
					'debug-bar'      => $assets->registered['debug-bar']->src,
					'uikit'          => $assets->registered['uikit']->src,
				],
				[
					'in_footer' => true,
					'version'   => null,
				]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Get the mocked assets.
	 *
	 * @since 1.5.0
	 *
	 * @param array $queue Array of assets to build.
	 *
	 * @return \Mockery\MockInterface
	 */
	protected function get_mock_assets( array $queue ) {
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
					'extra'  => [ 'group' => 1 ],
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

		return $wp_scripts_mock;
	}
}
