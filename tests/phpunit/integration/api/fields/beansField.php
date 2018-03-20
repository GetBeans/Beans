<?php
/**
 * Tests for beans_field()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once __DIR__ . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansField
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   api
 * @group   api-fields
 */
class Tests_BeansField extends Fields_Test_Case {

	/**
	 * The test field.
	 *
	 * @var array
	 */
	protected $field;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/field.php';

		beans_add_smart_action( 'beans_field_group_label', 'beans_field_label' );
		beans_add_smart_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_group_label', 'beans_field_label' );
		beans_remove_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
	}

	/**
	 * Test beans_field() should render the checkbox field.
	 */
	public function test_should_render_checkbox_field() {
		// Set up the test.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/checkbox.php';
		beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		$field = $this->merge_field_with_default( array(
			'id'             => 'beans_compile_all_styles',
			'label'          => false,
			'checkbox_label' => 'Compile all WordPress styles',
			'type'           => 'checkbox',
			'default'        => false,
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-checkbox beans_tests">
	<div class="bs-field-inside">
		<div class="bs-field bs-checkbox">
			<input type="hidden" value="0" name="beans_fields[beans_compile_all_styles]" />
			<input id="beans_compile_all_styles" type="checkbox" name="beans_fields[beans_compile_all_styles]" value="1" />
			<span class="bs-checkbox-label">Compile all WordPress styles</span>
		</div>
	</div>
</div>
EOB;
		// Run the tests.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
		$this->assertEquals( 1, did_action( 'beans_field_checkbox' ) );
		$this->assertEquals( 0, did_action( 'beans_field_group_label' ) );

		// Clean up.
		beans_remove_action( 'beans_field_checkbox', 'beans_field_checkbox' );
	}

	/**
	 * Test beans_field() should render the radio field.
	 */
	public function test_should_render_radio_field() {
		// Set up the test.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/radio.php';
		beans_add_smart_action( 'beans_field_radio', 'beans_field_radio' );
		$field = $this->merge_field_with_default( array(
			'id'          => 'beans_radio_test',
			'label'       => 'Having fun?',
			'description' => 'Radio buttons',
			'type'        => 'radio',
			'default'     => 'no',
			'options'     => array(
				'no'  => 'No',
				'yes' => 'Yes',
			),
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-radio beans_tests">
	<div class="bs-field-inside">
		<div class="bs-field bs-radio">
			<fieldset class="bs-field-fieldset">
				<legend class="bs-field-legend">Having fun?</legend>
					<label class="" for="beans_radio_test_no">
					  <input id="beans_radio_test_no" type="radio" name="beans_fields[beans_radio_test]" value="no" checked='checked' /> No
			    	</label>
					<label class="" for="beans_radio_test_yes">
					  <input id="beans_radio_test_yes" type="radio" name="beans_fields[beans_radio_test]" value="yes" /> Yes
			    	</label>
			</fieldset>
		</div>
	</div>
	<div class="bs-field-description">Radio buttons</div>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
		$this->assertEquals( 1, did_action( 'beans_field_radio' ) );
		$this->assertEquals( 0, did_action( 'beans_field_group_label' ) );

		// Clean up.
		beans_remove_action( 'beans_field_radio', 'beans_field_radio' );
	}

	/**
	 * Test beans_field() should render a group of fields.
	 */
	public function test_should_render_group_of_fields() {
		// Set up the test.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/activation.php';
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/select.php';
		beans_add_smart_action( 'beans_field_activation', 'beans_field_activation' );
		beans_add_smart_action( 'beans_field_select', 'beans_field_select' );

		// Prepare the group of fields.
		$group = $this->merge_field_with_default( array(
			'id'          => 'beans_group_test',
			'label'       => 'Group of fields',
			'description' => 'This is a group of fields.',
			'type'        => 'group',
			'context'     => 'beans_group_tests',
			'fields'      => array(
				array(
					'id'      => 'beans_group_activation_test',
					'label'   => 'Activate Foo',
					'type'    => 'activation',
					'default' => false,
				),
				array(
					'id'         => 'beans_group_select_test',
					'label'      => 'Select Foo',
					'type'       => 'select',
					'default'    => 'aggressive',
					'attributes' => array( 'style' => 'margin: -3px 0 0 -8px;' ),
					'options'    => array(
						'aggressive' => 'Aggressive',
						'standard'   => 'Standard',
					),
				),
			),
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $group );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-group beans_group_tests">
	<div class="bs-field-inside">
		<fieldset class="bs-field-fieldset">
			<legend class="bs-field-legend">Group of fields</legend>
			<div class="bs-field bs-activation">
				<input type="hidden" value="0" name="beans_fields[beans_group_activation_test]" />
				<input id="beans_group_activation_test" type="checkbox" name="beans_fields[beans_group_activation_test]" value="1" />
				<label for="beans_group_activation_test">Activate Foo</label>
			</div>
			<div class="bs-field bs-select">
				<label for="beans_group_select_test">Select Foo</label>
				<select id="beans_group_select_test" name="beans_fields[beans_group_select_test]" style="margin: -3px 0 0 -8px;">
					<option value="aggressive" selected='selected'>Aggressive</option>
					<option value="standard">Standard</option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="bs-field-description">This is a group of fields.</div>
</div>
EOB;
		// Run the tests.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
		$this->assertEquals( 2, did_action( 'beans_field_group_label' ) );
		$this->assertEquals( 1, did_action( 'beans_field_activation' ) );
		$this->assertEquals( 1, did_action( 'beans_field_select' ) );

		// Clean up.
		beans_remove_action( 'beans_field_activation', 'beans_field_activation' );
		beans_remove_action( 'beans_field_select', 'beans_field_select' );
	}

	/**
	 * Test should render the single field. This is a full integration test for the Fields API.
	 */
	public function test_full_integration_should_render_single_field() {
		$test_data = static::$test_data['single_fields'];

		// Register the fields.
		beans_register_fields( $test_data['fields'], 'beans_tests', $test_data['section'] );
		$fields = beans_get_fields( 'beans_tests', $test_data['section'] );

		// Register the checkbox, label, and description callbacks (as they've been unregistered in previous tests).
		add_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		add_action( 'beans_field_group_label', 'beans_field_label' );
		add_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
		add_action( 'beans_field_wrap_append_markup', 'beans_field_description' );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $fields[1] );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-checkbox beans_tests">
	<div class="bs-field-inside">
		<div class="bs-field bs-checkbox">
			<input type="hidden" value="0" name="beans_fields[beans_checkbox_test]" />
			<input id="beans_checkbox_test" type="checkbox" name="beans_fields[beans_checkbox_test]" value="1" />
			<span class="bs-checkbox-label">Enable the checkbox test</span>
		</div>
	</div>
</div>
EOB;
		// Check the HTML.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );

		// Clean up.
		remove_action( 'beans_field_checkbox', 'beans_field_checkbox' );
		remove_action( 'beans_field_group_label', 'beans_field_label' );
		remove_action( 'beans_field_wrap_prepend_markup', 'beans_field_label' );
		remove_action( 'beans_field_wrap_append_markup', 'beans_field_description' );
	}

	/**
	 * Test should render a group of fields. This is a full integration test for the Fields API.
	 */
	public function test_full_integration_should_render_group_of_fields() {
		$test_data = static::$test_data['group'];

		// Register the fields.
		beans_register_fields( $test_data['fields'], 'beans_tests', $test_data['section'] );
		$fields = beans_get_fields( 'beans_tests', $test_data['section'] );

		// Register each field's callback (as it's been unregistered in previous tests).
		foreach ( $test_data['fields'][0]['fields'] as $field ) {
			add_action( 'beans_field_' . $field['type'], 'beans_field_' . $field['type'] );
		}

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $fields[0] );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-group beans_tests">
	<div class="bs-field-inside">
		<fieldset class="bs-field-fieldset">
			<legend class="bs-field-legend">Group of fields</legend>
			<div class="bs-field bs-activation">
				<input type="hidden" value="0" name="beans_fields[beans_compile_all_scripts]" />
				<input id="beans_compile_all_scripts" type="checkbox" name="beans_fields[beans_compile_all_scripts]" value="1" />
				<label for="beans_compile_all_scripts"></label>
			</div>
			<div class="bs-field bs-select">
				<select id="beans_compile_all_scripts_mode" name="beans_fields[beans_compile_all_scripts_mode]" style="margin: -3px 0 0 -8px;">
					<option value="aggressive" selected='selected'>Aggressive</option>
					<option value="standard">Standard</option>
				</select>
			</div>
			<div class="bs-field bs-checkbox">
				<input type="hidden" value="0" name="beans_fields[beans_checkbox_test]" />
				<input id="beans_checkbox_test" type="checkbox" name="beans_fields[beans_checkbox_test]" value="1" />
				<span class="bs-checkbox-label">Enable the checkbox test</span>
			</div>
		</fieldset>
	</div>
	<div class="bs-field-description">This is a group of fields.</div>
</div>
EOB;
		// Check the HTML.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );

		// Clean up.
		foreach ( $test_data['fields'][0]['fields'] as $field ) {
			remove_action( 'beans_field_' . $field['type'], 'beans_field_' . $field['type'] );
		}
	}
}
