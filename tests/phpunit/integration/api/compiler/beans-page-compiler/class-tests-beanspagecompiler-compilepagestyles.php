<?php
/**
 * Tests for the compile_page_styles() method of Beans_Page_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans_Page_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Page_Compiler_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-page-compiler-test-case.php';

/**
 * Class Tests_BeansPageCompiler_CompilePageStyles
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansPageCompiler_CompilePageStyles extends Page_Compiler_Test_Case {

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		wp_dequeue_style( 'admin-bar' );
		wp_dequeue_style( 'open-sans' );
		wp_dequeue_style( 'dashicons' );
		foreach ( [ 'test-compiler-css', 'test-uikit-css' ] as $handle ) {
			wp_dequeue_style( $handle );
			unset( $GLOBALS['wp_styles']->registered[ $handle ] );
		}

		$GLOBALS['wp_styles']->done = [];

		parent::tearDown();
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when the styles compiler is not supported.
	 */
	public function test_should_not_compile_when_styles_compiler_not_supported() {
		beans_remove_api_component_support( 'wp_styles_compiler' );

		Monkey\Functions\expect( 'get_option' )->with( 'beans_compile_all_styles', false )->never();
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
		beans_add_api_component_support( 'wp_styles_compiler' );
		delete_option( 'beans_compile_all_styles' );
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
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );
		update_option( 'beans_dev_mode', 1 );
		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when there are no styles.
	 */
	public function test_should_not_compile_when_there_are_no_styles() {
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Run the tests.
		$this->assertEmpty( $GLOBALS['wp_styles']->queue );
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when assets are admin bar only.
	 */
	public function test_should_not_compile_when_assets_are_admin_bar_only() {
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Enqueue the styles and then check that they are registered.
		wp_enqueue_style( 'admin-bar' );
		wp_enqueue_style( 'open-sans' );
		wp_enqueue_style( 'dashicons' );
		$this->assertArrayHasKey( 'admin-bar', $GLOBALS['wp_styles']->registered );
		$this->assertContains( 'admin-bar', $GLOBALS['wp_styles']->queue );
		$this->assertArrayHasKey( 'open-sans', $GLOBALS['wp_styles']->registered );
		$this->assertContains( 'open-sans', $GLOBALS['wp_styles']->queue );
		$this->assertArrayHasKey( 'dashicons', $GLOBALS['wp_styles']->registered );
		$this->assertContains( 'dashicons', $GLOBALS['wp_styles']->queue );

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should not compile when the style is not registered.
	 */
	public function test_should_not_compile_when_style_is_not_registered() {
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();
		Monkey\Functions\expect( 'beans_compile_css_fragments' )->never();

		// Enqueue the style.
		wp_enqueue_style( 'admin-bar' );

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should compile when the style has src but no dependencies.
	 */
	public function test_should_compile_when_style_has_src_but_no_deps() {
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Enqueue the styles.
		wp_enqueue_style( 'admin-bar' );
		wp_enqueue_style( 'test-compiler-css', '/foo/tests/compiler.css' );
		wp_enqueue_style( 'test-uikit-css', '/foo/tests/uikit.css' );

		// Mock how beans_compile_css_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )
			->once()
			->with(
				'beans',
				[
					'test-compiler-css' => '/foo/tests/compiler.css',
					'test-uikit-css'    => '/foo/tests/uikit.css',
				],
				[ 'version' => null ]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );

		// Check the asset's done state.
		$this->assertSame( [ 'test-compiler-css', 'test-uikit-css' ], $GLOBALS['wp_styles']->done );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should add the query arg to the compiled style's src.
	 */
	public function test_should_add_query_arg_to_compiled_style_src() {
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );

		// Enqueue the styles.
		wp_enqueue_style( 'admin-bar' );
		wp_enqueue_style( 'test-compiler-css', '/foo/tests/compiler.css', [], null, 'screen' );
		wp_enqueue_style( 'test-uikit-css', '/foo/tests/uikit.css', [], null, '(max-width: 640px)' );

		// Mock how beans_compile_css_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )
			->once()
			->with(
				'beans',
				[
					'test-compiler-css' => '/foo/tests/compiler.css?beans_compiler_media_query=screen',
					'test-uikit-css'    => '/foo/tests/uikit.css?beans_compiler_media_query=(max-width: 640px)',
				],
				[ 'version' => null ]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );

		// Check the asset's done state.
		$this->assertSame( [ 'test-compiler-css', 'test-uikit-css' ], $GLOBALS['wp_styles']->done );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_styles() should compile styles and dependencies.
	 */
	public function test_should_compile_styles_and_deps() {
		beans_add_api_component_support( 'wp_styles_compiler' );
		update_option( 'beans_compile_all_styles', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Enqueue the styles.
		wp_enqueue_style( 'admin-bar' );
		wp_enqueue_style( 'test-compiler-css', '/foo/tests/compiler.css' );
		wp_enqueue_style( 'test-uikit-css', '/foo/tests/uikit.css', [ 'test-compiler-css' ] );

		// Mock how beans_compile_css_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_css_fragments' )
			->once()
			->with(
				'beans',
				[
					'test-compiler-css' => '/foo/tests/compiler.css',
					'test-uikit-css'    => '/foo/tests/uikit.css',
				],
				[ 'version' => null ]
			)
			->andReturnNull();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_styles() );

		// Check the asset's done state.
		$this->assertSame( [ 'test-compiler-css', 'test-uikit-css' ], $GLOBALS['wp_styles']->done );
	}
}
