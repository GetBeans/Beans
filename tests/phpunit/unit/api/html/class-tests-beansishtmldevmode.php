<?php
/**
 * Tests for _beans_is_html_dev_mode()
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
 * Class Tests_BeansIsHtmlDevMode
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansIsHtmlDevMode extends HTML_Test_Case {

	/**
	 * Test _beans_is_html_dev_mode() should return false when the option does not exist.
	 */
	public function test_should_return_false_when_option_does_not_exist() {
		$this->assertFalse( defined( 'BEANS_HTML_DEV_MODE' ) );

		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_dev_mode', false )
			->once()
			->andReturn( false );

		$this->assertFalse( _beans_is_html_dev_mode() );
	}

	/**
	 * Test _beans_is_html_dev_mode() should return the option's value.
	 */
	public function test_should_return_option_value() {
		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_dev_mode', false )
			->once()
			->andReturn( 0 );

		$this->assertFalse( _beans_is_html_dev_mode() );

		Monkey\Functions\expect( 'get_option' )
			->with( 'beans_dev_mode', false )
			->once()
			->andReturn( 1 );

		$this->assertTrue( _beans_is_html_dev_mode() );
	}

	/**
	 * Test _beans_is_html_dev_mode() should return the constant's value.
	 */
	public function test_should_return_constant_value() {
		Monkey\Functions\expect( 'get_option' )->never();

		define( 'BEANS_HTML_DEV_MODE', 0 );
		$this->assertFalse( _beans_is_html_dev_mode() );
	}
}
