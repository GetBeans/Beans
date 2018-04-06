<?php
/**
 * Tests for the register_metabox method of _Beans_Post_Meta.
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
 * Class Tests_Beans_Post_Meta_Register_Metabox.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Register_Metabox extends WP_UnitTestCase {

	/**
	 * Test _Beans_Post_Meta::register_metabox() should register an appropriate metabox.
	 */
	public function test_register_metabox_should_register_metabox() {
		global $wp_meta_boxes;

		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		$post_meta->register_metabox( 'post' );

		$this->assertArrayHasKey( 'tm-beans', $wp_meta_boxes['post']['normal']['high'] );
	}
}