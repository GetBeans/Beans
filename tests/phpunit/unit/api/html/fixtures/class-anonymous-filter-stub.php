<?php
/**
 * Stub for Beans_Anonymous_Filters.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Fixtures
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML\Fixtures;

/**
 * Class Anonymous_Filter_Stub
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Fixtures
 */
class Anonymous_Filter_Stub {

	/**
	 * The value that will be returned when this anonymous callback runs.
	 *
	 * @var mixed
	 */
	public $value_to_return;

	/**
	 * Anonymous_Filter_Stub constructor.
	 *
	 * @param string $hook            Hook.
	 * @param mixed  $value_to_return The value.
	 * @param int    $priority        Priority number.
	 */
	public function __construct( $hook, $value_to_return, $priority ) {
		$this->value_to_return = $value_to_return;

		add_filter( $hook, [ $this, 'callback' ], $priority );
	}

	/**
	 * Mocked callback.
	 */
	public function callback() {
		return $this->value_to_return;
	}
}
