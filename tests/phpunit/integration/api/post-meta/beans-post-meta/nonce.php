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
 * Class Tests_Beans_Post_Meta_Nonce
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Nonce extends WP_UnitTestCase {

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
}
