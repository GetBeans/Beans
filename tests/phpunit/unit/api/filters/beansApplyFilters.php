<?php
/**
 * Tests for beans_apply_filters()
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Filters;

use Beans\Framework\Tests\Unit\API\Filters\Includes\Filters_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-filters-test-case.php';

/**
 * Class Tests_BeansApplyFilters
 *
 * @package Beans\Framework\Tests\Unit\API\Filters
 * @group   unit-tests
 * @group   api
 */
class Tests_BeansApplyFilters extends Filters_Test_Case {

	/**
	 * Test beans_apply_filters() should return value after calling the hook with no sub-hook.
	 */
	public function test_should_return_value_after_calling_hook_no_subhook() {
		$filters = array(
			'beans_field_description_markup',
			'the_content',
			'post_title',
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
		$filters = array(
			'beans_loop_query_args[_main]'              => array(
				'beans_loop_query_args',
				'beans_loop_query_args[_main]',
			),
			'beans_loop_query_args[_foo]'               => array(
				'beans_loop_query_args',
				'beans_loop_query_args[_foo]',
			),
			'beans_widgets_area_args[_sidebar_primary]' => array(
				'beans_widgets_area_args',
				'beans_widgets_area_args[_sidebar_primary]',
			),
			'beans_widgets_area_args[_offcanvas_menu]'  => array(
				'beans_widgets_area_args',
				'beans_widgets_area_args[_offcanvas_menu]',
			),
		);

		foreach ( $filters as $filter => $events ) {

			// Set up the WordPress simulator for each of the filter events that will fire.
			foreach ( $events as $index => $event_name ) {
				Monkey\Filters\expectApplied( $events[ $index ] )
					->once()
					->with( 0 === $index ? 'foo' : $events[ $index - 1 ] )
					->andReturn( $events[ $index ] );
			}

			$this->assertSame( end( $events ), beans_apply_filters( $filter, 'foo' ) );
		}
	}

	/**
	 * Test beans_apply_filters() should return value after calling two levels of sub-hooks.
	 */
	public function test_should_return_value_after_calling_two_levels_of_sub_hooks() {
		$filters = array(
			'beans_loop_query_args[_first][_second]'   => array(
				'beans_loop_query_args',
				'beans_loop_query_args[_first]',
				'beans_loop_query_args[_second]',
				'beans_loop_query_args[_first][_second]',
			),
			'beans_widgets_area_args[_first][_second]' => array(
				'beans_widgets_area_args',
				'beans_widgets_area_args[_first]',
				'beans_widgets_area_args[_second]',
				'beans_widgets_area_args[_first][_second]',
			),
		);

		foreach ( $filters as $filter => $events ) {

			// Set up the WordPress simulator for each of the filter events that will fire.
			foreach ( $events as $index => $event_name ) {
				Monkey\Filters\expectApplied( $events[ $index ] )
					->once()
					->with( 0 === $index ? 'bar' : $events[ $index - 1 ] )
					->andReturn( $events[ $index ] );
			}

			$this->assertSame( end( $events ), beans_apply_filters( $filter, 'bar' ) );
		}
	}

	/**
	 * Test beans_apply_filters() should return value after calling three levels of sub-hooks.
	 */
	public function test_should_return_value_after_calling_three_levels_of_sub_hooks() {
		$filters = array(
			'beans_loop_query_args[_first][_second][_third]'   => array(
				'beans_loop_query_args',
				'beans_loop_query_args[_first]',
				'beans_loop_query_args[_second]',
				'beans_loop_query_args[_first][_second]',
				'beans_loop_query_args[_third]',
				'beans_loop_query_args[_first][_second][_third]',
			),
			'beans_widgets_area_args[_first][_second][_third]' => array(
				'beans_widgets_area_args',
				'beans_widgets_area_args[_first]',
				'beans_widgets_area_args[_second]',
				'beans_widgets_area_args[_first][_second]',
				'beans_widgets_area_args[_third]',
				'beans_widgets_area_args[_first][_second][_third]',
			),
		);

		foreach ( $filters as $filter => $events ) {

			// Set up the WordPress simulator for each of the filter events that will fire.
			foreach ( $events as $index => $event_name ) {
				Monkey\Filters\expectApplied( $events[ $index ] )
					->once()
					->with( 0 === $index ? 'beans' : $events[ $index - 1 ] )
					->andReturn( $events[ $index ] );
			}

			$this->assertSame( end( $events ), beans_apply_filters( $filter, 'beans' ) );
		}
	}
}
