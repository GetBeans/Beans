<?php
/**
 * Tests for beans_selfclose_markup().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansSelfcloseMarkup
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansSelfcloseMarkup extends HTML_Test_Case {

	/**
	 * Test beans_selfclose_markup() should unset the temporary global when the tag is null.
	 */
	public function test_should_unset_temporary_global_when_tag_is_null() {
		Monkey\Functions\when( 'beans_open_markup' )->justReturn();

		foreach ( static::$test_attachments as $attachment ) {
			// Check before we start.
			$this->assertArrayNotHasKey( '_beans_is_selfclose_markup', $GLOBALS );

			beans_selfclose_markup( $attachment['id'], null, $attachment['attributes'], $attachment['attachment'] );

			// Check after we run the function.
			$this->assertArrayNotHasKey( '_beans_is_selfclose_markup', $GLOBALS );
		}
	}

	/**
	 * Test beans_selfclose_markup() should invoke beans_open_markup().
	 */
	public function test_should_invoke_beans_open_markup() {

		foreach ( static::$test_attachments as $attachment ) {
			Monkey\Functions\expect( 'beans_open_markup' )
				->once()
				->with( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] )
				->andReturnNull();

			beans_selfclose_markup( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] );
		}

		// Placeholder for PHPUnit, as it requires an assertion. The real test is the "expect" above.
		$this->assertTrue( true );
	}
}
