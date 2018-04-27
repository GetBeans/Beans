<?php
/**
 * Tests the nonce method of _Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;
use _Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_Beans_Post_Meta_Nonce
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Nonce extends Beans_Post_Meta_Test_Case {

	/**
	 * Test _Beans_Post_Meta::nonce() should output correct nonce html.
	 */
	public function test_nonce_should_echo_nonce_input_html() {
		Monkey\Functions\expect( 'wp_create_nonce' )->once()->with( 'beans_post_meta_nonce' )->andReturn( '123456' );
		$expected_html_output = '<input type="hidden" name="beans_post_meta_nonce" value="123456" />';

		$post_meta = new _Beans_Post_Meta( 'tm-beans', array( 'title' => 'Post Options' ) );
		ob_start();
		$post_meta->render_nonce();
		$actual_output = ob_get_clean();

		$this->assertContains( $expected_html_output, $actual_output );
	}
}
