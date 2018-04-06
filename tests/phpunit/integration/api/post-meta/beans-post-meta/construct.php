<?php
/**
 * Tests for the construct method of _Beans_Post_Meta.
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
 * Class Tests_Beans_Post_Meta_Construct.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Construct extends WP_UnitTestCase {

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
}