<?php
/**
 * Tests for beans_get_post_meta()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansGetPostMeta extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/post-meta/functions.php';
	}

	/**
	 * Test beans_get_post_meta() should return the default when the post meta does not exist.
	 */
	public function test_should_return_default_when_post_meta_does_not_exist() {
		Monkey\Functions\expect( 'get_post_meta' )->with( 1 )->times( 3 )->andReturn( array() );

		$this->assertFalse( beans_get_post_meta( 'beans_layout', false, 1 ) );
		$this->assertSame( '', beans_get_post_meta( 'beans_layout', '', 1 ) );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c', 1 ) );
	}

	/**
	 * Test beans_get_post_meta() should get the post ID when none is provided.
	 */
	public function test_should_return_get_post_id_when_none_is_provided() {
		Monkey\Functions\expect( 'get_the_id' )->once()->andReturn( 47 );
		Monkey\Functions\expect( 'get_post_meta' )->with( 47 )->once()->andReturn( array() );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c' ) );

		$_GET['post'] = '18';
		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
		Monkey\Functions\expect( 'get_the_id' )->once()->andReturn( 0 );
		Monkey\Functions\expect( 'get_post_meta' )->with( '18' )->once()->andReturn( array() );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c' ) );
		unset( $_GET['post'] );
	}

	/**
	 * Test beans_get_post_meta() should return the post's meta value.
	 */
	public function test_should_return_post_meta_value() {
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 521 )
			->once()
			->ordered()
			->andReturn( array( 'beans_layout' => 'c_sp' ) )
			->andAlsoExpectIt()
			->with( 521, 'beans_layout', true )
			->once()
			->ordered()
			->andReturn( 'c_sp' );
		$this->assertSame( 'c_sp', beans_get_post_meta( 'beans_layout', false, 521 ) );

		Monkey\Functions\expect( 'get_the_id' )->once()->andReturn( 47 );
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 47 )
			->once()
			->ordered()
			->andReturn( array( 'beans_layout' => 'sp_c' ) )
			->andAlsoExpectIt()
			->with( 47, 'beans_layout', true )
			->once()
			->ordered()
			->andReturn( 'sp_c' );
		$this->assertSame( 'sp_c', beans_get_post_meta( 'beans_layout' ) );

		$_GET['post'] = '18';
		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
		Monkey\Functions\expect( 'get_the_id' )->once()->andReturn( 0 );
		Monkey\Functions\expect( 'get_post_meta' )
			->with( '18' )
			->once()
			->ordered()
			->andReturn( array( 'beans_layout' => 'default_fallback' ) )
			->andAlsoExpectIt()
			->with( '18', 'beans_layout', true )
			->once()
			->ordered()
			->andReturn( 'default_fallback' );
		$this->assertSame( 'default_fallback', beans_get_post_meta( 'beans_layout', 'c' ) );
		unset( $_GET['post'] );
	}
}
