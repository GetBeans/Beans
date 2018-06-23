<?php
/**
 *  Test field data for use with the render_fields() method tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta\Fixtures
 *
 * @since 1.5.0
 */

return [
	'fields' => [
		[
			'id'      => 'beans_layout_test',
			'label'   => 'Layout',
			'type'    => 'radio',
			'default' => 'default_fallback',
			'options' => [
				'default_fallback' => 'Use Default Layout',
				'c'                => BEANS_THEME_URL . 'lib/admin/assets/images/layouts/c.png',
				'c_sp'             => BEANS_THEME_URL . 'lib/admin/assets/images/layouts/c_sp.png',
				'sp_c'             => BEANS_THEME_URL . 'lib/admin/assets/images/layouts/sp_c.png',
			],
		],
		[
			'id'             => 'beans_checkbox_test',
			'label'          => 'Checkbox Test Field Label',
			'checkbox_label' => 'Enable the checkbox test',
			'type'           => 'checkbox',
			'default'        => false,
		],
		[
			'id'          => 'beans_text_test',
			'description' => 'Sample Text Field Description',
			'type'        => 'text',
			'default'     => 'Testing the text field.',
		],
	],
];
