<?php
/**
 * Tests for beans_field_checkbox()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldCheckbox
 *
 * @package Beans\Framework\Tests\Integration\API\Fields
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansFieldCheckbox extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/checkbox.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_checkbox', 'beans_field_checkbox' );
	}

	/**
	 * Test beans_field_checkbox() should render the checkbox with the label when given.
	 */
	public function test_should_render_checkbox_with_label_when_given() {
		$field = $this->merge_field_with_default( array(
			'id'             => 'beans_compile_all_styles',
			'label'          => false,
			'checkbox_label' => 'Compile all WordPress styles',
			'type'           => 'checkbox',
			'default'        => false,
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_checkbox( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input type="hidden" value="0" name="beans_fields[beans_compile_all_styles]" />
<input id="beans_compile_all_styles" type="checkbox" name="beans_fields[beans_compile_all_styles]" value="1" />
<span class="bs-checkbox-label">Compile all WordPress styles</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_checkbox() should render the checkbox with the default label when none is given.
	 */
	public function test_should_render_checkbox_with_default_label_when_none_is_given() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_compile_all_styles',
			'type'    => 'checkbox',
			'default' => false,
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_checkbox( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input type="hidden" value="0" name="beans_fields[beans_compile_all_styles]" />
<input id="beans_compile_all_styles" type="checkbox" name="beans_fields[beans_compile_all_styles]" value="1" />
<span class="bs-checkbox-label">Enable</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_checkbox() should render the checkbox with attributes when given.
	 */
	public function test_should_render_checkbox_with_attributes_when_given() {
		$field = $this->merge_field_with_default( array(
			'id'             => 'beans_compile_all_styles',
			'checkbox_label' => 'Compile all WordPress styles',
			'type'           => 'checkbox',
			'default'        => false,
			'attributes'     => array(
				'data-test' => 'foo',
			),
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_checkbox( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input type="hidden" value="0" name="beans_fields[beans_compile_all_styles]" />
<input id="beans_compile_all_styles" type="checkbox" name="beans_fields[beans_compile_all_styles]" value="1" data-test="foo"/>
<span class="bs-checkbox-label">Compile all WordPress styles</span>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
