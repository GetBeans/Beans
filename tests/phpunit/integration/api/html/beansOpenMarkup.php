<?php
/**
 * Tests for beans_open_markup().
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansOpenMarkup
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOpenMarkup extends HTML_Test_Case {

	/**
	 * Test beans_open_markup() should return null when the tag is set to null.
	 */
	public function test_should_return_null_when_tag_set_to_false() {
		$this->assertNull( beans_open_markup( 'beans_archive_title', null, array( 'class' => 'uk-article-title' ) ) );
	}

	/**
	 * Test beans_open_markup() should fire "{$id}_before_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_before_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\expect( '__beans_render_title_before_markup' )
			->once()
			->with( '' )
			->andReturnUsing( function() {
				echo '<!-- _before_markup fired -->';
			} );
		add_action( 'beans_archive_title_before_markup', '__beans_render_title_before_markup' );

		// Run the tests.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertStringStartsWith( '<!-- _before_markup fired -->', $actual );
	}

	/**
	 * Test beans_open_markup() should fire "{$id}_prepend_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_prepend_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\expect( '__beans_render_title_prepend_markup' )
			->once()
			->with( '' )
			->andReturnUsing( function() {
				echo '<!-- _prepend_markup fired -->';
			} );
		add_action( 'beans_archive_title_prepend_markup', '__beans_render_title_prepend_markup' );

		// Run the tests.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertStringEndsWith( '<!-- _prepend_markup fired -->', $actual );
	}

	/**
	 * Test beans_open_markup() should fire _beans_render_action() for the "_after_markup" hooks when the global
	 * $_temp_beans_selfclose_markup is set to true.
	 */
	public function test_should_fire_after_markup_hooks_when_selfclose_is_true() {
		Monkey\Functions\expect( '__beans_render_title_after_markup' )
			->once()
			->with( '' )
			->andReturnUsing( function() {
				echo '<!-- _after_markup fired -->';
			} );
		add_action( 'beans_archive_title_after_markup', '__beans_render_title_after_markup' );

		global $_temp_beans_selfclose_markup;
		$_temp_beans_selfclose_markup = true; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Used in function scope.

		// Run the tests.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
		$this->assertStringEndsWith( '<!-- _after_markup fired -->', $actual );

		// Check that the global was unset.
		$this->assertArrayNotHasKey( '_temp_beans_selfclose_markup', $GLOBALS );
	}

	/**
	 * Test beans_open_markup() should return built HTML when no before or prepend hooks are registered.
	 */
	public function test_should_return_built_html_when_before_or_prepend_hooks_registered() {
		$actual = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertSame( '<h1 class="uk-article-title">', $actual );
		$this->assertEquals( 0, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_open_markup() should return built HTML with the "data-markup-id" when in development mode.
	 */
	public function test_should_return_built_html_with_data_markup_id_when_in_dev_mode() {
		add_option( 'beans_dev_mode', 1 );

		$actual = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertSame( '<h1 class="uk-article-title" data-markup-id="beans_archive_title">', $actual );
	}

	/**
	 * Test beans_open_markup() should return a built HTML element when there are before and prepend hooks are
	 * registered.
	 */
	public function test_should_return_built_html_when_before_or_prepend_hooks() {
		add_action( 'beans_archive_title_before_markup', function() {
			echo '<!-- _before_markup fired -->';
		} );
		add_action( 'beans_archive_title_prepend_markup', function() {
			echo '<!-- _prepend_markup fired -->';
		} );

		// Run the tests.
		$actual   = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$expected = <<<EOB
<!-- _before_markup fired --><h1 class="uk-article-title"><!-- _prepend_markup fired -->
EOB;

		$this->assertSame( $expected, $actual );
		$this->assertEquals( 1, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_open_markup() should return a built self-closing HTML element when the global
	 * $_temp_beans_selfclose_markup is set to true.
	 */
	public function test_should_return_built_self_closing_html_when_selfclose_markup_is_true() {
		$args = array(
			'width'    => 800,
			'height'   => 500,
			'src'      => 'http://example.com/image.png',
			'alt'      => 'Some image',
			'itemprop' => 'image',
		);

		// Run it with development mode off.
		global $_temp_beans_selfclose_markup;
		$_temp_beans_selfclose_markup = true; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Used in function scope.

		$actual   = beans_open_markup( 'beans_post_image_item', 'img', $args, 'http://example.com/image.png' );
		$expected = <<<EOB
<img width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image"/>
EOB;
		$this->assertSame( $expected, $actual );

		// Run it with development mode on.
		add_option( 'beans_dev_mode', 1 );
		global $_temp_beans_selfclose_markup;
		$_temp_beans_selfclose_markup = true; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Used in function scope.

		$actual   = beans_open_markup( 'beans_post_image_item', 'img', $args, 'http://example.com/image.png' );
		$expected = <<<EOB
<img width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image" data-markup-id="beans_post_image_item"/>
EOB;
		$this->assertSame( $expected, $actual );
		$this->assertEquals( 0, did_action( 'beans_post_image_item_before_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_post_image_item_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_post_image_item_after_markup' ) );
	}
}
