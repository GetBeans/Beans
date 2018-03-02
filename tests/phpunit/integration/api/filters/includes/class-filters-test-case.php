<?php
/**
 * Test Case for Beans' Filter API unit tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Filters\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Filters\Includes;

use WP_UnitTestCase;

/**
 * Abstract Class Filters_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Filters\Includes
 */
abstract class Filters_Test_Case extends WP_UnitTestCase {

	/**
	 * An array of filters to test.
	 *
	 * @var array
	 */
	protected static $test_filters;

	/**
	 * Setup the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		require_once dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'stubs/functions.php';

		static::$test_filters = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-filters.php';
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
		$post_id = self::factory()->post->create( array( 'post_title' => 'Hello Beans' ) );
		$this->go_to( get_permalink( $post_id ) );
		do_action( 'template_redirect' ); // @codingStandardsIgnoreLine
	}
}
