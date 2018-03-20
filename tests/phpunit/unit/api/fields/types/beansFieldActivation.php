<?php
/**
 * Tests for beans_field_activation()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldActivation
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldActivation extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/activation.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_activation', 'beans_field_activation' );
	}

	/**
	 * Test beans_field_activation() should render the activation field.
	 */
	public function test_should_render_activation_field() {
		$field = $this->merge_field_with_default( array(
			'id'      => 'beans_compile_all_scripts',
			'type'    => 'activation',
			'default' => false,
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_activation( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input type="hidden" value="0" name="beans_fields[beans_compile_all_scripts]" />
<input id="beans_compile_all_scripts" type="checkbox" name="beans_fields[beans_compile_all_scripts]" value="1" />
<label for="beans_compile_all_scripts"></label>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_activation() should render the activation field with attributes when given.
	 */
	public function test_should_render_activation_field_with_attributes_when_given() {
		$field = $this->merge_field_with_default( array(
			'id'         => 'beans_compile_all_scripts',
			'type'       => 'activation',
			'default'    => false,
			'attributes' => array(
				'data-test' => 'foo',
			),
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_activation( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<input type="hidden" value="0" name="beans_fields[beans_compile_all_scripts]" />
<input id="beans_compile_all_scripts" type="checkbox" name="beans_fields[beans_compile_all_scripts]" value="1" data-test="foo"/>
<label for="beans_compile_all_scripts"></label>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
