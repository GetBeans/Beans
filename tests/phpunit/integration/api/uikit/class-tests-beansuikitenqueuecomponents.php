<?php
/**
 * Tests for beans_uikit_enqueue_components().
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitEnqueueComponents
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitEnqueueComponents extends UIkit_Test_Case {

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

		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		beans_uikit_enqueue_components( $components, 'core', true );
		$expected = array_merge( [ 'flex', 'switcher' ], $components );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );

		$expected[] = 'close';
		$expected[] = 'modal';
		beans_uikit_enqueue_components( 'modal', 'core', true );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );

		$expected[] = 'animation';
		$expected[] = 'scrollspy';
		beans_uikit_enqueue_components( 'scrollspy', 'core', true );
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add the given addons components and each (autoload) dependency into
	 * the registry.
	 */
	public function test_should_add_given_addons_components_and_each_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;

		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertEmpty( $_beans_uikit_enqueued_items['components']['add-ons'] );
		beans_uikit_enqueue_components( 'lightbox', 'add-ons', true );
		$expected = [
			'core'    => [
				'animation',
				'flex',
				'close',
				'modal',
				'overlay',
			],
			'add-ons' => [
				'slidenav',
				'lightbox',
			],
		];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );

		beans_uikit_enqueue_components( 'slider', 'add-ons', true );
		$expected['add-ons'][] = 'slider';
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );

		beans_uikit_enqueue_components( [ 'accordion', 'autocomplete', 'slideset' ], 'add-ons', true );
		$expected['add-ons'] = array_merge(
			$expected['add-ons'],
			[
				'dotnav',
				'accordion',
				'autocomplete',
				'slideset',
			]
		);
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add all core components into the registry when $components is true.
	 */
	public function test_should_add_all_core_components_into_registry_when_components_is_true() {
		global $_beans_uikit_enqueued_items;

		beans_uikit_enqueue_components( true );

		$this->assertCount( 42, $_beans_uikit_enqueued_items['components']['core'] );

		// Check common components.
		$this->assertContains( 'alert', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'button', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'cover', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'grid', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'nav', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'offcanvas', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'tab', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'utility', $_beans_uikit_enqueued_items['components']['core'] );

		// Spot check the unique LESS components.
		$this->assertContains( 'base', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'close', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'column', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'description-list', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'thumbnail', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'variables', $_beans_uikit_enqueued_items['components']['core'] );

		// Spot check the unique JS components.
		$this->assertContains( 'core', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'scrollspy', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'smooth-scroll', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'toggle', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertContains( 'touch', $_beans_uikit_enqueued_items['components']['core'] );

		// Check the components do not contain add-ons.
		$this->assertNotContains( 'accordion', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertNotContains( 'datepicker', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertNotContains( 'notify', $_beans_uikit_enqueued_items['components']['core'] );
		$this->assertNotContains( 'progress', $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test beans_uikit_enqueue_components() should add all add-ons components into the registry when $components is
	 * true.
	 */
	public function test_should_add_all_addons_components_into_registry_when_components_is_true() {
		global $_beans_uikit_enqueued_items;

		beans_uikit_enqueue_components( true, 'add-ons' );

		$this->assertCount( 29, $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Check common components.
		$this->assertContains( 'accordion', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'autocomplete', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'datepicker', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'form-password', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'search', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'sticky', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'tooltip', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'upload', $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Spot check the unique LESS components.
		$this->assertContains( 'dotnav', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'form-advanced', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'htmleditor', $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Spot check the unique JS components.
		$this->assertContains( 'parallax', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'lightbox', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'slideshow-fx', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertContains( 'timepicker', $_beans_uikit_enqueued_items['components']['add-ons'] );

		// Check the components do not contain add-ons.
		$this->assertNotContains( 'alert', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertNotContains( 'badge', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertNotContains( 'base', $_beans_uikit_enqueued_items['components']['add-ons'] );
		$this->assertNotContains( 'close', $_beans_uikit_enqueued_items['components']['add-ons'] );
	}
}
