<?php
/**
 * Tests for beans_get_layout()
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Layout;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansGetLayout
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansGetLayout extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( [
			'api/layout/functions.php',
			'api/post-meta/functions.php',
			'api/term-meta/functions.php',
		] );
	}

	/**
	 * Test beans_get_layout() should return the layout for a single post or page.
	 */
	public function test_should_return_layout_for_singular() {
		Monkey\Functions\expect( 'is_singular' )
			->times( 3 )
			->andReturn( true );

		foreach ( [ 'c', 'c_sp', 'sp_c_ss' ] as $layout ) {

			Monkey\Functions\expect( 'beans_get_post_meta' )
				->once()
				->with( 'beans_layout' )
				->andReturn( $layout );

			Monkey\Filters\expectApplied( 'beans_layout' )
				->once()
				->with( $layout )
				->andReturn( $layout );

			$this->assertSame( $layout, beans_get_layout() );
		}
	}

	/**
	 * Test beans_get_layout() should return the layout for the static posts page.
	 */
	public function test_should_return_layout_for_static_posts_page() {
		Monkey\Functions\expect( 'is_singular' )
			->times( 3 )
			->andReturn( false );

		Monkey\Functions\expect( 'is_home' )
			->times( 3 )
			->andReturn( true );

		Monkey\Functions\expect( 'get_option' )
			->times( 3 )
			->with( 'page_for_posts' )
			->andReturn( 1 );

		foreach ( [ 'c', 'c_sp', 'sp_c_ss' ] as $layout ) {

			Monkey\Functions\expect( 'beans_get_post_meta' )
				->once()
				->with( 'beans_layout', false, 1 )
				->andReturn( $layout );

			Monkey\Filters\expectApplied( 'beans_layout' )
				->once()
				->with( $layout )
				->andReturn( $layout );

			$this->assertSame( $layout, beans_get_layout() );
		}
	}

	/**
	 * Test beans_get_layout() should return layout for category, tag, or taxonomy archive web page.
	 */
	public function test_should_return_layout_for_cat_tag_tax() {
		Monkey\Functions\expect( 'is_singular' )
			->times( 3 )
			->andReturn( false );
		Monkey\Functions\expect( 'is_home' )
			->times( 3 )
			->andReturn( false );

		Monkey\Functions\expect( 'is_category' )
			->times( 3 )
			->andReturn( true );

		foreach ( [ 'c', 'c_sp', 'sp_c_ss' ] as $layout ) {

			Monkey\Functions\expect( 'beans_get_term_meta' )
				->once()
				->with( 'beans_layout' )
				->andReturn( $layout );

			Monkey\Filters\expectApplied( 'beans_layout' )
				->once()
				->with( $layout )
				->andReturn( $layout );

			$this->assertSame( $layout, beans_get_layout() );
		}
	}

	/**
	 * Test beans_get_layout() should return default layout when layout is not set.
	 */
	public function test_should_return_default_layout_when_layout_is_not_set() {
		Monkey\Functions\when( 'is_singular' )->justReturn( false );
		Monkey\Functions\when( 'is_home' )->justReturn( false );
		Monkey\Functions\when( 'is_category' )->justReturn( false );
		Monkey\Functions\when( 'is_tag' )->justReturn( false );
		Monkey\Functions\when( 'is_tax' )->justReturn( false );
		Monkey\Functions\when( 'beans_has_widget_area' )->justReturn( true );

		Monkey\Functions\expect( 'get_theme_mod' )
			->once()
			->with( 'beans_layout', 'c_sp' )
			->andReturn( 'c_sp' );

		Monkey\Filters\expectApplied( 'beans_layout' )
			->once()
			->with( 'c_sp' )
			->andReturn( 'c_sp' );

		$this->assertSame( 'c_sp', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return default layout when layout is `false`.
	 */
	public function test_should_return_default_layout_when_layout_is_false() {
		Monkey\Functions\when( 'is_singular' )->justReturn( true );
		Monkey\Functions\expect( 'beans_get_post_meta' )
			->once()
			->with( 'beans_layout' )
			->andReturn( false );
		Monkey\Functions\when( 'beans_has_widget_area' )->justReturn( true );

		Monkey\Functions\expect( 'get_theme_mod' )
			->once()
			->with( 'beans_layout', 'c_sp' )
			->andReturn( 'c_sp' );

		Monkey\Filters\expectApplied( 'beans_layout' )
			->once()
			->with( 'c_sp' )
			->andReturn( 'c_sp' );

		$this->assertSame( 'c_sp', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return default layout when layout is set to "default_fallback".
	 */
	public function test_should_return_default_layout_when_layout_is_default_fallback() {
		Monkey\Functions\when( 'is_singular' )->justReturn( true );
		Monkey\Functions\expect( 'beans_get_post_meta' )
			->once()
			->with( 'beans_layout' )
			->andReturn( 'default_fallback' );
		Monkey\Functions\when( 'beans_has_widget_area' )->justReturn( true );

		Monkey\Functions\expect( 'get_theme_mod' )
			->once()
			->with( 'beans_layout', 'c_sp' )
			->andReturn( 'c_sp' );

		Monkey\Filters\expectApplied( 'beans_layout' )
			->once()
			->with( 'c_sp' )
			->andReturn( 'c_sp' );

		$this->assertSame( 'c_sp', beans_get_layout() );
	}
}
