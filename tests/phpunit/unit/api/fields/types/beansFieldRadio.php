<?php
/**
 * Tests for beans_field_radio()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldRadio
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansFieldRadio extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/radio.php';
	}

	/**
	 * Test beans_field_radio() should render the radio field with image options.
	 */
	public function test_should_render_radio_field_with_image_options() {
		$field = $this->merge_field_with_default( array(
			'id'          => 'beans_layout',
			'label'       => 'Layout',
			'description' => 'The layout settings.',
			'type'        => 'radio',
			'default'     => 'default_fallback',
			'options'     => array(
				'default_fallback' => 'Use Default Layout',
				'c'                => 'http://example.com/images/layouts/c.png',
				'c_sp'             => 'http://example.com/images/layouts/c_sp.png',
				'sp_c'             => 'http://example.com/images/layouts/sp_c.png',
			),
		) );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_radio( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<fieldset class="bs-field-fieldset">
	<legend class="bs-field-legend">Layout</legend>
    <label class="" for="beans_layout_default_fallback">
        <input id="beans_layout_default_fallback" type="radio" name="beans_fields[beans_layout]" value="default_fallback" checked='checked' />
        Use Default Layout</label>
    <label class="bs-has-image" for="beans_layout_c">
        <span class="screen-reader-text">Option for c</span>
        <img src="http://example.com/images/layouts/c.png" alt="Option for c" />
        <input id="beans_layout_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout]" value="c" />
    </label>
    <label class="bs-has-image" for="beans_layout_c_sp">
        <span class="screen-reader-text">Option for c_sp</span>
        <img src="http://example.com/images/layouts/c_sp.png" alt="Option for c_sp" />
        <input id="beans_layout_c_sp" class="screen-reader-text" type="radio" name="beans_fields[beans_layout]" value="c_sp" />
    </label>
    <label class="bs-has-image" for="beans_layout_sp_c">
        <span class="screen-reader-text">Option for sp_c</span>
        <img src="http://example.com/images/layouts/sp_c.png" alt="Option for sp_c" />
        <input id="beans_layout_sp_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout]" value="sp_c" />
    </label>
</fieldset>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_radio() should render accessible images when accessibility parameters are given.
	 */
	public function test_should_render_accessible_images_when_given() {
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

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_radio( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<fieldset class="bs-field-fieldset">
	<legend class="bs-field-legend">Layout</legend>
    <label class="" for="beans_layout_default_fallback">
        <input id="beans_layout_default_fallback" type="radio" name="beans_fields[beans_layout]" value="default_fallback" checked='checked' />
        Use Default Layout</label>
    <label class="bs-has-image" for="beans_layout_c">
        <span class="screen-reader-text">Option for the Content Only Layout</span>
        <img src="http://example.com/images/layouts/c.png" alt="Content Only Layout" />
        <input id="beans_layout_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout]" value="c" />
    </label>
    <label class="bs-has-image" for="beans_layout_c_sp">
        <span class="screen-reader-text">Option for the Content + Sidebar Primary Layout</span>
        <img src="http://example.com/images/layouts/c_sp.png" alt="Option for the Content + Sidebar Primary Layout" />
        <input id="beans_layout_c_sp" class="screen-reader-text" type="radio" name="beans_fields[beans_layout]" value="c_sp" />
    </label>
    <label class="bs-has-image" for="beans_layout_sp_c">
        <span class="screen-reader-text">Sidebar Primary + Content Layout</span>
        <img src="http://example.com/images/layouts/sp_c.png" alt="Sidebar Primary + Content Layout" />
        <input id="beans_layout_sp_c" class="screen-reader-text" type="radio" name="beans_fields[beans_layout]" value="sp_c" />
    </label>
</fieldset>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
