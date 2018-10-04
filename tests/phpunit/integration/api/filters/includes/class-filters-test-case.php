<?php
/**
 * Test Case for Beans' Filter API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Filters\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Filters\Includes;

use Brain\Monkey;
use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Filters_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Filters\Includes
 */
abstract class Filters_Test_Case extends Test_Case {

	/**
	 * An array of filters to test.
	 *
	 * @var array
	 */
	protected static $test_filters;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_filters = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-filters.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->mock_filter_callbacks();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {

		foreach ( static::$test_filters as $beans_id => $filter ) {

			if ( ! isset( $filter['callback'] ) ) {
				continue;
			}

			remove_filter( $filter['hook'], $filter['callback'], $filter['priority'] );
		}

		parent::tearDown();
	}

	/**
	 * Check that the right parameters are registered in WordPress.
	 *
	 * @since 1.5.0
	 *
	 * @param array $filter        The filter that should be registered.
	 * @param bool  $remove_filter When true, it removes the filter automatically to clean up this test.
	 *
	 * @return void
	 */
	protected function check_parameters_registered_in_wp( array $filter, $remove_filter = true ) {
		global $wp_filter;
		$registered_filter = $wp_filter[ $filter['hook'] ]->callbacks[ $filter['priority'] ];

		$this->assertArrayHasKey( $filter['callback'], $registered_filter );
		$this->assertEquals( $filter['callback'], $registered_filter[ $filter['callback'] ]['function'] );
		$this->assertEquals( $filter['args'], $registered_filter[ $filter['callback'] ]['accepted_args'] );

		// Then remove the filter.
		if ( $remove_filter ) {
			remove_filter( $filter['hook'], $filter['callback'], $filter['priority'] );
		}
	}

	/**
	 * Create a post, load it, and force the "template redirect" to fire.
	 */
	protected function go_to_post() {
		$post_id = self::factory()->post->create( [ 'post_title' => 'Hello Beans' ] );
		$this->go_to( get_permalink( $post_id ) );
		do_action( 'template_redirect' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Valid use case as we need to fire this action as part of our tests.
	}

	/**
	 * Define the mocks for the filter callbacks.
	 */
	protected function mock_filter_callbacks() {
		Monkey\Functions\when( 'beans_test_the_content' )->alias(
			function ( $post_title, $post_id ) {
					return $post_title . '_' . $post_id;
			}
		);
		Monkey\Functions\when( 'beans_test_modify_widget_count' )->justReturn( 20 );
		Monkey\Functions\when( 'beans_test_query_args_base' )->justReturn( [ 'base' ] );
		Monkey\Functions\when( 'beans_test_query_args_main' )->alias(
			function ( $args ) {
					$args[] = '_main';
					return $args;
			}
		);
	}
}
