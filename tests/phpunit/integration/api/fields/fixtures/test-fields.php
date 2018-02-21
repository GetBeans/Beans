<?php
/**
 * Test data for the Fields unit tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Fixtures
 *
 * @since   1.5.0
 */

return array(
	array(
		'fields'  => array(
			array(
				'id'      => 'beans_layout',
				'label'   => 'Layout',
				'type'    => 'radio',
				'default' => 'default_fallback',
				'options' => array(
					'default_fallback' => 'Use Default Layout',
					'c'                => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/c.png',
					'c_sp'             => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/c_sp.png',
					'sp_c'             => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/sp_c.png',
				),
			),
		),
		'context' => 'post_meta',
		'section' => 'tm-beans',
	),
);
