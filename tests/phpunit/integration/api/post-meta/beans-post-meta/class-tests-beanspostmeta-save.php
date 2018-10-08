<?php
/**
 * Tests for the save() method of Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Post_Meta_Test_Case;
use Beans_Post_Meta;

require_once BEANS_THEME_DIR . '/lib/api/post-meta/class-beans-post-meta.php';
require_once dirname( __DIR__ ) . '/includes/class-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_Save.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_Save extends Post_Meta_Test_Case {

	/**
	 * Test Beans_Post_Meta::save() should return the post_ID when ok_to_save() is false.
	 */
	public function test_should_return_post_id_when_ok_to_save_is_false() {
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$post_id   = $this->factory()->post->create();

		$this->assertEquals( $post_id, $post_meta->save( $post_id ) );
	}

	/**
	 * Test Beans_Post_Meta::save() should run update_post_meta() and return null when ok_to_save() is true.
	 */
	public function test_should_run_update_post_meta_and_return_null_when_ok_to_save() {
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		$post_id   = $this->factory()->post->create();

		// Run with permission to save.
		$user_id = $this->factory()->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user_id );

		// Give beans_post() a field to find and set a nonce to return.
		$_POST['beans_fields']          = [ 'beans_post_test_field' => 'beans_post_test_field_value' ];
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertnull( $post_meta->save( $post_id ) );
		$this->assertEquals( 'beans_post_test_field_value', get_post_meta( $post_id, 'beans_post_test_field', true ) );
	}
}
