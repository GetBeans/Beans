<?php
/**
 * Stub for API Actions.
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions;

/**
 * Class Actions_Stub
 *
 * @package Beans\Framework\Tests\Unit\API\Actions
 */
class Actions_Stub {

	/**
	 * Dummy static method.
	 */
	public static function dummy_static_method() {
		// Nothing happening here.
	}

	/**
	 * Dummy method.
	 */
	public function dummy_method() {
		// Nothing happening here.
	}

	/**
	 * Echo what you send me.
	 *
	 * @param string|int $message Message to echo.
	 */
	public static function echo_static( $message ) {
		echo $message; // @codingStandardsIgnoreLine - WordPress.XSS.EscapeOutput.OutputNotEscaped - reason: we are not testing escaping functionality.
	}
}
