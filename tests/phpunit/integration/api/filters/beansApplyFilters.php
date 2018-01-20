<?php
/**
 * Tests for beans_apply_filters()
 *
 * @package Beans\Framework\Tests\Integration\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Filters;

use Beans\Framework\Tests\Integration\API\Filters\Includes\Filters_Test_Case;

require_once __DIR__ . '/includes/class-filters-test-case.php';

/**
 * Class Tests_BeansApplyFilters
 *
 * @package Beans\Framework\Tests\Integration\API\Filters
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansApplyFilters extends Filters_Test_Case {

	/**
	 * Test beans_apply_filters() should return value after calling the hook with no sub-hook.
	 */
	public function test_should_return_value_after_calling_hook_no_subhook() {
		$filters = array(
			'beans_field_description_markup',
			'beans_widget_content_categories_output',
		);

		foreach ( $filters as $filter ) {
			$this->assertSame( 'foo', beans_apply_filters( $filter, 'foo' ) );
		}
	}

	/**
	 * Test beans_apply_filters() should return value after calling one level of sub-hooks.
	 */
	public function test_should_return_value_after_calling_one_level_of_sub_hooks() {
		add_filter( 'beans_loop_query_args', 'beans_loop_query_args_base' );
		add_filter( 'beans_loop_query_args[_main]', 'beans_loop_query_args_main' );

		$this->assertSame( array( 'base', '_main' ), beans_apply_filters( 'beans_loop_query_args[_main]', 'foo' ) );
	}

	/**
	 * Test beans_apply_filters() should return value after calling two levels of sub-hooks.
	 */
	public function test_should_return_value_after_calling_two_levels_of_sub_hooks() {
		add_filter( 'beans_loop_query_args', 'beans_loop_query_args_base' );
		add_filter( 'beans_loop_query_args[_main]', 'beans_loop_query_args_main' );
		add_filter( 'beans_loop_query_args[_second]', function( $args ) {
			$args[] = '_second';
			return $args;
		} );
		add_filter( 'beans_loop_query_args[_main][_second]', function( $args ) {
			$args[] = '[_main][_second]';
			return $args;
		} );

		$this->assertSame(
			array( 'base', '_main', '_second', '[_main][_second]' ),
			beans_apply_filters( 'beans_loop_query_args[_main][_second]', 'foo' )
		);
	}

	/**
	 * Test beans_apply_filters() should return value after calling three levels of sub-hooks.
	 */
	public function test_should_return_value_after_calling_three_levels_of_sub_hooks() {
		add_filter( 'beans_loop_query_args', 'beans_loop_query_args_base' );
		add_filter( 'beans_loop_query_args[_main]', 'beans_loop_query_args_main' );
		add_filter( 'beans_loop_query_args[_second]', function( $args ) {
			$args[] = '_second';
			return $args;
		} );
		add_filter( 'beans_loop_query_args[_main][_second]', function( $args ) {
			$args[] = '[_main][_second]';
			return $args;
		} );
		add_filter( 'beans_loop_query_args[_third]', function( $args ) {
			$args[] = '_third';
			return $args;
		} );
		add_filter( 'beans_loop_query_args[_main][_second][_third]', function( $args ) {
			$args[] = '[_main][_second][_third]';
			return $args;
		} );

		$this->assertSame(
			array( 'base', '_main', '_second', '[_main][_second]', '_third', '[_main][_second][_third]' ),
			beans_apply_filters( 'beans_loop_query_args[_main][_second][_third]', 'foo' )
		);
	}
}
