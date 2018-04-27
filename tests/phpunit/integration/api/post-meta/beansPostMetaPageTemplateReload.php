<?php
/**
 * Tests for beans_post_meta_page_template_reload()
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;

require_once BEANS_API_PATH . 'post-meta/functions-admin.php';
require_once dirname( __FILE__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMetaPageTemplateReload extends Beans_Post_Meta_Test_Case {

	/**
	 * Test beans_post_meta_page_template_reload should do nothing when not editing a post object.
	 */
	public function test_does_nothing_when_not_editing_post_object() {
		global $pagenow;
		$pagenow = 'wp-login.php'; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Resetting global here for tests.

		ob_start();
		_beans_post_meta_page_template_reload();
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Test beans_post_meta_page_template_reload should do nothing when post meta not assigned to page templates.
	 */
	public function test_does_nothing_when_post_meta_not_assigned_to_page_templates() {
		global $_beans_post_meta_conditions, $pagenow;

		$_beans_post_meta_conditions = array();
		$pagenow                     = 'post.php'; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Resetting global here for tests.

		ob_start();
		_beans_post_meta_page_template_reload();
		$output = ob_get_clean();

		$this->assertSame( '', $output );
	}

	/**
	 * Test beans_post_meta_page_template_reload should output script html when post meta is assigned to page templates.
	 */
	public function test_does_nothing_when_post_meta_assigned_to_page_templates() {
		global $_beans_post_meta_conditions, $pagenow;

		$_beans_post_meta_conditions = array( 'page-template-name.php' );
		$pagenow                     = 'post.php'; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Resetting global here for tests.

		ob_start();
		_beans_post_meta_page_template_reload();
		$output = ob_get_clean();

		$this->assertContains( '<script type="text/javascript">', $output );
	}
}
