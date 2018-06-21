<?php
/**
 * Tests for the save() method of _Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta.
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Post_Meta_Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_Save
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_Save extends Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::save() should return false when doing autosave.
	 */
	public function test_should_return_false_when_doing_autosave() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( '_beans_doing_autosave' )->once()->andReturn( true );
		$this->assertFalse( $post_meta->save( 256 ) );
	}

	/**
	 * Test _Beans_Post_Meta::save() should return the post_ID when ok_to_save() is false.
	 */
	public function test_should_return_post_id_when_ok_to_save_false() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		Monkey\Functions\expect( '_beans_doing_autosave' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( false );
		$this->assertEquals( 256, $post_meta->save( 256 ) );
	}

	/**
	 * Test _Beans_Post_Meta::save() should run update_post_meta() and return null when ok_to_save() is true.
	 */
	public function test_should_run_update_post_meta_and_return_null_when_ok_to_save() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$fields    = [ 'beans_post_test_field' => 'beans_test_post_field_value' ];

		Monkey\Functions\expect( '_beans_doing_autosave' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_post' )->andReturn( $fields );
		Monkey\Functions\expect( 'update_post_meta' )
			->once()
			->with( 256, 'beans_post_test_field', 'beans_test_post_field_value' )
			->andReturn( true );
		$this->assertnull( $post_meta->save( 256 ) );
	}
}
