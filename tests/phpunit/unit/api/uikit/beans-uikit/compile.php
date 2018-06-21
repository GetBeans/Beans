<?php
/**
 * Tests for the compile() method of _Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_Compile
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_Compile extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::compile() should not compile when there are no assets to compile.
	 */
	public function test_should_not_compile_when_no_assets_to_compile() {
		$beans_uikit = new _Beans_Uikit();

		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_styles' )
			->once()
			->andReturn( [] );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_scripts' )
			->once()
			->andReturn( [] );
		Monkey\Functions\expect( 'beans_compile_less_fragments' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertNull( $beans_uikit->compile() );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the styles.
	 */
	public function test_should_compile_styles() {
		$beans_uikit = new _Beans_Uikit();

		$styles = [
			BEANS_API_PATH . 'uikit/src/less/core/variables.less',
			BEANS_API_PATH . 'uikit/src/fixes.less',
		];
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_styles' )
			->once()
			->with( $styles )
			->andReturn( $styles );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_scripts' )->andReturn( false );
		Monkey\Functions\expect( 'beans_compile_less_fragments' )
			->once()
			->with( 'uikit', $styles, [] )
			->andReturnNull();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertNull( $beans_uikit->compile() );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the styles with filtered args.
	 */
	public function test_should_compile_styles_with_filtered_args() {
		$beans_uikit = new _Beans_Uikit();

		$styles = [
			BEANS_API_PATH . 'uikit/src/less/core/variables.less',
			BEANS_API_PATH . 'uikit/src/fixes.less',
		];
		$args   = [ 'dependencies' => [ 'foo' ] ];
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_styles' )->andReturn( $styles );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_scripts' )->andReturn( false );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_styles_args' )
			->once()
			->with( [] )
			->andReturn( $args );
		Monkey\Functions\expect( 'beans_compile_less_fragments' )
			->once()
			->with( 'uikit', $styles, $args )
			->andReturnNull();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertNull( $beans_uikit->compile() );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the scripts.
	 */
	public function test_should_compile_scripts() {
		$beans_uikit = new _Beans_Uikit();

		$scripts = [
			BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
		];
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_styles' )->andReturn( false );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_scripts' )
			->once()
			->with( $scripts )
			->andReturn( $scripts );
		Monkey\Functions\expect( 'beans_compile_less_fragments' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )
			->once()
			->with( 'uikit', $scripts, [ 'dependencies' => [ 'jquery' ] ] )
			->andReturnNull();

		$this->assertNull( $beans_uikit->compile() );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the scripts with filtered args.
	 */
	public function test_should_compile_scripts_with_filtered_args() {
		$beans_uikit = new _Beans_Uikit();

		$scripts = [
			BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
		];
		$args    = [
			'dependencies' => [ 'jquery' ],
			'in_footer'    => true,
			'minify_js'    => true,
		];
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_styles' )->andReturn( false );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_scripts' )->andReturn( $scripts );
		Monkey\Filters\expectApplied( 'beans_uikit_euqueued_scripts_args' )
			->once()
			->with( [ 'dependencies' => [ 'jquery' ] ] )
			->andReturn( $args );
		Monkey\Functions\expect( 'beans_compile_less_fragments' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )
			->once()
			->with( 'uikit', $scripts, $args )
			->andReturnNull();

		$this->assertNull( $beans_uikit->compile() );
	}
}
