<?php
/**
 * Tests for beans_reset_markup().
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
 * Class Tests_BeansResetMarkup
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansResetMarkup extends HTML_Test_Case {

	/**
	 * Test beans_reset_markup() should reset the HTML markup opening and closing tag.
	 */
	public function test_should_reset_html_markup_opening_and_closing_tag() {
		// First, register to modify the tag.
		$anonymous_filter = beans_modify_markup( 'beans_archive_title', 'h2', 20 );
		$this->assertEquals( 20, has_filter( 'beans_archive_title_markup', [ $anonymous_filter, 'callback' ] ) );

		// Reset it.
		beans_reset_markup( 'beans_archive_title' );

		// Check that it did reset.
		$this->assertFalse( has_filter( 'beans_archive_title_markup' ) );

		// Double check by building the markup.
		$actual = beans_open_markup( 'beans_archive_title', 'h1' );
		$this->assertSame( '<h1 >', $actual );
		$this->assertSame( '</h1>', beans_close_markup( 'beans_archive_title', 'h1' ) );
	}

	/**
	 * Test beans_reset_markup() should reset the HTML markup self-closing tag.
	 */
	public function test_should_reset_html_markup_self_closing_tag() {
		// First, register to modify the tag.
		$anonymous_filter = beans_modify_markup( 'beans_post_image_item', 'foo', 20 );
		$this->assertEquals( 20, has_filter( 'beans_post_image_item_markup', [ $anonymous_filter, 'callback' ] ) );

		// Reset it.
		beans_reset_markup( 'beans_post_image_item' );

		// Check that it did reset.
		$this->assertFalse( has_filter( 'beans_post_image_item_markup' ) );

		// Double check by building the markup.
		$actual = beans_selfclose_markup( 'beans_post_image_item', 'img' );
		$this->assertSame( '<img />', $actual );
	}

	/**
	 * Test beans_reset_markup() should reset sub-hooks.
	 */
	public function test_should_reset_sub_hooks() {
		// First, register to modify the tag.
		$anonymous_filter = beans_modify_markup( 'beans_title[_foo]', 'p', 20 );
		$this->assertEquals( 20, has_filter( 'beans_title[_foo]_markup', [ $anonymous_filter, 'callback' ] ) );

		// Reset it.
		beans_reset_markup( 'beans_title[_foo]' );

		// Check that it did reset.
		$this->assertFalse( has_filter( 'beans_title[_foo]_markup' ) );

		// Double check by building the markup.
		$actual = beans_open_markup( 'beans_title[_foo]', 'h4' );
		$this->assertSame( '<h4 >', $actual );
		$this->assertSame( '</h4>', beans_close_markup( 'beans_title[_foo]', 'h4' ) );
	}
}
