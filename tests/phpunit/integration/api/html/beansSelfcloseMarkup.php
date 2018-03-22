<?php
/**
 * Tests for beans_selfclose_markup().
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
 * Class Tests_BeansSelfcloseMarkup
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansSelfcloseMarkup extends HTML_Test_Case {

	/**
	 * Test beans_selfclose_markup() should unset the temporary global when the tag is null.
	 */
	public function test_should_unset_temporary_global_when_tag_is_null() {

		foreach ( static::$test_attachments as $attachment ) {
			// Check before we start.
			$this->assertArrayNotHasKey( '_beans_is_selfclose_markup', $GLOBALS );

			beans_selfclose_markup( $attachment['id'], null, $attachment['attributes'], $attachment['attachment'] );

			// Check after we run the function.
			$this->assertArrayNotHasKey( '_beans_is_selfclose_markup', $GLOBALS );
		}
	}

	/**
	 * Test beans_selfclose_markup() should return null when the tag is set to null.
	 */
	public function test_should_return_null_when_tag_set_to_null() {

		foreach ( static::$test_attachments as $attachment ) {
			$this->assertNull( beans_selfclose_markup( $attachment['id'], null, $attachment['attributes'], $attachment['attachment'] ) );
		}
	}

	/**
	 * Test beans_selfclose_markup() should return the built HTML self-closing element.
	 */
	public function test_should_return_built_html_self_closing_element() {
		// Check the first attachment.
		$attachment = current( static::$test_attachments );
		$expected   = <<<EOB
<source media="(max-width: 200px)" srcset="https://example.com/small-image.png"/>
EOB;
		$this->assertSame( $expected, beans_selfclose_markup( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] ) );

		// Check the next one.
		$attachment = next( static::$test_attachments );
		$expected   = <<<EOB
<img width="1200" height="600" src="https://example.com/image.png" alt="A background image." itemprop="image"/>
EOB;
		$this->assertSame( $expected, beans_selfclose_markup( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] ) );
	}
}
