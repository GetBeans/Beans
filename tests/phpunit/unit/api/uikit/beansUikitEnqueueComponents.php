<?php
/**
 * Tests for beans_uikit_enqueue_components().
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitEnqueueComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitEnqueueComponents extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_enqueue_components() should add the given core component into the registry when given a string.
	 */
	public function test_should_add_given_core_component_into_registry_when_given_string() {
		global $_beans_uikit_enqueued_items;

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( 'alert', 'core', false );
		$expected = [ 'alert' ];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( 'button', 'core', false );
		$expected[] = 'button';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( 'overlay', 'core', false );
		$expected[] = 'overlay';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given add-ons component into the registry when given a string.
	 */
	public function test_should_add_given_addons_component_into_registry_when_given_string() {
		global $_beans_uikit_enqueued_items;

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( 'accordion', 'add-ons', false );
		$expected = [ 'accordion' ];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( 'datepicker', 'add-ons', false );
		$expected[] = 'datepicker';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( 'sticky', 'add-ons', false );
		$expected[] = 'sticky';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given core components into the registry.
	 */
	public function test_should_add_given_core_components_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [
			'alert',
			'button',
			'overlay',
		];

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( $components, 'core', false );
		$this->assertSame( $components, $_beans_uikit_enqueued_items['components']['core'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( [ 'tab' ], 'core', false );
		$expected = array_merge( $components, [ 'tab' ] );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given add-ons components into the registry.
	 */
	public function test_should_add_given_addons_components_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [
			'accordion',
			'autocomplete',
			'datepicker',
			'sticky',
			'tooltip',
		];

		// Test when the registry is empty.
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( $components, 'add-ons', false );
		$this->assertSame( $components, $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Test when components are already in the registry.
		beans_uikit_enqueue_components( [ 'notify' ], 'add-ons', false );
		$expected = array_merge( $components, [ 'notify' ] );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should not add duplicates into the registry.
	 */
	public function test_should_not_add_duplicates_into_registry() {
		global $_beans_uikit_enqueued_items;

		$core                                      = [
			'alert',
			'button',
			'overlay',
		];
		$addons                                    = [
			'accordion',
			'autocomplete',
			'datepicker',
			'sticky',
			'tooltip',
		];
		$_beans_uikit_enqueued_items['components'] = [
			'core'    => $core,
			'add-ons' => $addons,
		];

		// Check the core components.
		beans_uikit_enqueue_components( 'alert', 'core', false );
		$this->assertCount( 3, $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertSame( $core, $_beans_uikit_enqueued_items['components']['core'] );

		// Check when duplicates are in different order.
		beans_uikit_enqueue_components( [ 'overlay', 'alert', 'button' ], 'core', false );
		$this->assertCount( 3, $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertSame( $core, $_beans_uikit_enqueued_items['components']['core'] );

		// Check the add-ons components.
		beans_uikit_enqueue_components( 'accordion', 'add-ons', false );
		$this->assertCount( 5, $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertSame( $addons, $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Check when duplicates are in different order.
		beans_uikit_enqueue_components(
			[
				'datepicker',
				'sticky',
				'tooltip',
				'autocomplete',
				'accordion',
			],
			'add-ons',
			false
		);
		$this->assertCount( 5, $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertSame( $addons, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add all core components into the registry when $components is true.
	 */
	public function test_should_add_all_core_components_into_registry_when_components_is_true() {
		$components = [
			'alert',
			'animation',
			'article',
			'badge',
			'base',
		];

		Monkey\Functions\expect( 'beans_uikit_get_all_components' )->once()->with( 'core' )->andReturn( $components );

		beans_uikit_enqueue_components( true );

		global $_beans_uikit_enqueued_items;
		$this->assertSame( $components, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add all add-ons components into the registry when $components is
	 * true.
	 */
	public function test_should_add_all_addons_components_into_registry_when_components_is_true() {
		$components = [
			'accordion',
			'autocomplete',
			'datepicker',
			'dotnav',
			'form-advanced',
			'sticky',
		];

		Monkey\Functions\expect( 'beans_uikit_get_all_components' )->once()->with( 'add-ons' )->andReturn( $components );

		beans_uikit_enqueue_components( true, 'add-ons' );

		global $_beans_uikit_enqueued_items;
		$this->assertSame( $components, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given core components and each (autoload) dependency into
	 * the registry.
	 */
	public function test_should_add_given_core_components_and_each_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [
			'alert',
			'button',
			'overlay',
			'tab',
		];
		Monkey\Functions\expect( '_beans_uikit_autoload_dependencies' )
			->once()
			->with( $components )
			->andReturnUsing(
				function() {
					global $_beans_uikit_enqueued_items;
					$_beans_uikit_enqueued_items['components']['core'] = [ 'flex', 'switcher' ];

					return [ 'flex', 'switcher' ];
				}
			);

		beans_uikit_enqueue_components( $components, 'core', true );
		$expected = array_merge( [ 'flex', 'switcher' ], $components );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given add-ons components and each (autoload) dependency into
	 * the registry.
	 */
	public function test_should_add_given_addons_components_and_each_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;
		$components = [ 'accordion', 'autocomplete', 'slideset' ];
		Monkey\Functions\expect( '_beans_uikit_autoload_dependencies' )
			->once()
			->with( $components )
			->andReturnUsing(
				function() {
					global $_beans_uikit_enqueued_items;
					$_beans_uikit_enqueued_items['components']['add-ons'] = [ 'dotnav' ];

					return [ 'dotnav' ];
				}
			);

		beans_uikit_enqueue_components( $components, 'add-ons', true );
		$expected = array_merge( [ 'dotnav' ], $components );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['add-ons'] );
	}
}
