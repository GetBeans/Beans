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
	 * Prepares the test environment before loading the tests.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/checkbox.php';
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();

		// Make sure the radio is hooked into Beans.
		beans_add_smart_action( 'beans_field_checkbox', 'beans_field_checkbox' );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();

		beans_remove_action( 'beans_field_checkbox', 'beans_field_checkbox' );
	}

	/**
	 * Test beans_field() should render a checkbox field.
	 */
	public function test_should_render_a_checkbox_field() {
		$field = $this->merge_field_with_default( array(
			'id'             => 'beans_compile_all_styles',
			'label'          => false,
			'checkbox_label' => 'Compile all WordPress styles',
			'type'           => 'checkbox',
			'default'        => false,
		) );
		Monkey\Actions\expectDone( 'beans_field_checkbox' )->once()->with( $field );

		ob_start();
		beans_field( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="bs-field-wrap bs-checkbox beans_tests">
	<div class="bs-field-inside">
		<div class="bs-field bs-checkbox">
			<input type="hidden" value="0" name="beans_fields[beans_compile_all_styles]" />
			<input type="checkbox" name="beans_fields[beans_compile_all_styles]" value="1" />
			<span class="bs-checkbox-label">Compile all WordPress styles</span>
		</div>
	</div>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
