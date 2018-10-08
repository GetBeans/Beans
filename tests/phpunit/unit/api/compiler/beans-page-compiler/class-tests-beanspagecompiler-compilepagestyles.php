<?php
/**
 * Tests for the compile_page_styles() method of Beans_Page_Compiler.
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
 * Class Tests_BeansPageCompiler_CompilePageStyles
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansPageCompiler_CompilePageStyles extends Page_Compiler_Test_Case {

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when the styles compiler is not supported.
	 */
	public function test_should_not_compile_when_styles_compiler_not_supported() {
		Monkey\Functions\expect( 'beans_get_component_support' )
			->once()
			->with( 'wp_styles_compiler' )
			->andReturn( false );
		Monkey\Functions\expect( 'get_option' )->never();
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when the "compile all styles" option is not
	 * set.
	 */
	public function test_should_not_compile_when_option_is_not_set() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'beans_compile_all_styles', false )
			->andReturn( false );
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when in dev mode.
	 */
	public function test_should_not_compile_when_in_dev_mode() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when there are no styles.
	 */
	public function test_should_not_compile_when_there_are_no_styles() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Check that beans_get() only gets called once.
		Monkey\Functions\expect( 'beans_get' )->once()->andReturn( null );

		// Check that beans_compile_css_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when assets are admin bar only.
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

		// Check that beans_compile_css_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when the style is not registered.
	 */
	public function test_should_not_compile_when_style_is_not_registered() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets                            = $this->get_mock_assets( [ 'admin-bar', 'uikit', 'child-style' ] );
		$assets->registered['uikit']       = null;
		$assets->registered['child-style'] = null;

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_styles', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'child-style', $assets->registered )
			->andReturn( $assets->registered['child-style'] );

		// Check that beans_compile_css_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when the style has no src.
	 */
	public function test_should_not_compile_when_style_has_no_src() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets                                 = $this->get_mock_assets( [ 'admin-bar', 'uikit', 'child-style' ] );
		$assets->registered['uikit']->src       = '';
		$assets->registered['child-style']->src = '';

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_styles', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'child-style', $assets->registered )
			->andReturn( $assets->registered['child-style'] );

		// Check that beans_compile_css_fragments() does not get called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should compile when the style has src but no dependencies.
	 */
	public function test_should_compile_when_style_has_src_but_no_deps() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets = $this->get_mock_assets( [ 'admin-bar', 'uikit', 'child-style' ] );

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_styles', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'child-style', $assets->registered )
			->andReturn( $assets->registered['child-style'] );

		// Mock how beans_compile_css_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )
			->once()
			->with(
				'beans',
				[
					'uikit'       => $assets->registered['uikit']->src,
					'child-style' => $assets->registered['child-style']->src,
				],
				[ 'version' => null ]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );

		// Check the asset's done state.
		$this->assertSame( [ 'uikit', 'child-style' ], $assets->done );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should add the query arg to the compiled style's src.
	 */
	public function test_should_add_query_arg_to_compiled_style_src() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );

		// Initialize the mocked assets.
		$assets                                  = $this->get_mock_assets( [ 'admin-bar', 'child-style' ] );
		$assets->registered['child-style']->args = 'screen';
		$original_src                            = $assets->registered['child-style']->src;
		$new_src                                 = $original_src . '?beans_compiler_media_query=screen';

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_styles', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'child-style', $assets->registered )
			->andReturn( $assets->registered['child-style'] );

		// Check that add_query_arg() is called.
		Monkey\Functions\expect( 'add_query_arg' )
			->once()
			->with( [ 'beans_compiler_media_query' => 'screen' ], $original_src )
			->andReturn( $new_src );

		// Check that the new source is compiled.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )
			->once()
			->with( 'beans', [ 'child-style' => $new_src ], [ 'version' => null ] )
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );

		// Check the asset states when done.
		$this->assertSame( $new_src, $assets->registered['child-style']->src );
		$this->assertSame( [ 'child-style' ], $assets->done );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should compile styles and dependencies.
	 */
	public function test_should_compile_style_and_deps() {
		Monkey\Functions\when( 'beans_get_component_support' )->justReturn( true );
		Monkey\Functions\when( 'get_option' )->justReturn( true );
		Monkey\Functions\when( '_beans_is_compiler_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Initialize the mocked assets.
		$assets = $this->get_mock_assets(
			[
				'admin-bar',
				'debug-bar',
				'uikit',
				'child-style',
				'debug-bar-actions-filters',
			]
		);

		// Check the order of how beans_get() will be called.
		Monkey\Functions\expect( 'beans_get' )
			->ordered()
			->once()
			->with( 'wp_styles', $GLOBALS )
			->andReturn( $assets )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'debug-bar', $assets->registered )
			->andReturn( $assets->registered['debug-bar'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'uikit', $assets->registered )
			->andReturn( $assets->registered['uikit'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'child-style', $assets->registered )
			->andReturn( $assets->registered['child-style'] )
			->andAlsoExpectIt()
			->ordered()
			->once()
			->with( 'debug-bar-actions-filters', $assets->registered )
			->andReturn( $assets->registered['debug-bar-actions-filters'] )
			->andAlsoExpectIt();

		// Mock how beans_compile_css_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )
			->once()
			->with(
				'beans',
				[
					'debug-bar'                 => $assets->registered['debug-bar']->src,
					'uikit'                     => $assets->registered['uikit']->src,
					'child-style'               => $assets->registered['child-style']->src,
					'debug-bar-actions-filters' => $assets->registered['debug-bar-actions-filters']->src,
				],
				[ 'version' => null ]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );

		// Check the asset's done state.
		$this->assertSame( [ 'debug-bar', 'uikit', 'child-style', 'debug-bar-actions-filters' ], $assets->done );
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
		$wp_styles_mock        = Mockery::mock( 'WP_Styles' );
		$wp_styles_mock->queue = $queue;
		$registered            = [];

		if ( in_array( 'debug-bar', $queue, true ) ) {
			$registered['debug-bar'] = $this->get_deps_mock(
				[
					'handle' => 'debug-bar',
					'src'    => 'http://beansdev.test/wp-content/plugins/debug-bar/css/debug-bar.dev.css',
					'ver'    => '20170515',
				]
			);
		}

		if ( in_array( 'uikit', $queue, true ) ) {
			$registered['uikit'] = $this->get_deps_mock(
				[
					'handle' => 'uikit',
					'src'    => 'http://beansdev.test/wp-content/uploads/beans/compiler/uikit/ac2cc0a-f36bb75.css',
				]
			);
		}

		if ( in_array( 'child-style', $queue, true ) ) {
			$registered['child-style'] = $this->get_deps_mock(
				[
					'handle' => 'child-style',
					'src'    => 'http://beansdev.test/wp-content/themes/tm-beans-child/style.css',
				]
			);
		}

		if ( in_array( 'debug-bar-actions-filters', $queue, true ) ) {
			$registered['debug-bar-actions-filters'] = $this->get_deps_mock(
				[
					'handle' => 'debug-bar-actions-filters',
					'src'    => 'http://beansdev.test/wp-content/plugins/debug-bar-actions-and-filters-addon/css/debug-bar-actions-filters.css',
					'deps'   => [ 'debug-bar' ],
					'ver'    => '1.5.1all',
				]
			);
		}

		$wp_styles_mock->registered = $registered;

		return $wp_styles_mock;
	}
}
