<?php
/**
 * Tests for the save method of _Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use WP_UnitTestCase;
use _Beans_Post_Meta;

require_once BEANS_THEME_DIR . '/lib/api/post-meta/class-beans-post-meta.php';

/**
 * Class Tests_Beans_Post_Meta_Save.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Save extends WP_UnitTestCase {

	/**
	 * Test _Beans_Post_Meta::save() returns post_id when ok_to_save() is false.
	 */
	public function test_save_should_return_post_id_when_not_ok_to_save() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_id   = $this->factory()->post->create();

		$this->assertEquals( $post_id, $post_meta->save( $post_id ) );
	}

	/**
	 * Test _Beans_Post_Meta::save() runs update_post_meta() and return null when ok_to_save() is true.
	 */
	public function test_save_should_run_update_post_meta_and_return_null_when_ok_to_save() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_id   = $this->factory()->post->create();

		// Run with permission to save.
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Give beans_post() a field to find and set a nonce to return.
		$_POST['beans_fields']          = array( 'beans_post_test_field' => 'beans_post_test_field_value' );
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertnull( $post_meta->save( $post_id ) );
		$this->assertEquals( 'beans_post_test_field_value', get_post_meta( $post_id, 'beans_post_test_field', true ) );
	}
}
