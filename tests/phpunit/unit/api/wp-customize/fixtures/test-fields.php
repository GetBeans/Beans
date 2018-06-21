<?php
/**
 * Test data for the Beans WP Customize API unit tests.
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize\Fixtures
 *
 * @since   1.5.0
 */

return [
	// Single fields.
	'single_fields' => [
		'fields'  => [
			'name'  => 'beans-test',
			'type'  => 'radio',
			'label' => 'Layout',
			[
				'id'      => 'beans_customizer_layout',
				'name'    => 'beans-test',
				'label'   => 'Layout',
				'type'    => 'radio',
				'default' => 'default_fallback',
				'options' => [
					'default_fallback' => 'Use Default Layout',
					'c'                => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/c.png',
					'c_sp'             => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/c_sp.png',
					'sp_c'             => BEANS_THEME_DIR . 'lib/admin/assets/images/layouts/sp_c.png',
				],
			],
			[
				'id'             => 'beans_customizer_checkbox',
				'name'           => 'beans-test',
				'label'          => false,
				'checkbox_label' => 'Enable the checkbox test',
				'type'           => 'checkbox',
				'default'        => false,
			],
			[
				'id'      => 'beans_customizer_text',
				'name'    => 'beans-test',
				'type'    => 'text',
				'default' => 'Testing the text field.',
			],
		],
		'context' => 'beans-test',
		'section' => 'tm-beans-customizer',
		'args'    => [
			'title'       => 'Beans Customizer Section',
			'priority'    => 250,
			'description' => 'Customizer Beans Section',
		],
	],

	// Group of fields.
	'group'         => [
		'fields'  => [
			'name'   => 'beans-test',
			'type'   => 'group',
			'fields' => [
				[
					'id'      => 'beans_compile_all_scripts',
					'name'    => 'beans-test',
					'type'    => 'activation',
					'default' => false,
				],
				[
					'id'         => 'beans_compile_all_scripts_mode',
					'name'       => 'beans-test',
					'type'       => 'select',
					'default'    => 'aggressive',
					'attributes' => [ 'style' => 'margin: -3px 0 0 -8px;' ],
					'options'    => [
						'aggressive' => 'Aggressive',
						'standard'   => 'Standard',
					],
				],
				[
					'id'             => 'beans_checkbox_test',
					'name'           => 'beans-test',
					'label'          => false,
					'checkbox_label' => 'Enable the checkbox test',
					'type'           => 'checkbox',
					'default'        => false,
				],
			],
		],
		'context' => 'group_tests',
		'section' => 'tm-beans-customizer',
		'name'    => 'beans-test',
		'args'    => [
			'title'       => 'Beans Customizer Section',
			'priority'    => 250,
			'description' => 'Customizer Beans Section',
		],
	],
];
