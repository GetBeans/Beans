<?php
/**
 * Tests for the render_metabox_content() method of Beans_Post_Meta.
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
 * Class Tests_BeansPostMeta_RenderMetaboxContent.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_RenderMetaboxContent extends Post_Meta_Test_Case {

	/**
	 * Test Beans_Post_Meta::render_metabox_content() should output post meta fields markup when called.
	 */
	public function test_should_output_fields_markup() {
		$test_data = require dirname( ( __DIR__ ) ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );

		beans_register_fields( $test_data['fields'], 'post_meta', $test_data['section'] );

		$post_id = $this->factory()->post->create();

		ob_start();
		$post_meta->render_metabox_content( $post_id );
		$output = ob_get_clean();

		$this->assertContains( 'bs-radio', $output );
		$this->assertContains( 'bs-checkbox', $output );
		$this->assertContains( 'bs-text', $output );
	}
}
