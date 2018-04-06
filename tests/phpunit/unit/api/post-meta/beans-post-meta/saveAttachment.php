<?php
/**
 * Tests the save_attachment method of _Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_Beans_Post_Meta_Save_Attachment
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_Save_Attachment extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::save_attachment() doesn't update post meta when doing autosave.
	 */
	public function test_save_attachment_should_not_update_post_meta_when_doing_autosave() {
		$post_meta  = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$attachment = array( 'ID' => 543 );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( true );
		Monkey\Functions\expect( 'update_post_meta' )->never();
		$this->assertEquals( $attachment, $post_meta->save_attachment( $attachment ) );
	}

	/**
	 * Test _Beans_Post_Meta::save_attachment() runs update_post_meta() and returns attachment when ok_to_save() is true.
	 */
	public function test_save_attachment_should_run_update_post_meta_and_return_attachment_when_ok_to_save() {
		$post_meta  = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$attachment = array( 'ID' => 543 );
		$fields     = array( 'beans_post_test_field' => 'beans_test_post_field_value' );

		Monkey\Functions\expect( 'beans_doing_autosave' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_verify_nonce' )->once()->andReturn( true );
		Monkey\Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Monkey\Functions\expect( 'beans_post' )->andReturn( $fields );
		Monkey\Functions\expect( 'update_post_meta' )
			->once()
			->with( 543, 'beans_post_test_field', 'beans_test_post_field_value' )
			->andReturn( true );
		$this->assertEquals( $attachment, $post_meta->save_attachment( $attachment ) );
	}
}

