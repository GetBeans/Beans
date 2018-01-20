<?php
/**
 * Array of test filters, which is used to test Beans Actions API against the original filter configurations.
 *
 * @package Beans\Framework\Tests\Integration\API\Filters\Fixtures
 *
 * @since   1.5.0
 */

return array(
	'beans_field_description_markup'         => array(
		'hook'            => 'beans_field_description_markup',
		'value_to_return' => 'p',
		'priority'        => 10,
		'args'            => 1,
	),
	'beans_widget_content_categories_output' => array(
		'hook'     => 'beans_widget_content_categories_output',
		'callback' => 'beans_modify_widget_count',
		'priority' => 10,
		'args'     => 1,
	),
	'beans_loop_query_args'                  => array(
		'hook'     => 'beans_loop_query_args',
		'callback' => 'beans_loop_query_args_base',
		'priority' => 20,
		'args'     => 1,
	),
	'beans_loop_query_args[_main]'           => array(
		'hook'     => 'beans_loop_query_args[_main]',
		'callback' => 'beans_loop_query_args_main',
		'priority' => 20,
		'args'     => 1,
	),
	'the_content'                            => array(
		'hook'     => 'the_content',
		'callback' => 'beans_test_the_content',
		'priority' => 20,
		'args'     => 2,
	),
);
