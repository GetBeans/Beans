<?php
/**
 * Tests for beans_remove_output().
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
 * Class Tests_BeansRemoveOutput
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansRemoveOutput extends HTML_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once __DIR__ . '/fixtures/class-anonymous-filter-stub.php';
	}

	/**
	 * Test beans_remove_output() should return an anonymous filter instance.
	 */
	public function test_should_return_anonymous_filter_instance() {
		Monkey\Functions\when( 'beans_add_filter' )->alias( function( $hook, $value, $priority ) {
			return new Anonymous_Filter_Stub( $hook, $value, $priority );
		} );

		$this->assertInstanceOf( Anonymous_Filter_Stub::class, beans_remove_output( 'beans_post_meta_item_date' ) );
	}

	/**
	 * Test beans_remove_output() should call beans_add_filter() to register the callback for the remove process.
	 */
	public function test_should_call_beans_add_filter_to_register_callback() {
		$ids = array(
			'beans_post_meta_item_date',
			'beans_post_meta_item_author',
			'beans_post_meta_item_comments',
		);

		foreach ( $ids as $id ) {
			Monkey\Functions\expect( 'beans_add_filter' )
				->once()
				->with( "{$id}_output", false, 99999999 )
				->andReturn( false );

			beans_remove_output( $id );
		}

		// Tests are focused above on ensuring beans_apply_filters() is called with the right arguments.
		$this->assertTrue( true );
	}
}
