<?php
/**
 * This class creates an anonymous callback, which is required since Beans still supports PHP 5.2.
 *
 * @package Beans\Framework\API\Filters
 *
 * @since   1.0.0
 */

/**
 * Anonymous Filter.
 *
 * @since   1.0.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Filters
 */
final class _Beans_Anonymous_Filters {

	/**
	 * The value that will be returned when this anonymous callback runs.
	 *
	 * @var mixed
	 */
	public $value_to_return;

	/**
	 * Constructor.
	 *
	 * @param string $hook            The name of the filter to which the $callback is hooked.
	 * @param mixed  $value_to_return The value that will be returned when this anonymous callback runs.
	 * @param int    $priority        Optional. Used to specify the order in which the functions
	 *                                associated with a particular filter are executed. Default 10.
	 *                                Lower numbers correspond with earlier execution,
	 *                                and functions with the same priority are executed
	 *                                in the order in which they were added to the filter.
	 * @param int    $args            Optional. The number of arguments the function accepts. Default 1.
	 */
	public function __construct( $hook, $value_to_return, $priority, $args ) {
		$this->value_to_return = $value_to_return;

		add_filter( $hook, array( $this, 'callback' ), $priority, $args );
	}

	/**
	 * Get filter content and set it as the callback.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function callback() {
		return $this->value_to_return;
	}
}
