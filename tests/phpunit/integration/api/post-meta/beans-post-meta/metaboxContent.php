<?php
/**
 * Tests for the metabox_content method of _Beans_Post_Meta.
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
 * Class Tests_Beans_Post_Meta_Metabox_Content.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Metabox_Content extends WP_UnitTestCase {

	/**
	 * Test _Beans_Post_Meta::metabox_content() should output post meta fields markup.
	 */
	public function test_metabox_content_should_return_fields_markup() {
		$test_data = require dirname( ( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
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
}
