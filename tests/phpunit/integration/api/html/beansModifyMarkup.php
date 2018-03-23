<?php
/**
 * Tests for beans_modify_markup().
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
 * Class Tests_BeansModifyMarkup
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansModifyMarkup extends HTML_Test_Case {

	/**
	 * Test beans_modify_markup() should return a _Beans_Anonymous_Filters instance.
	 */
	public function test_should_return_anonymous_filter_instance() {
		$this->assertInstanceOf( _Beans_Anonymous_Filters::class, beans_modify_markup( 'beans_archive_title', 'h2' ) );
	}

	/**
	 * Test beans_modify_markup() should register a callback to the "{$id}_markup" filter hook.
	 */
	public function test_should_register_callback_to_id_markup_filter() {
		$anonymous_filter = beans_modify_markup( 'beans_archive_title', 'h2' );

		$this->assertSame( 10, has_filter( 'beans_archive_title_markup', array(
			$anonymous_filter,
			'callback',
		) ) );
	}

	/**
	 * Test beans_modify_markup() should modify the markup HTML tag.
	 */
	public function test_should_modify_markup_html_tag() {
		beans_modify_markup( 'beans_archive_title', 'h2' );
		$expected = <<<EOB
<!-- _before_markup fired --><h2 class="uk-article-title"><!-- _prepend_markup fired -->
EOB;

		// Let's test it out by running the markup for this ID.
		add_action( 'beans_archive_title_before_markup', function() {
			echo '<!-- _before_markup fired -->';
		} );
		add_action( 'beans_archive_title_prepend_markup', function() {
			echo '<!-- _prepend_markup fired -->';
		} );

		// Check the opening markup.
		$actual = beans_open_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertSame( $expected, $actual );

		// Check the closing markup.
		$this->assertSame( '</h2>', beans_close_markup( 'beans_archive_title', 'h1' ) );
	}

	/**
	 * Test beans_modify_markup() should modify the markup HTML self-closing tag.
	 */
	public function test_should_modify_markup_html_self_closing_tag() {
		beans_modify_markup( 'beans_post_image_item', 'foo' );
		$expected = <<<EOB
<foo width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image"/>
EOB;

		$actual   = beans_selfclose_markup( 'beans_post_image_item', 'img', array(
			'width'    => 800,
			'height'   => 500,
			'src'      => 'http://example.com/image.png',
			'alt'      => 'Some image',
			'itemprop' => 'image',
		) );
		$this->assertSame( $expected, $actual );
	}
}
