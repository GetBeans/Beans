<?php
/**
 * Tests for the metabox_content method of _Beans_Post_Meta.
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
 * Class Tests_Beans_Post_Meta_Metabox_Content.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Metabox_Content extends Beans_Post_Meta_Test_Case {

	/**
	 * Fixture to clean up after tests.
	 */
	public function tearDown() {
		parent::tearDown();
	}

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
