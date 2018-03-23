<?php
/**
 * Tests for beans_selfclose_markup_e().
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
 * Class Tests_BeansSelfcloseMarkupE
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansSelfcloseMarkupE extends HTML_Test_Case {

	/**
	 * Test beans_selfclose_markup_e() should echo an empty string when the tag is set to null.
	 */
	public function test_should_echo_empty_string_when_tag_set_to_null() {

		foreach ( static::$test_attachments as $attachment ) {
			ob_start();
			beans_selfclose_markup_e( $attachment['id'], null, $attachment['attributes'], $attachment['attachment'] );
			$this->assertSame( '', ob_get_clean() );
		}
	}

	/**
	 * Test beans_selfclose_markup_e() should echo the built HTML self-closing element.
	 */
	public function test_should_echo_built_html_self_closing_element() {
		// Check the first attachment.
		$attachment = current( static::$test_attachments );
		$expected   = <<<EOB
<source media="(max-width: 200px)" srcset="https://example.com/small-image.png"/>
EOB;
		ob_start();
		beans_selfclose_markup_e( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] );
		$this->assertSame( $expected, ob_get_clean() );

		// Check the next one.
		$attachment = next( static::$test_attachments );
		$expected   = <<<EOB
<img width="1200" height="600" src="https://example.com/image.png" alt="A background image." itemprop="image"/>
EOB;
		ob_start();
		beans_selfclose_markup_e( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] );
		$this->assertSame( $expected, ob_get_clean() );
	}
}
