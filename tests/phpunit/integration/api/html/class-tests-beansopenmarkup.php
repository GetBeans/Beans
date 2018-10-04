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
	public function test_should_return_null_when_tag_set_to_null() {
		$this->assertNull( beans_open_markup( 'beans_archive_title', null, [ 'class' => 'uk-article-title' ] ) );
	}

	/**
	 * Test beans_open_markup() should fire "{$id}_before_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_before_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\expect( '__beans_render_title_before_markup' )
			->once()
			->with( '' )
			->andReturnUsing(
				function() {
						echo '<!-- _before_markup fired -->';
				}
			);
		add_action( 'beans_archive_title_before_markup', '__beans_render_title_before_markup' );

		// Run the tests.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
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
			->andReturnUsing(
				function() {
						echo '<!-- _prepend_markup fired -->';
				}
			);
		add_action( 'beans_archive_title_prepend_markup', '__beans_render_title_prepend_markup' );

		// Run the tests.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertEquals( 1, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertStringEndsWith( '<!-- _prepend_markup fired -->', $actual );
	}

	/**
	 * Test beans_open_markup() should fire _beans_render_action() for the "_after_markup" hooks when the global
	 * $_beans_is_selfclose_markup is set to true.
	 */
	public function test_should_fire_after_markup_hooks_when_selfclose_is_true() {
		Monkey\Functions\expect( '__beans_render_title_after_markup' )
			->once()
			->with( '' )
			->andReturnUsing(
				function() {
						echo '<!-- _after_markup fired -->';
				}
			);
		add_action( 'beans_archive_title_after_markup', '__beans_render_title_after_markup' );

		global $_beans_is_selfclose_markup;
		$_beans_is_selfclose_markup = true;

		// Run the tests.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
		$this->assertStringEndsWith( '<!-- _after_markup fired -->', $actual );

		// Check that the global was unset.
		$this->assertArrayNotHasKey( '_beans_is_selfclose_markup', $GLOBALS );
	}

	/**
	 * Test beans_open_markup() should return the built HTML element when before or prepend hooks are not registered.
	 */
	public function test_should_return_built_html_when_before_or_prepend_hooks_not_registered() {
		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<h1 class="uk-article-title">', $actual );
		$this->assertEquals( 0, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_open_markup() should return the built HTML element with the "data-markup-id" when in development mode.
	 */
	public function test_should_return_built_html_with_data_markup_id_when_in_dev_mode() {
		add_option( 'beans_dev_mode', 1 );

		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<h1 class="uk-article-title" data-markup-id="beans_archive_title">', $actual );
	}

	/**
	 * Test beans_open_markup() should return the built HTML when before and prepend hooks are registered.
	 */
	public function test_should_return_built_html_when_before_or_prepend_hooks() {
		add_action(
			'beans_archive_title_before_markup',
			function() {
				echo '<!-- _before_markup fired -->';
			}
		);
		add_action(
			'beans_archive_title_prepend_markup',
			function() {
				echo '<!-- _prepend_markup fired -->';
			}
		);

		// Run the tests.
		$actual   = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
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
	 * $_beans_is_selfclose_markup is set to true.
	 */
	public function test_should_return_built_self_closing_html_when_selfclose_markup_is_true() {
		$args = [
			'width'    => 800,
			'height'   => 500,
			'src'      => 'http://example.com/image.png',
			'alt'      => 'Some image',
			'itemprop' => 'image',
		];

		// Run it with development mode off.
		global $_beans_is_selfclose_markup;
		$_beans_is_selfclose_markup = true;

		$actual   = beans_open_markup( 'beans_post_image_item', 'img', $args, 'http://example.com/image.png' );
		$expected = <<<EOB
<img width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image"/>
EOB;
		$this->assertSame( $expected, $actual );

		// Run it with development mode on.
		add_option( 'beans_dev_mode', 1 );
		global $_beans_is_selfclose_markup;
		$_beans_is_selfclose_markup = true;

		$actual   = beans_open_markup( 'beans_post_image_item', 'img', $args, 'http://example.com/image.png' );
		$expected = <<<EOB
<img width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image" data-markup-id="beans_post_image_item"/>
EOB;
		$this->assertSame( $expected, $actual );
		$this->assertEquals( 0, did_action( 'beans_post_image_item_before_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_post_image_item_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_post_image_item_after_markup' ) );
	}

	/**
	 * Test beans_open_markup() should return only the output from the hooked callbacks and not the HTML element when
	 * the tag is empty.
	 */
	public function test_should_return_only_hooked_callbacks_output_and_no_html_element_when_tag_is_empty() {
		add_action(
			'beans_archive_title_before_markup',
			function() {
				echo '<!-- _before_markup fired -->';
			}
		);
		add_action(
			'beans_archive_title_prepend_markup',
			function() {
				echo '<!-- _prepend_markup fired -->';
			}
		);

		// Check with an empty string.
		$actual = beans_open_markup( 'beans_archive_title', '', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<!-- _before_markup fired --><!-- _prepend_markup fired -->', $actual );

		// Check with false.
		$actual = beans_open_markup( 'beans_archive_title', false, [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<!-- _before_markup fired --><!-- _prepend_markup fired -->', $actual );

		// Check the hooks.
		$this->assertEquals( 2, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertEquals( 2, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_open_markup() should escape the built HTML tag.
	 */
	public function test_should_escape_built_html_tag() {
		$expected = <<<EOB
<&lt;script&gt;alert(&quot;Should escape me.&quot;)&lt;/script&gt; class="uk-article-title" itemprop="headline">
EOB;
		// Check when given as the tag.
		$actual = beans_open_markup(
			'beans_post_title',
			'<script>alert("Should escape me.")</script>',
			[
				'class'    => 'uk-article-title',
				'itemprop' => 'headline',
			]
		);
		$this->assertSame( $expected, $actual );

		// Check when tag is filtered.
		add_filter(
			'beans_post_title_markup',
			function() {
				return '<script>alert("Should escape me.")</script>';
			}
		);
		$actual = beans_open_markup(
			'beans_post_title',
			'h1',
			[
				'class'    => 'uk-article-title',
				'itemprop' => 'headline',
			]
		);
		$this->assertSame( $expected, $actual );

		// Check the attributes too.
		$expected = <<<EOB
<a href="http://example.com/testing-ensure-safe?val=scriptalert(Should%20escape%20me.);/script" title="Testing to ensure safe." rel="bookmark">
EOB;
		$this->assertSame(
			$expected,
			beans_open_markup(
				'beans_post_title_link',
				'a',
				[
					'href'  => 'http://example.com/testing-ensure-safe?val=<script>alert("Should escape me.");</script>',
					'title' => 'Testing to ensure safe.',
					'rel'   => 'bookmark',
				]
			)
		);
	}
}
