<?php
/**
 * Tests for the render_nonce method of _Beans_Post_Meta.
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
 * Class Tests_BeansPostMeta_Render_Nonce
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_Render_Nonce extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::nonce() should echo correct nonce input HTML when called.
	 */
	public function test_nonce_should_echo_nonce_input_html() {
		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );

		$expected_html_output = '<input type="hidden" name="beans_post_meta_nonce" value="%x" />';

		ob_start();
		$post_meta->render_nonce();
		$actual_output = ob_get_clean();

		$this->assertStringMatchesFormat( $expected_html_output, $actual_output );
	}
}
