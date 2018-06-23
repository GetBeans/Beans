<?php
/**
 * Tests for the ok_to_save() method of _Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use _Beans_Post_Meta;

require_once BEANS_THEME_DIR . '/lib/api/post-meta/class-beans-post-meta.php';
require_once dirname( __DIR__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_OkToSave.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_OkToSave extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::ok_to_save() should return false when nonce check fails.
	 */
	public function test_ok_to_save_should_return_false_when_nonce_check_fails() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$post_id   = $this->factory()->post->create();

		$this->assertFalse( $post_meta->ok_to_save( $post_id, [ [ 'id' => 'beans_test_slider' ] ] ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() should return false when `edit_post` user permissions are not met.
	 */
	public function test_ok_to_save_should_return_false_when_user_permissions_not_met() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$post_id   = $this->factory()->post->create();

		// Run without permission to save.
		$user_id = $this->factory()->user->create( [ 'role' => 'subscriber' ] );
		wp_set_current_user( $user_id );

		// Set a nonce to return.
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertFalse( $post_meta->ok_to_save( $post_id, [ [ 'id' => 'beans_test_slider' ] ] ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() should return false when post meta has no fields.
	 */
	public function test_ok_to_save_should_return_false_when_no_fields() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$post_id   = $this->factory()->post->create();

		// Run with permission to save.
		$user_id = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		// Set a nonce to return.
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertFalse( $post_meta->ok_to_save( $post_id, [] ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() should return true when all conditions for saving are met.
	 */
	public function test_ok_to_save_should_return_true_when_all_conditions_met() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$post_id   = $this->factory()->post->create();

		// Run with permission to save.
		$user_id = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		// Set a nonce to return.
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertTrue( $post_meta->ok_to_save( $post_id, [ [ 'id' => 'beans_test_slider' ] ] ) );
	}
}
