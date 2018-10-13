<?php
/**
 * Tests for beans_modify_markup().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Fixtures\Anonymous_Filter_Stub;
use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansModifyMarkup
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansModifyMarkup extends HTML_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once __DIR__ . '/fixtures/class-anonymous-filter-stub.php';
	}

	/**
	 * Test beans_modify_markup() should return an anonymous filter instance.
	 */
	public function test_should_return_anonymous_filter_instance() {
		Monkey\Functions\when( 'beans_add_filter' )->alias(
			function( $hook, $value ) {
				return new Anonymous_Filter_Stub( $hook, $value, 10 );
			}
		);

		$this->assertInstanceOf( Anonymous_Filter_Stub::class, beans_modify_markup( 'beans_archive_title', 'h2' ) );
	}

	/**
	 * Test beans_modify_markup() should call beans_add_filter() to register the callback for the modification process.
	 */
	public function test_should_call_beans_add_filter_to_register_callback() {
		Monkey\Functions\expect( 'beans_add_filter' )
			->once()
			->with( 'beans_archive_title_markup', 'h2', 10, 1 );
		beans_modify_markup( 'beans_archive_title', 'h2' );

		// Placeholder for PHPUnit, as it requires an assertion. The real test is the "expect" above.
		$this->assertTrue( true );
	}
}
