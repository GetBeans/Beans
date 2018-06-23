<?php
/**
 * Tests for beans_remove_markup().
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use _Beans_Anonymous_Filters;
use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansRemoveMarkup
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansRemoveMarkup extends HTML_Test_Case {

	/**
	 * Test beans_remove_markup() should return a _Beans_Anonymous_Filters instance.
	 */
	public function test_should_return_anonymous_filter_instance() {
		$this->assertInstanceOf( _Beans_Anonymous_Filters::class, beans_remove_markup( 'beans_archive_title' ) );
	}

	/**
	 * Test beans_remove_markup() should register a callback to the "{$id}_markup" filter hook.
	 */
	public function test_should_register_callback_to_id_markup_filter() {
		$anonymous_filter = beans_remove_markup( 'beans_archive_title' );

		$this->assertSame( 10, has_filter( 'beans_archive_title_markup', [
			$anonymous_filter,
			'callback',
		] ) );
	}

	/**
	 * Test beans_remove_markup() should remove the markup's element when $remove_actions is false (default behavior).
	 */
	public function test_should_remove_only_element_when_remove_actions_is_false() {
		beans_remove_markup( 'beans_archive_title' );

		// Let's test it out by running the markup for this ID.
		add_action( 'beans_archive_title_before_markup', function() {
			echo '<!-- _before_markup fired -->';
		} );
		add_action( 'beans_archive_title_prepend_markup', function() {
			echo '<!-- _prepend_markup fired -->';
		} );
		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<!-- _before_markup fired --><!-- _prepend_markup fired -->', $actual );
		$this->assertSame( '', beans_close_markup( 'beans_archive_title', 'h1' ) );
	}

	/**
	 * Test beans_remove_markup() should remove the HTML markup when $remove_actions is true.
	 */
	public function test_should_remove_html_markup_when_remove_actions_is_true() {
		beans_remove_markup( 'beans_archive_title' );

		// Let's test it out by running the markup for this ID.
		$this->assertSame( '', beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] ) );
		$this->assertSame( '', beans_close_markup( 'beans_archive_title', 'h1' ) );
	}
}
