<?php
/**
 * Tests for beans_field_description()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldDescription
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldDescription extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/field.php';
	}

	/**
	 * Test beans_field_description() should not render the field's description when none is given.
	 */
	public function test_should_not_render_field_description_when_none_given() {
		$field = $this->merge_field_with_default(
			[
				'id'      => 'beans_text_test',
				'type'    => 'text',
				'default' => '',
			]
		);

		$this->assertNull( beans_field_description( $field ) );

		$field['description'] = '';
		$this->assertNull( beans_field_description( $field ) );
	}

	/**
	 * Test beans_field_description() should render the field's description.
	 */
	public function test_should_render_field_description() {
		$field = $this->merge_field_with_default(
			[
				'id'          => 'beans_text_test',
				'type'        => 'text',
				'description' => 'Testing the text field.',
				'default'     => '',
			]
		);

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_description( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-description">Testing the text field.</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}


	/**
	 * Test beans_field_description() should render the field's extended description.
	 */
	public function test_should_render_extended_description() {
		$field = $this->merge_field_with_default(
			[
				'id'          => 'beans_text_test',
				'type'        => 'text',
				'description' => 'Testing the text field.<!--more-->This is the extended part of the description.',
				'default'     => '',
			]
		);

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_description( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-description">Testing the text field.<br />
	<a class="bs-read-more" href="#">More...</a>
	<div class="bs-extended-content" style="display: none;">This is the extended part of the description.</div>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
