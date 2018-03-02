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
use Brain\Monkey;

require_once __DIR__ . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansField
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
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
		Monkey\Actions\expectDone( 'beans_field_checkbox' )->once()->with( $field );
		Monkey\Actions\expectDone( 'beans_field_group_label' )->once();

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
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );

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
		Monkey\Actions\expectDone( 'beans_field_radio' )->once()->with( $field );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-radio beans_tests">
	<h3 class="bs-fields-header hndle">Having fun?</h3>
	<div class="bs-field-inside">
		<div class="bs-field bs-radio">
			<fieldset>
				<legend class="screen-reader-text">Radio buttons</legend>
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

		Monkey\Actions\expectDone( 'beans_field_group_label' )->once();
		Monkey\Actions\expectDone( 'beans_field_activate' )->once()->with( $group['fields'][0] );
		Monkey\Actions\expectDone( 'beans_field_activate' )->once()->with( $group['fields'][0] );
		Monkey\Actions\expectDone( 'beans_field_select' )->once()->with( $group['fields'][1] );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field( $group );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-group beans_group_tests">
	<h3 class="bs-fields-header hndle">Group of fields</h3>
	<div class="bs-field-inside">
		<div class="bs-field bs-activation">
			<label for="beans_group_activation_test">Activate Foo</label>
			<input type="hidden" value="0" name="beans_fields[beans_group_activation_test]" />
			<input id="beans_group_activation_test" type="checkbox" name="beans_fields[beans_group_activation_test]" value="1" />
		</div>
		<div class="bs-field bs-select">
			<label for="beans_group_select_test">Select Foo</label>
			<select id="beans_group_select_test" name="beans_fields[beans_group_select_test]" style="margin: -3px 0 0 -8px;">
				<option value="aggressive" selected='selected'>Aggressive</option>
				<option value="standard">Standard</option>
			</select>
		</div>
	</div>
	<div class="bs-field-description">This is a group of fields.</div>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );

		// Clean up.
		beans_remove_action( 'beans_field_activation', 'beans_field_activation' );
		beans_remove_action( 'beans_field_select', 'beans_field_select' );
	}
}
