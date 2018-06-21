<?php
/**
 * Tests for beans_field_label()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldLabel
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldLabel extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/field.php';
	}

	/**
	 * Test beans_field_label() should not render the field's label when none is given.
	 */
	public function test_should_not_render_field_label_when_none_given() {
		$field = $this->merge_field_with_default( [
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'default' => '',
		] );

		$this->assertNull( beans_field_label( $field ) );

		$field['label'] = '';
		$this->assertNull( beans_field_label( $field ) );
	}

	/**
	 * Test beans_field_label() should render the field's label.
	 */
	public function test_should_render_field_label() {
		$field = $this->merge_field_with_default( [
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'label'   => 'Testing the text field.',
			'default' => '',
		] );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<label for="beans_text_test">Testing the text field.</label>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_label() should not render the radio field's group label.
	 */
	public function test_should_not_render_radio_group_label() {
		$field = $this->merge_field_with_default( [
			'id'          => 'beans_layout',
			'label'       => 'Layout',
			'description' => 'The layout settings.',
			'type'        => 'radio',
			'default'     => 'default_fallback',
			'options'     => [
				'default_fallback' => 'Use Default Layout',
				'c'                => [
					'src'                => 'http://example.com/images/layouts/c.png',
					'alt'                => 'Content Only Layout',
					'screen_reader_text' => 'Option for the Content Only Layout',
				],
				'c_sp'             => [
					'src'                => 'http://example.com/images/layouts/c_sp.png',
					'screen_reader_text' => 'Option for the Content + Sidebar Primary Layout',
				],
				'sp_c'             => [
					'src' => 'http://example.com/images/layouts/sp_c.png',
					'alt' => 'Sidebar Primary + Content Layout',
				],
			],
		] );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		// Run the test.
		$this->assertEmpty( $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_label() should not render the group field's label.
	 */
	public function test_should_not_render_group_label() {
		$field = [
			'id'          => 'beans_group_test',
			'label'       => 'Group of fields',
			'description' => 'This is a group of fields.',
			'type'        => 'group',
			'fields'      => [
				[
					'id'      => 'beans_group_activation_test',
					'label'   => 'Activate',
					'type'    => 'activation',
					'default' => false,
				],
				[
					'id'         => 'beans_group_select_test',
					'label'      => 'Select',
					'type'       => 'select',
					'default'    => 'aggressive',
					'attributes' => [ 'style' => 'margin: -3px 0 0 -8px;' ],
					'options'    => [
						'aggressive' => 'Aggressive',
						'standard'   => 'Standard',
					],
				],
				[
					'id'             => 'beans_group_checkbox_test',
					'label'          => false,
					'checkbox_label' => 'Enable the checkbox test',
					'type'           => 'checkbox',
					'default'        => false,
				],
			],
		];

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		$this->assertEmpty( $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_label() should not render the activation field's label.
	 */
	public function test_should_not_render_activation_field_label() {
		$field = [
			'id'      => 'beans_group_activation_test',
			'label'   => 'Activate',
			'type'    => 'activation',
			'default' => false,
		];

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		$this->assertEmpty( $this->format_the_html( $html ) );
	}
}
