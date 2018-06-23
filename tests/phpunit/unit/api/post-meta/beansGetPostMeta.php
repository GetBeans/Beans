<?php
/**
 * Tests for beans_get_post_meta()
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Get_Post_Meta extends Beans_Post_Meta_Test_Case {

	/**
	 * Test beans_get_post_meta() should return the default when the post_id cannot be resolved.
	 */
	public function test_should_return_default_when_post_id_cannot_be_resolved() {
		Monkey\Functions\expect( 'get_the_ID' )->twice()->andReturn( false );
		Monkey\Functions\expect( 'beans_get' )->twice()->andReturn( null );
		Monkey\Functions\expect( 'get_post_meta' )->never();

		$this->assertFalse( beans_get_post_meta( 'beans_layout' ) );
		$this->assertSame( 'default_fallback', beans_get_post_meta( 'beans_layout', 'default_fallback' ) );
	}

	/**
	 * Test beans_get_post_meta() should return the default when the post meta does not exist.
	 */
	public function test_should_return_default_when_post_meta_does_not_exist() {
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 1 )
			->times( 3 )
			->andReturn( [] );

		$this->assertFalse( beans_get_post_meta( 'beans_layout', false, 1 ) );
		$this->assertSame( '', beans_get_post_meta( 'beans_layout', '', 1 ) );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c', 1 ) );
	}

	/**
	 * Test beans_get_post_meta() should get the post ID when none is provided.
	 */
	public function test_should_get_post_id_when_none_is_provided() {
		Monkey\Functions\expect( 'get_the_ID' )->once()->andReturn( 47 );
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 47 )
			->once()
			->andReturn( [] );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c' ) );

		Monkey\Functions\expect( 'get_the_ID' )->once()->andReturn( 0 );
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'post' )
			->andReturn( 18 );
		Monkey\Functions\expect( 'get_post_meta' )
			->with( '18' )
			->once()
			->andReturn( [] );
		$this->assertSame( 'c', beans_get_post_meta( 'beans_layout', 'c' ) );
	}

	/**
	 * Test beans_get_post_meta() should return the post's meta value.
	 */
	public function test_should_return_post_meta_value() {
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 521 )
			->once()
			->ordered()
			->andReturn( [ 'beans_layout' => 'c_sp' ] )
			->andAlsoExpectIt()
			->with( 521, 'beans_layout', true )
			->once()
			->ordered()
			->andReturn( 'c_sp' );
		$this->assertSame( 'c_sp', beans_get_post_meta( 'beans_layout', false, 521 ) );

		Monkey\Functions\expect( 'get_the_ID' )->once()->andReturn( 47 );
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 47 )
			->once()
			->ordered()
			->andReturn( [ 'beans_layout' => 'sp_c' ] )
			->andAlsoExpectIt()
			->with( 47, 'beans_layout', true )
			->once()
			->ordered()
			->andReturn( 'sp_c' );
		$this->assertSame( 'sp_c', beans_get_post_meta( 'beans_layout' ) );

		Monkey\Functions\expect( 'get_the_ID' )->once()->andReturn( 0 );
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'post' )
			->andReturn( 18 );
		Monkey\Functions\expect( 'get_post_meta' )
			->with( '18' )
			->once()
			->ordered()
			->andReturn( [ 'beans_layout' => 'default_fallback' ] )
			->andAlsoExpectIt()
			->with( '18', 'beans_layout', true )
			->once()
			->ordered()
			->andReturn( 'default_fallback' );
		$this->assertSame( 'default_fallback', beans_get_post_meta( 'beans_layout', 'c' ) );
	}
}
