<?php
/** Tests for beans_post_meta_page_template_reload.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_Beans_Post_Meta_Page_Template_Reload
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_Beans_Post_Meta_Page_Template_Reload extends Test_Case {

	/**
	 * Test beans_post_meta_page_template_reload does nothing when not editing a post object.
	 */
	public function test_does_nothing_when_not_editing_post_object() {
		global $pagenow;
		$pagenow = 'wp-login.php';

		ob_start();
		_beans_post_meta_page_template_reload();
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Test beans_post_meta_page_template_reload does nothing when post meta not assigned to page templates.
	 */
	public function test_does_nothing_when_post_meta_not_assigned_to_page_templates() {
		global $_beans_post_meta_conditions, $pagenow;

		$_beans_post_meta_conditions = array();
		$pagenow                     = 'post.php';

		ob_start();
		_beans_post_meta_page_template_reload();
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Test beans_post_meta_page_template_reload outputs script html when post meta is assigned to page templates.
	 */
	public function test_does_nothing_when_post_meta_assigned_to_page_templates() {
		global $_beans_post_meta_conditions, $pagenow;

		$_beans_post_meta_conditions = array( 'page-template-name.php' );
		$pagenow                     = 'post.php';

		ob_start();
		_beans_post_meta_page_template_reload();
		$output = ob_get_clean();

		$this->assertContains( '<script type="text/javascript">', $output );
	}
}
