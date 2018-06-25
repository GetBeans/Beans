<?php
/**
 * Beans Filters extend WordPress Filters by registering sub filters if they are told to do so.
 *
 * @package Beans\Framework\API\Filters
 */

/**
 * Hooks a callback (function or method) to a specific filter event.
 *
 * This function is similar to {@link https://codex.wordpress.org/Function_Reference/add_filter add_filter()}
 * with the exception that it accepts a $callback_or_value argument which is used to automatically create an
 * anonymous function.
 *
 * @since 1.0.0
 *
 * @param string         $hook              The name of the filter event to which the callback is hooked.
 * @param callable|mixed $callback_or_value For a callback, specify the name of the function|method you wish to be
 *                                          called when the filter event fires.
 *                                          For a value, specify the value to be returned when the filter event fires.
 *                                          Beans creates an anonymous function to hook into the filter event.
 * @param int            $priority          Optional. Used to specify the order in which the callbacks associated with
 *                                          a particular action are executed. Default is 10. Lower numbers correspond
 *                                          with earlier execution.  Callbacks with the same priority are executed in
 *                                          the order in which they were added to the filter.
 * @param int            $args              Optional. The number of arguments the callback accepts. Default is 1.
 *
 * @return bool|_Beans_Anonymous_Filters
 */
function beans_add_filter( $hook, $callback_or_value, $priority = 10, $args = 1 ) {

	if ( is_callable( $callback_or_value ) ) {
		return add_filter( $hook, $callback_or_value, $priority, $args );
	}

	return _beans_add_anonymous_filter( $hook, $callback_or_value, $priority, $args );
}

/**
 * Call the functions added to a filter hook.
 *
 * This function is similar to {@link https://codex.wordpress.org/Function_Reference/apply_filters apply_filters()}
 * with the exception of creating sub-hooks if it is told to do so.
 *
 * Sub-hooks must be set in square brackets as part of the filter id argument. Sub-hooks are cascaded
 * in a similar way to CSS classes. Maximum 3 sub-hooks allowed.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string $id    A unique string used as a reference. Sub-hook(s) must be set in square brackets. Each sub-
 *                      hook will create a filter. For instance, 'hook[_sub_hook]' will create a
 *                      'hook_name' filter as well as a 'hook[_sub_hook]' filter. The id may contain
 *                      multiple sub hooks such as 'hook[_sub_hook][_sub_sub_hook]'.
 *                      In this case, four filters will be created 'hook', 'hook[_sub_hook]',
 *                      'hook[_sub_sub_hook]' and 'hook[_sub_hook][_sub_sub_hook]'. Sub-hooks
 *                      always run the parent filter first, so a filter set to the parent will apply
 *                      to all sub-hooks. Maximum 3 sub-hooks allowed.
 * @param mixed  $value The value on which the filters hooked to <tt>$id</tt> are applied to it.
 *
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function beans_apply_filters( $id, $value ) {
	$args = func_get_args();

	// Return simple filter if no sub-hook(s) is(are) set.
	if ( ! preg_match_all( '#\[(.*?)\]#', $args[0], $sub_hooks ) ) {
		return call_user_func_array( 'apply_filters', $args );
	}

	$prefix          = current( explode( '[', $args[0] ) );
	$variable_prefix = $prefix;
	$suffix          = preg_replace( '/^.*\]\s*/', '', $args[0] );

	// Base filter.
	$args[0] = $prefix . $suffix;
	$value   = call_user_func_array( 'apply_filters', $args );

	foreach ( (array) $sub_hooks[0] as $index => $sub_hook ) {

		// If there are more than 3 sub-hooks, stop processing.
		if ( $index > 2 ) {
			break;
		}

		$variable_prefix .= $sub_hook;
		$levels           = array( $prefix . $sub_hook . $suffix );

		// Cascade sub-hooks.
		if ( $index > 0 ) {
			$levels[] = $variable_prefix . $suffix;
		}

		// Apply sub-hooks.
		foreach ( $levels as $level ) {
			$args[0] = $level;
			$args[1] = $value;
			$value   = call_user_func_array( 'apply_filters', $args );

			// Apply filter without square brackets for backwards compatibility.
			$args[0] = preg_replace( '#(\[|\])#', '', $args[0] );
			$args[1] = $value;
			$value   = call_user_func_array( 'apply_filters', $args );
		}
	}

	return $value;
}

/**
 * Check if any filter has been registered for a hook.
 *
 * This function is similar to {@link https://codex.wordpress.org/Function_Reference/has_filters has_filters()}
 * with the exception of checking sub-hooks if it is told to do so.
 *
 * @since 1.0.0
 *
 * @param string        $id       A unique string used as a reference. Sub-hook(s) must be set in square brackets.
 * @param callable|bool $callback Optional. The callback to check for. Default false.
 *
 * @return bool|int If $callback is omitted, returns boolean for whether the hook has any callbacks registered.
 *                  When checking a specific callback, returns the priority of that hook when a callback is registered;
 *                  else, it returns false. When using the `$callback` argument, this function may return a non-boolean
 *                  value that evaluates to false (e.g. 0). Make sure you use the === operator for testing the return
 *                  value.
 */
function beans_has_filters( $id, $callback = false ) {

	// Check simple filter if no sub-hook is set.
	if ( ! preg_match_all( '#\[(.*?)\]#', $id, $sub_hooks ) ) {
		return has_filter( $id, $callback );
	}

	$prefix          = current( explode( '[', $id ) );
	$variable_prefix = $prefix;
	$suffix          = preg_replace( '/^.*\]\s*/', '', $id );

	// Check base filter.
	$priority_number = has_filter( $prefix . $suffix, $callback );

	if ( false !== $priority_number ) {
		return $priority_number;
	}

	foreach ( (array) $sub_hooks[0] as $index => $sub_hook ) {

		// If there are more than 3 sub-hooks, return false.
		if ( $index > 2 ) {
			return false;
		}

		$variable_prefix .= $sub_hook;
		$levels           = array( $prefix . $sub_hook . $suffix );

		// Cascade sub-hooks.
		if ( $index > 0 ) {
			$levels[] = $variable_prefix . $suffix;
		}

		// Apply sub-hooks.
		foreach ( $levels as $level ) {
			$priority_number = has_filter( $level, $callback );

			if ( false !== $priority_number ) {
				return $priority_number;
			}

			// Check filter without square brackets for backwards compatibility.
			$priority_number = has_filter( preg_replace( '#(\[|\])#', '', $level ), $callback );

			if ( false !== $priority_number ) {
				return $priority_number;
			}
		}
	}

	return false;
}

/**
 * Add anonymous callback using a class since php 5.2 is still supported.
 *
 * @since  1.0.0
 * @since  1.5.0 Returns the object.
 * @ignore
 * @access private
 *
 * @param string $hook        The name of the filter event to which the callback is hooked.
 * @param mixed  $value       The value that will be returned when the anonymous callback runs.
 * @param int    $priority    Optional. Used to specify the order in which the functions
 *                            associated with a particular filter are executed. Default 10.
 *                            Lower numbers correspond with earlier execution,
 *                            and functions with the same priority are executed
 *                            in the order in which they were added to the filter.
 * @param int    $args        Optional. The number of arguments the function accepts. Default 1.
 *
 * @return _Beans_Anonymous_Filters
 */
function _beans_add_anonymous_filter( $hook, $value, $priority = 10, $args = 1 ) {
	require_once BEANS_API_PATH . 'filters/class-beans-anonymous-filters.php';

	return new _Beans_Anonymous_Filters( $hook, $value, $priority, $args );
}
