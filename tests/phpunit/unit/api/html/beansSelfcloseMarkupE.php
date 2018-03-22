<?php
/**
 * Tests for beans_selfclose_markup_e().
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
 * Class Tests_BeansSelfcloseMarkupE
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansSelfcloseMarkupE extends HTML_Test_Case {

	/**
	 * Test beans_selfclose_markup_e() should invoke beans_selfclose_markup().
	 */
	public function test_should_invoke_beans_open_markup() {

		foreach ( static::$test_attachments as $attachment ) {
			Monkey\Functions\expect( 'beans_selfclose_markup' )
				->once()
				->with( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] )
				->andReturnNull();

			ob_start();
			beans_selfclose_markup_e( $attachment['id'], $attachment['tag'], $attachment['attributes'], $attachment['attachment'] );
			ob_get_clean();
		}

		// The assertions are above. This assertion is a placeholder, as PHPUnit requires a specific assert to run.
		$this->assertTrue( true );
	}
}
