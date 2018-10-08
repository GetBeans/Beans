<?php
/**
 * Tests for beans_uikit_dequeue_components().
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitDequeueComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitDequeueComponents extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_dequeue_components() should remove then given core component from the registry when given a
	 * string.
	 */
	public function test_should_remove_given_core_component_from_registry_when_given_string() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core'] = [
			'alert',
			'button',
			'overlay',
		];

		beans_uikit_dequeue_components( 'alert' );
		$this->assertNotContains( 'alert', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertSame(
			[
				1 => 'button',
				2 => 'overlay',
			],
			$_beans_uikit_enqueued_items['components']['core']
		);

		beans_uikit_dequeue_components( 'button' );
		$this->assertNotContains( 'button', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertSame(
			[
				2 => 'overlay',
			],
			$_beans_uikit_enqueued_items['components']['core']
		);

		beans_uikit_dequeue_components( 'overlay' );
		$this->assertNotContains( 'button', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_dequeue_components() should remove the given add-ons component from the registry when given a
	 * string.
	 */
	public function test_should_remove_given_addons_component_from_registry_when_given_string() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['add-ons'] = [
			'accordion',
			'datepicker',
			'sticky',
		];

		beans_uikit_dequeue_components( 'accordion', 'add-ons' );
		$this->assertNotContains( 'accordion', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertSame(
			[
				1 => 'datepicker',
				2 => 'sticky',
			],
			$_beans_uikit_enqueued_items['components']['add-ons']
		);

		beans_uikit_dequeue_components( 'datepicker', 'add-ons' );
		$this->assertNotContains( 'datepicker', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertSame(
			[
				2 => 'sticky',
			],
			$_beans_uikit_enqueued_items['components']['add-ons']
		);

		beans_uikit_dequeue_components( 'sticky', 'add-ons' );
		$this->assertNotContains( 'sticky', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_dequeue_components() should remove the given core components from the registry when given an
	 * array.
	 */
	public function test_should_remove_given_core_components_from_registry_when_given_array() {
		$components = [
			'alert',
			'button',
			'overlay',
		];
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core'] = $components;

		beans_uikit_dequeue_components( [ 'alert' ] );
		$this->assertNotContains( 'alert', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertSame(
			[
				1 => 'button',
				2 => 'overlay',
			],
			$_beans_uikit_enqueued_items['components']['core']
		);

		beans_uikit_dequeue_components( $components );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );

		// Check when there's nothing in the registry.
		beans_uikit_dequeue_components( [ 'tab' ] );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_dequeue_components() should remove the given add-ons component(s) from the registry when given
	 * an array.
	 */
	public function test_should_remove_given_addons_component_from_registry_when_given_array() {
		$components = [
			'accordion',
			'datepicker',
			'sticky',
		];
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['add-ons'] = $components;

		beans_uikit_dequeue_components( [ 'sticky' ], 'add-ons' );
		$this->assertNotContains( 'sticky', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertSame( [ 'accordion', 'datepicker' ], $_beans_uikit_enqueued_items['components']['add-ons'] );

		beans_uikit_dequeue_components( [ 'accordion', 'datepicker' ], 'add-ons' );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Check when there's nothing in the registry.
		beans_uikit_dequeue_components( [ 'tooltip' ] );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_dequeue_components() should remove all core components from the registry when true is passed as
	 * the argument for $components.
	 */
	public function test_should_remove_all_core_components_from_registry_when_components_is_true() {
		$components = [
			'alert',
			'animation',
			'article',
			'badge',
			'base',
		];
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core'] = $components;

		beans_uikit_dequeue_components( true );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should remove all add-ons components from the registry when true is passed as
	 * the argument for $components.
	 */
	public function test_should_remove_all_addons_components_from_registry_when_components_is_true() {
		$components = [
			'accordion',
			'autocomplete',
			'datepicker',
			'dotnav',
			'form-advanced',
			'sticky',
		];
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['add-ons'] = $components;

		beans_uikit_dequeue_components( true, 'add-ons' );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
	}
}
