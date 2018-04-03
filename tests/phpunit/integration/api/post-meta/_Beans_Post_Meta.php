<?php
/**
 * Tests for beans_get_post_meta()
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use WP_UnitTestCase;
use _Beans_Post_Meta;

require_once BEANS_THEME_DIR . '/lib/api/post-meta/class.php';

/**
 * Class Tests_Beans_Post_Meta
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   integration-tests
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta extends WP_UnitTestCase {

	/**
	 * Test correct hooks are set on class instantiation.
	 */
	public function test_construct_sets_correct_hooks_when_instantiated() {
		// First instantiation sets all hooks.
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		$this->assertEquals( 10, has_action( 'edit_form_top', array( $post_meta, 'nonce' ) ) );
		$this->assertEquals( 10, has_action( 'save_post', array( $post_meta, 'save' ) ) );
		$this->assertEquals( 10, has_filter( 'attachment_fields_to_save', array( $post_meta, 'save_attachment' ) ) );
		$this->assertEquals( 10, has_action( 'add_meta_boxes', array( $post_meta, 'register_metabox' ) ) );

		// Subsequent instantiation sets 'add_meta_boxes' hook only.
		$post_meta_2 = new _Beans_Post_Meta( 'tm-beans-custom-post-meta', array( 'title' => 'Custom Options' ) );

		$this->assertFalse( has_action( 'edit_form_top', array( $post_meta_2, 'nonce' ) ) );
		$this->assertFalse( has_action( 'save_post', array( $post_meta_2, 'save' ) ) );
		$this->assertFalse( has_filter( 'attachment_fields_to_save', array( $post_meta_2, 'save_attachment' ) ) );
		$this->assertEquals( 10, has_action( 'add_meta_boxes', array( $post_meta_2, 'register_metabox' ) ) );

	}

	/**
	 * Test _Beans_Post_Meta::nonce() should output correct nonce html.
	 */
	public function test_nonce_should_echo_nonce_input_html() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		$expected_html_output = '<input type="hidden" name="beans_post_meta_nonce" value="%x" />';

		ob_start();
		$post_meta->nonce();
		$actual_output = ob_get_clean();

		$this->assertStringMatchesFormat( $expected_html_output, $actual_output );
	}

	/**
	 * Test _Beans_Post_Meta::register_metabox() should register an appropriate metabox.
	 */
	public function test_register_metabox_should_register_metabox() {
		global $wp_meta_boxes;

		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_meta->register_metabox( 'post' );

		$this->assertArrayHasKey( 'tm-beans', $wp_meta_boxes['post']['normal']['high'] );
	}

	/**
	 * Test _Beans_Post_Meta::metabox_content() should output post meta fields markup.
	 */
	public function test_metabox_content_should_return_fields_markup() {
		$test_data = require dirname( ( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		beans_register_fields( $test_data['fields'], 'post_meta', $test_data['section'] );

		$post_id = $this->factory()->post->create();

		ob_start();
		$post_meta->metabox_content( $post_id );
		$output = ob_get_clean();

		$this->assertContains( 'bs-radio', $output );
		$this->assertContains( 'bs-checkbox', $output );
		$this->assertContains( 'bs-text', $output );
	}

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

	/**
	 * Test _Beans_Post_Meta::save_attachment() runs update_post_meta() and returns attachment when ok_to_save() is true.
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

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false with unverified nonce.
	 */
	public function test_ok_to_save_should_return_false_when_unverified_nonce() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_id   = $this->factory()->post->create();

		$this->assertFalse( $post_meta->ok_to_save( $post_id, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false with invalid user permissions.
	 */
	public function test_ok_to_save_should_return_false_when_user_cannot_edit() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_id   = $this->factory()->post->create();

		// Run without permission to save.
		$user_id = $this->factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );

		// Set a nonce to return.
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertFalse( $post_meta->ok_to_save( $post_id, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is false when post meta has no fields.
	 */
	public function test_ok_to_save_should_return_false_when_fields_empty() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_id   = $this->factory()->post->create();

		// Run with permission to save.
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Set a nonce to return.
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertFalse( $post_meta->ok_to_save( $post_id, array() ) );
	}

	/**
	 * Test _Beans_Post_Meta::ok_to_save() is true when all conditions for saving are met.
	 */
	public function test_ok_to_save_should_return_true_when_all_conditions_met() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_id   = $this->factory()->post->create();

		// Run with permission to save.
		$user_id = $this->factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );

		// Set a nonce to return.
		$_POST['beans_post_meta_nonce'] = wp_create_nonce( 'beans_post_meta_nonce' );

		$this->assertTrue( $post_meta->ok_to_save( $post_id, array( array( 'id' => 'beans_test_slider' ) ) ) );
	}
}
