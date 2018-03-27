<?php
/**
 * This class creates an anonymous callback, which is required since Beans still supports PHP 5.2.
 *
 * @package Beans\Framework\API\Actions
 *
 * @since   1.5.0
 */

/**
 * Anonymous Action.
 *
 * @since   1.5.0
 * @ignore
 * @access  private
 *
 * @package Beans\Framework\API\Actions
 */
final class _Beans_Anonymous_Action {

	/**
	 * The callback to register to the given $hook.
	 *
	 * @var string
	 */
	public $callback;

	/**
	 * Constructor.
	 *
	 * @param string $hook        The name of the action to which the $callback is hooked.
	 * @param array  $callback    The callback to register to the given $hook and arguments to pass.
	 * @param int    $priority    Optional. Used to specify the order in which the functions
	 *                            associated with a particular action are executed. Default 10.
	 *                            Lower numbers correspond with earlier execution,
	 *                            and functions with the same priority are executed
	 *                            in the order in which they were added to the action.
	 * @param int    $number_args Optional. The number of arguments the function accepts. Default 1.
	 */
	public function __construct( $hook, array $callback, $priority = 10, $number_args = 1 ) {
		$this->callback = $callback;

		add_action( $hook, array( $this, 'callback' ), $priority, $number_args );
	}

	/**
	 * Get action content and set it as the callback.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function callback() {
		echo call_user_func_array( $this->callback[0], $this->callback[1] ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- The callback handles escaping its output, as Beans does not know what HTML or content will be passed back to it.
	}
}
