<?php
/**
 * Tests for the __construct() method of _Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Post_Meta_Test_Case;
use _Beans_Post_Meta;

require_once BEANS_THEME_DIR . '/lib/api/post-meta/class-beans-post-meta.php';
require_once dirname( __DIR__ ) . '/includes/class-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_Construct.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_Construct extends Post_Meta_Test_Case {

	/**
	 * Test __construct() should set the correct hooks when the class is instantiated.
	 */
	public function test_should_set_correct_hooks_when_instantiated() {
		// First instantiation sets all hooks.
		$post_meta = new _Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		$this->assertEquals( 10, has_action( 'edit_form_top', [ $post_meta, 'render_nonce' ] ) );
		$this->assertEquals( 10, has_action( 'save_post', [ $post_meta, 'save' ] ) );
		$this->assertEquals( 10, has_filter( 'attachment_fields_to_save', [ $post_meta, 'save_attachment' ] ) );
		$this->assertEquals( 10, has_action( 'add_meta_boxes', [ $post_meta, 'register_metabox' ] ) );

		// Subsequent instantiation sets 'add_meta_boxes' hook only.
		$post_meta_2 = new _Beans_Post_Meta( 'tm-beans-custom-post-meta', [ 'title' => 'Custom Options' ] );

		$this->assertFalse( has_action( 'edit_form_top', [ $post_meta_2, 'render_nonce' ] ) );
		$this->assertFalse( has_action( 'save_post', [ $post_meta_2, 'save' ] ) );
		$this->assertFalse( has_filter( 'attachment_fields_to_save', [ $post_meta_2, 'save_attachment' ] ) );
		$this->assertEquals( 10, has_action( 'add_meta_boxes', [ $post_meta_2, 'register_metabox' ] ) );
	}
}
