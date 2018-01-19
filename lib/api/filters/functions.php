<?php
/**
 * Beans Filters extends WordPress Filters by registering sub filters if it is told to do so.
 *
 * @package API\Filters
 */

/**
 * Hooks a callback (function or method) to a specific filter event.
 *
 * This function is similar to {@link http://codex.wordpress.org/Function_Reference/add_filter add_filter()}
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
 * @return bool
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
 * This function is similar to {@link http://codex.wordpress.org/Function_Reference/apply_filters apply_filters()}
 * with the exception of creating sub-hooks if it is told to do so.
 *
 * Sub-hooks must be set in square brackets as part of the filter id argument. Sub-hooks are cascaded
 * in a similar way to CSS classes. Maximum 3 sub-hooks allowed.
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
 * @param mixed  $var   Additional variables passed to the functions hooked to <tt>$id</tt>.
 *
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function beans_apply_filters( $id, $value ) {

	$args = func_get_args();

	// Return simple filter if no sub-hook is set.
	if ( ! preg_match_all( '#\[(.*?)\]#', $args[0], $matches ) ) {
		return call_user_func_array( 'apply_filters', $args );
	}

	$prefix          = current( explode( '[', $args[0] ) );
	$variable_prefix = $prefix;
	$suffix          = preg_replace( '/^.*\]\s*/', '', $args[0] );

	// Base filter.
	$args[0] = $prefix . $suffix;
	$value   = call_user_func_array( 'apply_filters', $args );

	foreach ( $matches[0] as $i => $subhook ) {

		$variable_prefix = $variable_prefix . $subhook;
		$levels          = array( $prefix . $subhook . $suffix );

		// Cascade sub-hooks.
		if ( $i > 0 ) {

			if ( count( $matches[0] ) > 2 ) {
				$levels[] = str_replace( $subhook, '', $id );
			}

			$levels[] = $variable_prefix . $suffix;

		}

		// Apply sub-hooks.
		foreach ( $levels as $level ) {

			$args[0] = $level;
			$args[1] = $value;
			$value   = call_user_func_array( 'apply_filters', $args );

			// Apply filter whithout square brackets for backwards compatibility.
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
 * This function is similar to {@link http://codex.wordpress.org/Function_Reference/has_filters has_filters()}
 * with the exception of checking sub-hooks if it is told to do so.
 *
 * @since 1.0.0
 *
 * @param string        $id       The filter ID.
 * @param callback|bool $callback Optional. The callback to check for. Default false.
 *
 * @return bool|int If $callback is omitted, returns boolean for whether the hook has
 *                  anything registered. When checking a specific function, the priority of that
 *                  hook is returned, or false if the function is not attached. When using the
 *                  $callback argument, this function may return a non-boolean value
 *                  that evaluates to false (e.g. 0), so use the === operator for testing the
 *                  return value.
 */
function beans_has_filters( $id, $callback = false ) {

	// Check simple filter if no subhook is set.
	if ( ! preg_match_all( '#\[(.*?)\]#', $id, $matches ) ) {
		return has_filter( $id, $callback );
	}

	$prefix          = current( explode( '[', $id ) );
	$variable_prefix = $prefix;
	$suffix          = preg_replace( '/^.*\]\s*/', '', $id );

	// Check base filter.
	if ( has_filter( $prefix . $suffix, $callback ) ) {
		return true;
	}

	foreach ( $matches[0] as $i => $subhook ) {

		$variable_prefix = $variable_prefix . $subhook;
		$levels          = array( $prefix . $subhook . $suffix );

		// Cascade sub-hooks.
		if ( $i > 0 ) {

			$levels[] = str_replace( $subhook, '', $id );
			$levels[] = $variable_prefix . $suffix;

		}

		// Apply sub-hooks.
		foreach ( $levels as $level ) {

			if ( has_filter( $level, $callback ) ) {
				return true;
			}

			// Check filter whithout square brackets for backwards compatibility.
			if ( has_filter( preg_replace( '#(\[|\])#', '', $level ), $callback ) ) {
				return true;
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
	require_once( BEANS_API_PATH . 'filters/class-beans-anonymous-filters.php' );

	return new _Beans_Anonymous_Filters( $hook, $value, $priority, $args );
}
