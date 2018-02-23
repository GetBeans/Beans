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
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
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
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_label', 'beans_field_label' );
		beans_remove_action( 'beans_field_description', 'beans_field_description' );
	}

	/**
	 * Test beans_field_label() should not render the field's label when none is given.
	 */
	public function test_should_not_render_field_label_when_none_given() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'default' => '',
		) );

		$this->assertNull( beans_field_label( $field ) );

		$field['label'] = '';
		$this->assertNull( beans_field_label( $field ) );
	}

	/**
	 * Test beans_field_label() should render the field's label.
	 */
	public function test_should_render_field_label() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_text_test',
			'type'    => 'text',
			'label'   => 'Testing the text field.',
			'default' => '',
		) );

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
	 * Test beans_field_label() should render the radio field's group header.
	 */
	public function test_should_render_radio_group_header() {
		$field = $this->merge_field_with_default( array(
			'id'          => 'beans_layout',
			'label'       => 'Layout',
			'description' => 'The layout settings.',
			'type'        => 'radio',
			'default'     => 'default_fallback',
			'options'     => array(
				'default_fallback' => 'Use Default Layout',
				'c'                => array(
					'src'                => 'http://example.com/images/layouts/c.png',
					'alt'                => 'Content Only Layout',
					'screen_reader_text' => 'Option for the Content Only Layout',
				),
				'c_sp'             => array(
					'src'                => 'http://example.com/images/layouts/c_sp.png',
					'screen_reader_text' => 'Option for the Content + Sidebar Primary Layout',
				),
				'sp_c'             => array(
					'src' => 'http://example.com/images/layouts/sp_c.png',
					'alt' => 'Sidebar Primary + Content Layout',
				),
			),
		) );

		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<h3 class="bs-fields-header">Layout</h3>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_label() should render the radio field's group header.
	 */
	public function test_should_render_group_header() {
		$field = array(
			'id'          => 'beans_group_test',
			'label'       => 'Group of fields',
			'description' => 'This is a group of fields.',
			'type'        => 'group',
			'fields'      => array(
				array(
					'id'      => 'beans_group_activation_test',
					'label'   => 'Activate',
					'type'    => 'activation',
					'default' => false,
				),
				array(
					'id'         => 'beans_group_select_test',
					'label'      => 'Select',
					'type'       => 'select',
					'default'    => 'aggressive',
					'attributes' => array( 'style' => 'margin: -3px 0 0 -8px;' ),
					'options'    => array(
						'aggressive' => 'Aggressive',
						'standard'   => 'Standard',
					),
				),
				array(
					'id'             => 'beans_group_checkbox_test',
					'label'          => false,
					'checkbox_label' => 'Enable the checkbox test',
					'type'           => 'checkbox',
					'default'        => false,
				),
			),
		);

		ob_start();
		beans_field_label( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<h3 class="bs-fields-header">Group of fields</h3>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
