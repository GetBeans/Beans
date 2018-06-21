<?php
/**
 * Array of test data for the Beans Options API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Options\Fixtures
 *
 * @since   1.5.0
 */

return [
	// Compiler Options.
	[
		'fields'    => [
			[
				'id'          => 'beans_compiler_items',
				'type'        => 'flush_cache',
				'description' => 'Clear CSS and Javascript cached files. New cached versions will be compiled on page load.',
			],
			[
				'id'             => 'beans_compile_all_styles',
				'label'          => false,
				'checkbox_label' => 'Compile all WordPress styles',
				'type'           => 'checkbox',
				'default'        => false,
				'description'    => 'Compile and cache all the CSS files that have been enqueued to the WordPress head.',
			],
			[
				'id'          => 'beans_compile_all_scripts_group',
				'label'       => 'Compile all WordPress scripts',
				'type'        => 'group',
				'fields'      => [
					[
						'id'      => 'beans_compile_all_scripts',
						'type'    => 'activation',
						'default' => false,
					],
					[
						'id'         => 'beans_compile_all_scripts_mode',
						'type'       => 'select',
						'default'    => 'aggressive',
						'attributes' => [ 'style' => 'margin: -3px 0 0 -8px;' ],
						'options'    => [
							'aggressive' => 'Aggressive',
							'standard'   => 'Standard',
						],
					],
				],
				'description' => 'Compile and cache all the Javascript files that have been enqueued to the WordPress head.<!--more-->JavaSript is outputted in the footer if the level is set to <strong>Aggressive</strong> and might conflict with some third party plugins which are not following WordPress standards.',
			],
		],
		'menu_slug' => 'beans_settings',
		'section'   => 'compiler_options',
		'args'      => [
			'title'   => 'Compiler options',
			'context' => 'normal',
		],
	],
	// Images Options.
	[
		'fields'    => [
			[
				'id'          => 'beans_edited_images_directories',
				'type'        => 'flush_edited_images',
				'description' => 'Clear all edited images. New images will be created on page load.',
			],
		],
		'menu_slug' => 'beans_settings',
		'section'   => 'images_options',
		'args'      => [
			'title'   => 'Images options',
			'context' => 'column',
		],
	],
	// Mode Options.
	[
		'fields'    => [
			[
				'id'             => 'beans_dev_mode',
				'checkbox_label' => 'Enable development mode',
				'type'           => 'checkbox',
				'description'    => 'This option should be enabled while your website is in development.',
			],
		],
		'menu_slug' => 'beans_settings',
		'section'   => 'mode_options',
		'args'      => [
			'title'   => 'Mode options',
			'context' => 'column',
		],
	],
];
