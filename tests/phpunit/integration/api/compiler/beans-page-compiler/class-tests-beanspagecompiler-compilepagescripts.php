<?php
/**
 * Tests for the compile_page_scripts() method of Beans_Page_Compiler.
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
 * Class Tests_BeansPageCompiler_CompilePageScripts
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansPageCompiler_CompilePageScripts extends Page_Compiler_Test_Case {

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		wp_dequeue_script( 'admin-bar' );
		foreach ( [ 'test-compiler-js', 'test-uikit-js' ] as $handle ) {
			wp_dequeue_script( $handle );
			unset( $GLOBALS['wp_scripts']->registered[ $handle ] );
		}

		$GLOBALS['wp_scripts']->done = [];

		parent::tearDown();
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when the scripts compiler is not supported.
	 */
	public function test_should_not_compile_when_scripts_compiler_not_supported() {
		beans_remove_api_component_support( 'wp_scripts_compiler' );

		Monkey\Functions\expect( 'get_option' )->with( 'beans_compile_all_scripts', false )->never();
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
		beans_add_api_component_support( 'wp_scripts_compiler' );
		delete_option( 'beans_compile_all_scripts' );

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
		beans_add_api_component_support( 'wp_scripts_compiler' );
		update_option( 'beans_compile_all_scripts', 1 );
		update_option( 'beans_dev_mode', 1 );

		Monkey\Functions\expect( 'beans_get' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when there are no scripts.
	 */
	public function test_should_not_compile_when_there_are_no_scripts() {
		beans_add_api_component_support( 'wp_scripts_compiler' );
		update_option( 'beans_compile_all_scripts', 1 );
		delete_option( 'beans_dev_mode' );

		Monkey\Functions\expect( 'add_query_arg' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Run the tests.
		$this->assertEmpty( $GLOBALS['wp_scripts']->queue );
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when assets are admin bar only.
	 */
	public function test_should_not_compile_when_assets_are_admin_bar_only() {
		beans_add_api_component_support( 'wp_scripts_compiler' );
		update_option( 'beans_compile_all_scripts', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Enqueue the admin bar's script.
		wp_enqueue_script( 'admin-bar' );
		$this->assertArrayHasKey( 'admin-bar', $GLOBALS['wp_scripts']->registered );
		$this->assertContains( 'admin-bar', $GLOBALS['wp_scripts']->queue );

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should not compile when scripts are not registered.
	 */
	public function test_should_not_compile_when_scripts_not_registered() {
		beans_add_api_component_support( 'wp_scripts_compiler' );
		update_option( 'beans_compile_all_scripts', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		// Enqueue the admin bar's script.
		wp_enqueue_script( 'admin-bar' );

		// Run the tests.
		$this->assertNull( ( new Beans_Page_Compiler() )->compile_page_scripts() );
	}

	/**
	 * Test Beans_Page_Compiler::compile_page_scripts() should compile when the script has src but no dependencies.
	 */
	public function test_should_compile_when_script_has_src_but_no_deps() {
		beans_add_api_component_support( 'wp_scripts_compiler' );
		update_option( 'beans_compile_all_scripts', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Enqueue the scripts.
		wp_enqueue_script( 'admin-bar' );
		wp_enqueue_script( 'test-compiler-js', '/foo/tests/compiler.js' );
		wp_enqueue_script( 'test-uikit-js', '/foo/tests/uikit.js' );

		// Mock how beans_compile_js_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )
			->once()
			->with(
				'beans',
				[
					'test-compiler-js' => '/foo/tests/compiler.js',
					'test-uikit-js'    => '/foo/tests/uikit.js',
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
		beans_add_api_component_support( 'wp_scripts_compiler' );
		update_option( 'beans_compile_all_scripts', 1 );
		delete_option( 'beans_dev_mode' );
		Monkey\Functions\expect( 'add_query_arg' )->never();

		// Enqueue the scripts.
		wp_enqueue_script( 'admin-bar' );
		wp_enqueue_script( 'test-compiler-js', '/foo/tests/compiler.js' );
		wp_enqueue_script( 'test-uikit-js', '/foo/tests/uikit.js', [ 'jquery' ] );

		// Mock how beans_compile_js_fragments() will be called.
		Monkey\Functions\expect( 'beans_compile_js_fragments' )
			->once()
			->with(
				'beans',
				[
					'jquery-core'      => $GLOBALS['wp_scripts']->registered['jquery-core']->src,
					'jquery-migrate'   => $GLOBALS['wp_scripts']->registered['jquery-migrate']->src,
					'test-compiler-js' => '/foo/tests/compiler.js',
					'test-uikit-js'    => '/foo/tests/uikit.js',
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
}
