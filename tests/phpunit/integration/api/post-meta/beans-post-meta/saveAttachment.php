<?php
/**
 * Tests for the save_attachment method of _Beans_Post_Meta.
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
 * Class Tests_BeansPostMeta_SaveAttachment.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_SaveAttachment extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::save_attachment() should run update_post_meta() and return attachment when ok_to_save() is true.
	 */
	public function test_save_attachment_should_run_update_post_meta_and_return_attachment_when_ok_to_save() {
		$post_meta       = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$attachment_id   = $this->factory()->attachment->create();
		$attachment_data = get_post( $attachment_id, ARRAY_A );

		// Run with permission to save.
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Give beans_post() a field to find and set a nonce to return.
		$_POST['beans_fields']          = array( 'beans_post_test_field' => 'beans_post_test_field_value' );
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertSame( $attachment_data, $post_meta->save_attachment( $attachment_data ) );
		$this->assertEquals( 'beans_post_test_field_value', get_post_meta( $attachment_id, 'beans_post_test_field', true ) );
	}
}
