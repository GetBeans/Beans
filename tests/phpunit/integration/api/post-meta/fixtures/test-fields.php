<?php
/**
 *  Test field data for use with metabox_content method tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta\Fixtures
 *
 * @since 1.5.0
 */

return array(
	'fields'  => array(
		array(
			'id'      => 'beans_layout_test',
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
		array(
			'id'             => 'beans_checkbox_test',
			'label'          => false,
			'checkbox_label' => 'Enable the checkbox test',
			'type'           => 'checkbox',
			'default'        => false,
		),
		array(
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'default' => 'Testing the text field.',
		),
	),
	'context' => 'tests',
	'section' => 'tm-beans',
);
