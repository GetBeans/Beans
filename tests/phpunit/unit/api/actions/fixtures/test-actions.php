<?php
/**
 * Array of test actions, which is used to test Beans Actions API against the original action configurations.
 *
 * @package Beans\Framework\Tests\Integration\API\Actions\Fixtures
 *
 * @since   1.5.0
 */

return array(
	'beans_post_meta'          => array(
		'hook'     => 'beans_post_header',
		'callback' => 'beans_post_meta',
		'priority' => 15,
		'args'     => 1,
	),
	'beans_post_image'         => array(
		'hook'     => 'beans_post_body',
		'callback' => 'beans_post_image',
		'priority' => 5,
		'args'     => 1,
	),
	'beans_previous_post_link' => array(
		'hook'     => 'previous_post_link',
		'callback' => 'beans_previous_post_link',
		'priority' => 10,
		'args'     => 4,
	),
);
