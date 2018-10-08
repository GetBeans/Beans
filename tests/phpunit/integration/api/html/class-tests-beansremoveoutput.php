<?php
/**
 * Tests for beans_remove_output().
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans_Anonymous_Filters;
use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansRemoveOutput
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansRemoveOutput extends HTML_Test_Case {

	/**
	 * Test beans_remove_output() should return a Beans_Anonymous_Filters instance.
	 */
	public function test_should_return_anonymous_filter_instance() {
		$this->assertInstanceOf( Beans_Anonymous_Filters::class, beans_remove_output( 'beans_post_meta_item_date' ) );
	}

	/**
	 * Test beans_remove_output() should register a callback to the "{$id}_output" filter hook.
	 */
	public function test_should_return_register_callback_to_id_output_filter() {
		$anonymous_filter = beans_remove_output( 'beans_post_meta_item_date' );

		$this->assertSame(
			99999999,
			has_filter(
				'beans_post_meta_item_date_output',
				[
					$anonymous_filter,
					'callback',
				]
			)
		);
	}

	/**
	 * Test beans_remove_output() should remove the output.
	 */
	public function test_should_return_remove_output() {
		// Check that everything works before we remove it.
		$this->assertSame( 'Beans rocks!', beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );

		// Now remove the output.
		beans_remove_output( 'beans_archive_title_text' );

		// Check when not in developer mode.
		add_option( 'beans_dev_mode', 0 );
		$this->assertNull( beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );

		// Check when in developer mode.
		update_option( 'beans_dev_mode', 1 );
		$this->assertNull( beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
	}
}
