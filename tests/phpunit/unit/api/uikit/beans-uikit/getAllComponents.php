<?php
/**
 * Tests for the get_all_components() method of _Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_GetAllComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_GetAllComponents extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::get_all_components() should not return duplicate core components.
	 */
	public function test_should_not_return_duplicate_core_components() {
		$actual = ( new _Beans_Uikit() )->get_all_components( 'core' );

		// Get the number of times each component appears in the array.
		$num_times_component_in_array = array_count_values( $actual );

		// Spot check the common components that are in both JS and LESS directories, meaning they could be duplicated.
		$this->assertEquals( 1, $num_times_component_in_array['alert'] );
		$this->assertEquals( 1, $num_times_component_in_array['button'] );
		$this->assertEquals( 1, $num_times_component_in_array['cover'] );
		$this->assertEquals( 1, $num_times_component_in_array['tab'] );

		// By flipping the array, we should only have 1 element when there are no duplicates.
		$this->assertCount( 1, array_flip( $num_times_component_in_array ) );
	}

	/**
	 * Test _Beans_Uikit::get_all_components() should not return duplicate add-ons components.
	 */
	public function test_should_not_return_duplicate_add_ons_components() {
		$actual = ( new _Beans_Uikit() )->get_all_components( 'add-ons' );

		// Get the number of times each component appears in the array.
		$num_times_component_in_array = array_count_values( $actual );

		// Spot check the common components that are in both JS and LESS directories, meaning they could be duplicated.
		$this->assertEquals( 1, $num_times_component_in_array['accordion'] );
		$this->assertEquals( 1, $num_times_component_in_array['autocomplete'] );
		$this->assertEquals( 1, $num_times_component_in_array['datepicker'] );
		$this->assertEquals( 1, $num_times_component_in_array['sticky'] );
		$this->assertEquals( 1, $num_times_component_in_array['tooltip'] );

		// By flipping the array, we should only have 1 element when there are no duplicates.
		$this->assertCount( 1, array_flip( $num_times_component_in_array ) );
	}

	/**
	 * Test _Beans_Uikit::get_all_components() should return all core components.
	 */
	public function test_should_return_all_core_components() {
		$actual = ( new _Beans_Uikit() )->get_all_components( 'core' );
		$this->assertCount( 42, $actual );

		// Check common components.
		$this->assertContains( 'alert', $actual );
		$this->assertContains( 'button', $actual );
		$this->assertContains( 'cover', $actual );
		$this->assertContains( 'grid', $actual );
		$this->assertContains( 'nav', $actual );
		$this->assertContains( 'offcanvas', $actual );
		$this->assertContains( 'tab', $actual );
		$this->assertContains( 'utility', $actual );

		// Spot check the unique LESS components.
		$this->assertContains( 'base', $actual );
		$this->assertContains( 'close', $actual );
		$this->assertContains( 'column', $actual );
		$this->assertContains( 'description-list', $actual );
		$this->assertContains( 'thumbnail', $actual );
		$this->assertContains( 'variables', $actual );

		// Spot check the unique JS components.
		$this->assertContains( 'core', $actual );
		$this->assertContains( 'scrollspy', $actual );
		$this->assertContains( 'smooth-scroll', $actual );
		$this->assertContains( 'toggle', $actual );
		$this->assertContains( 'touch', $actual );

		// Check the components do not contain add-ons.
		$this->assertNotContains( 'accordion', $actual );
		$this->assertNotContains( 'datepicker', $actual );
		$this->assertNotContains( 'notify', $actual );
		$this->assertNotContains( 'progress', $actual );
	}

	/**
	 * Test _Beans_Uikit::get_all_components() should return all add-ons components.
	 */
	public function test_should_return_all_add_ons_components() {
		$actual = ( new _Beans_Uikit() )->get_all_components( 'add-ons' );
		$this->assertCount( 29, $actual );

		// Check common components.
		$this->assertContains( 'accordion', $actual );
		$this->assertContains( 'autocomplete', $actual );
		$this->assertContains( 'datepicker', $actual );
		$this->assertContains( 'form-password', $actual );
		$this->assertContains( 'search', $actual );
		$this->assertContains( 'sticky', $actual );
		$this->assertContains( 'tooltip', $actual );
		$this->assertContains( 'upload', $actual );

		// Spot check the unique LESS components.
		$this->assertContains( 'dotnav', $actual );
		$this->assertContains( 'form-advanced', $actual );
		$this->assertContains( 'htmleditor', $actual );

		// Spot check the unique JS components.
		$this->assertContains( 'parallax', $actual );
		$this->assertContains( 'lightbox', $actual );
		$this->assertContains( 'slideshow-fx', $actual );
		$this->assertContains( 'timepicker', $actual );

		// Check the components do not contain add-ons.
		$this->assertNotContains( 'alert', $actual );
		$this->assertNotContains( 'badge', $actual );
		$this->assertNotContains( 'base', $actual );
		$this->assertNotContains( 'close', $actual );
	}
}
