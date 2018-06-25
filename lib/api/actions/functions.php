<?php
/**
 * Beans Actions extends WordPress Actions by registering each action with a unique ID.
 *
 * While WordPress requires two or three arguments to remove an action, Beans
 * actions can be modified, replaced, removed or reset using only the ID as a reference.
 *
 * @package Beans\Framework\API\Actions
 *
 * @since   1.5.0
 */

/**
 * Hooks a callback (function or method) to a specific action event.
 *
 * This function is similar to {@link https://codex.wordpress.org/Function_Reference/add_action add_action()}
 * with the exception of being registered by ID within Beans in order to be manipulated by the other Beans
 * Actions functions.
 *
 * @since 1.0.0
 * @since 1.5.0 Returns false when action is not added via add_action.
 *
 * @param string   $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string   $hook     The name of the action to which the `$callback` is hooked.
 * @param callable $callback The name of the function|method you wish to be called when the action event fires.
 * @param int      $priority Optional. Used to specify the order in which the callbacks associated with a particular
 *                           action are executed. Default is 10.
 *                           Lower numbers correspond with earlier execution.  Callbacks with the same priority
 *                           are executed in the order in which they were added to the action.
 * @param int      $args     Optional. The number of arguments the callback accepts. Default is 1.
 *
 * @return bool
 */
function beans_add_action( $id, $hook, $callback, $priority = 10, $args = 1 ) {
	$action = array(
		'hook'     => $hook,
		'callback' => $callback,
		'priority' => $priority,
		'args'     => $args,
	);

	$replaced_action = _beans_get_action( $id, 'replaced' );

	// If the ID is set to be "replaced", then replace that(those) parameter(s).
	if ( ! empty( $replaced_action ) ) {
		$action = array_merge( $action, $replaced_action );
	}

	$action = _beans_set_action( $id, $action, 'added', true );

	// If the ID is set to be "removed", then bail out.
	if ( _beans_get_action( $id, 'removed' ) ) {
		return false;
	}

	$modified_action = _beans_get_action( $id, 'modified' );

	// If the ID is set to be "modified", then modify that(those) parameter(s).
	if ( ! empty( $modified_action ) ) {
		$action = array_merge( $action, $modified_action );
	}

	return add_action( $action['hook'], $action['callback'], $action['priority'], $action['args'] );
}

/**
 * Set {@see beans_add_action()} using the callback argument as the action ID.
 *
 * This function is a shortcut of {@see beans_add_action()}. It does't require a Beans ID as it uses the
 * callback argument instead.
 *
 * @since 1.0.0
 * @since 1.5.0 Returns false when action is not added via add_action.
 *
 * @param string   $hook     The name of the action to which the `$callback` is hooked.
 * @param callable $callback The name of the function|method you wish to be called when the action event fires.
 * @param int      $priority Optional. Used to specify the order in which the callbacks associated with a particular
 *                           action are executed. Default is 10.
 *                           Lower numbers correspond with earlier execution.  Callbacks with the same priority
 *                           are executed in the order in which they were added to the action.
 * @param int      $args     Optional. The number of arguments the callback accepts. Default is 1.
 *
 * @return bool
 */
function beans_add_smart_action( $hook, $callback, $priority = 10, $args = 1 ) {
	return beans_add_action( $callback, $hook, $callback, $priority, $args );
}

/**
 * Modify one or more of the arguments for the given action, i.e. referenced by its Bean's ID.
 *
 * This function modifies a registered action using {@see beans_add_action()} or
 * {@see beans_add_smart_action()}. Each optional argument must be set to NULL to keep the original value.
 *
 * The original action can be reset using {@see beans_reset_action()}.
 *
 * @since 1.0.0
 * @since 1.5.0 Improved action parameter filtering.
 *
 * @param string        $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string|null   $hook     Optional. The new action's event name to which the $callback is hooked.
 *                                Use NULL to keep the original value.
 * @param callable|null $callback Optional. The new callback (function or method) you wish to be called.
 *                                Use NULL to keep the original value.
 * @param int|null      $priority Optional. The new priority.
 *                                Use NULL to keep the original value.
 * @param int|null      $args     Optional. The new number of arguments the $callback accepts.
 *                                Use NULL to keep the original value.
 *
 * @return bool
 */
function beans_modify_action( $id, $hook = null, $callback = null, $priority = null, $args = null ) {
	$action = _beans_build_action_array( $hook, $callback, $priority, $args );

	// If no changes were passed in, there's nothing to modify. Bail out.
	if ( empty( $action ) ) {
		return false;
	}

	$current_action = _beans_get_current_action( $id );

	// If the action is registered, let's remove it.
	if ( ! empty( $current_action ) ) {
		remove_action( $current_action['hook'], $current_action['callback'], $current_action['priority'] );
	}

	// Merge the modified parameters and register with Beans.
	$action = _beans_merge_action( $id, $action, 'modified' );

	// If there is no action to modify, bail out.
	if ( empty( $current_action ) ) {
		return false;
	}

	// Overwrite the modified parameters.
	$action = array_merge( $current_action, $action );

	return add_action( $action['hook'], $action['callback'], $action['priority'], $action['args'] );
}

/**
 * Modify one or more of the arguments for the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_modify_action()}.
 *
 * @since 1.0.0
 * @since 1.5.0 Return false if the hook is empty or not a string.
 *
 * @param string $id   The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string $hook The new action's event name to which the callback is hooked.
 *
 * @return bool
 */
function beans_modify_action_hook( $id, $hook ) {

	if ( empty( $hook ) || ! is_string( $hook ) ) {
		return false;
	}

	return beans_modify_action( $id, $hook );
}

/**
 * Modify the callback of the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_modify_action()}.
 *
 * @since 1.0.0
 * @since 1.5.0 Return false if the callback is empty.
 *
 * @param string   $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param callable $callback The new callback (function or method) you wish to be called.
 *
 * @return bool
 */
function beans_modify_action_callback( $id, $callback ) {

	if ( empty( $callback ) ) {
		return false;
	}

	return beans_modify_action( $id, null, $callback );
}

/**
 * Modify the priority of the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_modify_action()}.
 *
 * @since 1.0.0
 *
 * @param string     $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param int|string $priority The new priority.
 *
 * @return bool
 */
function beans_modify_action_priority( $id, $priority ) {
	return beans_modify_action( $id, null, null, $priority );
}

/**
 * Modify the number of arguments of the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_modify_action()}.
 *
 * @since 1.0.0
 *
 * @param string     $id             The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param int|string $number_of_args The new number of arguments the $callback accepts.
 *
 * @return bool
 */
function beans_modify_action_arguments( $id, $number_of_args ) {
	return beans_modify_action( $id, null, null, null, $number_of_args );
}

/**
 * Replace one or more of the arguments for the given action, i.e. referenced by its Bean's ID.
 *
 * This function replaces an action registered using {@see beans_add_action()} or
 * {@see beans_add_smart_action()}. Each optional argument must be set to NULL to keep
 * the original value.
 *
 * This function is not resettable as it overwrites the original action's argument(s).
 * That means using {@see beans_reset_action()} will not restore the original action.
 *
 * @since 1.0.0
 * @since 1.5.0 Returns false when no replacement arguments are passed.
 *
 * @param string        $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string|null   $hook     Optional. The new action's event name to which the $callback is hooked.
 *                                Use NULL to keep the original value.
 * @param callable|null $callback Optional. The new callback (function or method) you wish to be called.
 *                                Use NULL to keep the original value.
 * @param int|null      $priority Optional. The new priority.
 *                                Use NULL to keep the original value.
 * @param int|null      $args     Optional. The new number of arguments the $callback accepts.
 *                                Use NULL to keep the original value.
 *
 * @return bool
 */
function beans_replace_action( $id, $hook = null, $callback = null, $priority = null, $args = null ) {
	$action = _beans_build_action_array( $hook, $callback, $priority, $args );

	// If no changes were passed in, there's nothing to modify. Bail out.
	if ( empty( $action ) ) {
		return false;
	}

	// Set and get the latest "replaced" action.
	$action = _beans_merge_action( $id, $action, 'replaced' );

	// Modify the action.
	$is_modified = beans_modify_action( $id, $hook, $callback, $priority, $args );

	// If there's a current action, merge it with the replaced one; else, it will be replaced when the original is added.
	if ( $is_modified ) {
		_beans_merge_action( $id, $action, 'added' );
	}

	return $is_modified;
}

/**
 * Replace the action's event name (hook) for the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_replace_action()}.
 *
 * @since 1.0.0
 * @since 1.5.0 Return false if the hook is empty or not a string.
 *
 * @param string $id   The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string $hook The new action's event name to which the callback is hooked.
 *
 * @return bool
 */
function beans_replace_action_hook( $id, $hook ) {

	if ( empty( $hook ) || ! is_string( $hook ) ) {
		return false;
	}

	return beans_replace_action( $id, $hook );
}

/**
 * Replace the callback of the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_replace_action()}.
 *
 * @since 1.0.0
 * @since 1.5.0 Return false if the callback is empty.
 *
 * @param string $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string $callback The new callback (function or method) you wish to be called.
 *
 * @return bool
 */
function beans_replace_action_callback( $id, $callback ) {

	if ( empty( $callback ) ) {
		return false;
	}

	return beans_replace_action( $id, null, $callback );
}

/**
 * Replace the priority of the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_replace_action()}.
 *
 * @since 1.0.0
 *
 * @param string $id       The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param int    $priority The new priority.
 *
 * @return bool
 */
function beans_replace_action_priority( $id, $priority ) {
	return beans_replace_action( $id, null, null, $priority );
}

/**
 * Replace the number of arguments of the given action, i.e. referenced by its Bean's ID.
 *
 * This function is a shortcut of {@see beans_replace_action()}.
 *
 * @since 1.0.0
 *
 * @param string $id   The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param int    $args The new number of arguments the $callback accepts.
 *
 * @return bool
 */
function beans_replace_action_arguments( $id, $args ) {
	return beans_replace_action( $id, null, null, null, $args );
}

/**
 * Remove an action.
 *
 * This function removes an action registered using {@see beans_add_action()} or
 * {@see beans_add_smart_action()}. The original action can be re-added using {@see beans_reset_action()}.
 *
 * This function is "load order" agnostic, meaning that you can remove an action before it's added.
 *
 * @since 1.0.0
 * @since 1.5.0 When no current action, sets "removed" to default configuration.
 *
 * @param string $id The action's Beans ID, a unique ID tracked within Beans for this action.
 *
 * @return bool
 */
function beans_remove_action( $id ) {
	$action = _beans_get_current_action( $id );

	// When there is a current action, remove it.
	if ( ! empty( $action ) ) {
		remove_action( $action['hook'], $action['callback'], $action['priority'] );
	} else {
		// If the action is not registered yet, set it to a default configuration.
		$action = array(
			'hook'     => null,
			'callback' => null,
			'priority' => null,
			'args'     => null,
		);
	}

	// Store as "removed".
	return _beans_set_action( $id, $action, 'removed' );
}

/**
 * Reset an action.
 *
 * This function resets an action registered using {@see beans_add_action()} or
 * {@see beans_add_smart_action()}.
 *
 * If the original values were replaced using {@see beans_replace_action()}, these values will be used, as
 * {@see beans_replace_action()} is not resettable.
 *
 * @since 1.0.0
 * @since 1.5.0 Bail out if the action does not need to be reset.
 *
 * @param string $id The action's Beans ID, a unique ID tracked within Beans for this action.
 *
 * @return bool
 */
function beans_reset_action( $id ) {
	_beans_unset_action( $id, 'modified' );
	_beans_unset_action( $id, 'removed' );

	$action = _beans_get_action( $id, 'added' );

	// If there is no "added" action, bail out.
	if ( empty( $action ) ) {
		return false;
	}

	$current = _beans_get_current_action( $id );

	// If there's no current action, return the "added" action.
	if ( empty( $current ) ) {
		return $action;
	}

	remove_action( $current['hook'], $current['callback'], $current['priority'] );
	add_action( $action['hook'], $action['callback'], $action['priority'], $action['args'] );

	return $action;
}

/**
 * Initialize action globals.
 *
 * @ignore
 */
global $_beans_registered_actions;

if ( ! isset( $_beans_registered_actions ) ) {
	$_beans_registered_actions = array(
		'added'    => array(),
		'modified' => array(),
		'removed'  => array(),
		'replaced' => array(),
	);
}

/**
 * Get the action's configuration for the given ID and status. Returns `false` if the action is not registered with
 * Beans.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param string $id     The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string $status Status for which to get the action.
 *
 * @return array|bool
 */
function _beans_get_action( $id, $status ) {
	global $_beans_registered_actions;

	$registered_actions = beans_get( $status, $_beans_registered_actions );

	// If the status is empty, return false, as no actions are registered.
	if ( empty( $registered_actions ) ) {
		return false;
	}

	$id     = _beans_unique_action_id( $id );
	$action = beans_get( $id, $registered_actions );

	// If the action is empty, return false.
	if ( empty( $action ) ) {
		return false;
	}

	return $action;
}

/**
 * Store the action's configuration for the given ID and status.
 *
 * What happens if the action's configuration is already registered?  If the `$overwrite` flag is set to `true`,
 * then the new action's configuration is stored, overwriting the previous one. Else, the registered action's
 * configuration is returned.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param string      $id        The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param array|mixed $action    The action configuration to store.
 * @param string      $status    Status for which to store the action.
 * @param bool        $overwrite Optional. When set to `true`, the new action's configuration is stored, overwriting a
 *                               previously stored configuration (if one exists).
 *
 * @return array|mixed
 */
function _beans_set_action( $id, $action, $status, $overwrite = false ) {
	$id = _beans_unique_action_id( $id );

	// If not overwriting, return the registered action (if it's registered).
	if ( ! $overwrite ) {
		$registered_action = _beans_get_action( $id, $status );

		if ( ! empty( $registered_action ) ) {
			return $registered_action;
		}
	}

	if ( ! empty( $action ) || 'removed' === $status ) {
		global $_beans_registered_actions;
		$_beans_registered_actions[ $status ][ $id ] = $action;
	}

	return $action;
}

/**
 * Unset the action's configuration for the given ID and status. Returns `false` if there are is no action
 * registered with Beans actions for the given ID and status. Else, returns true when complete.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param string $id     The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param string $status Status for which to get the action.
 *
 * @return bool
 */
function _beans_unset_action( $id, $status ) {
	$id = _beans_unique_action_id( $id );

	// Bail out if the ID is not registered for the given status.
	if ( false === _beans_get_action( $id, $status ) ) {
		return false;
	}

	global $_beans_registered_actions;
	unset( $_beans_registered_actions[ $status ][ $id ] );

	return true;
}

/**
 * Merge the action's configuration and then store it for the given ID and status.
 *
 * If the action's configuration has not already been registered with Beans, just store it.
 *
 * @since  1.0.0
 * @ignore
 * @access private
 *
 * @param string $id     The action's Beans ID, a unique ID tracked within Beans for this action.
 * @param array  $action The new action's configuration to merge and then store.
 * @param string $status Status for which to merge/store this action.
 *
 * @return array
 */
function _beans_merge_action( $id, array $action, $status ) {
	$id                = _beans_unique_action_id( $id );
	$registered_action = _beans_get_action( $id, $status );

	// If the action's configuration is already registered with Beans, merge the new configuration with it.
	if ( ! empty( $registered_action ) ) {
		$action = array_merge( $registered_action, $action );
	}

	// Now store/register it.
	return _beans_set_action( $id, $action, $status, true );
}

/**
 * Get the current action, meaning get from the "added" and/or "modified" statuses.
 *
 * @since  1.0.0
 * @since  1.5.0 Bails out if there is no "added" action registered.
 * @ignore
 * @access private
 *
 * @param string $id The action's Beans ID, a unique ID tracked within Beans for this action.
 *
 * @return array|bool
 */
function _beans_get_current_action( $id ) {

	// Bail out if the action is "removed".
	if ( _beans_get_action( $id, 'removed' ) ) {
		return false;
	}

	$added = _beans_get_action( $id, 'added' );

	// If there is no "added" action registered, bail out.
	if ( empty( $added ) ) {
		return false;
	}

	$modified = _beans_get_action( $id, 'modified' );

	// If the action is set to be modified, merge the changes and return the action.
	if ( ! empty( $modified ) ) {
		return array_merge( $added, $modified );
	}

	return $added;
}

/**
 * Build the action's array for only the valid given arguments.
 *
 * @since 1.5.0
 *
 * @param string|null   $hook     Optional. The action event's name to which the $callback is hooked.
 *                                Valid when not falsey,
 *                                i.e. (meaning not `null`, `false`, `0`, `0.0`, an empty string, or empty array).
 * @param callable|null $callback Optional. The callback (function or method) you wish to be called when the event
 *                                fires. Valid when not falsey, i.e. (meaning not `null`, `false`, `0`, `0.0`, an empty
 *                                string, or empty array).
 * @param int|null      $priority Optional. Used to specify the order in which the functions associated with a
 *                                particular action are executed. Valid when it's numeric, including 0.
 * @param int|null      $args     Optional. The number of arguments the callback accepts.
 *                                Valid when it's numeric, including 0.
 *
 * @return array
 */
function _beans_build_action_array( $hook = null, $callback = null, $priority = null, $args = null ) {
	$action = array();

	if ( ! empty( $hook ) ) {
		$action['hook'] = $hook;
	}

	if ( ! empty( $callback ) ) {
		$action['callback'] = $callback;
	}

	foreach ( array( 'priority', 'args' ) as $arg_name ) {
		$arg = ${$arg_name};

		if ( is_numeric( $arg ) ) {
			$action[ $arg_name ] = (int) $arg;
		}
	}

	return $action;
}

/**
 * Add anonymous callback using a class since php 5.2 is still supported.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param string $hook        The name of the action to which the $callback is hooked.
 * @param array  $callback    The callback to register to the given $hook and arguments to pass.
 * @param int    $priority    Optional. Used to specify the order in which the functions
 *                            associated with a particular action are executed. Default 10.
 *                            Lower numbers correspond with earlier execution,
 *                            and functions with the same priority are executed
 *                            in the order in which they were added to the action.
 * @param int    $number_args Optional. The number of arguments the function accepts. Default 1.
 *
 * @return _Beans_Anonymous_Action
 */
function _beans_add_anonymous_action( $hook, array $callback, $priority = 10, $number_args = 1 ) {
	require_once BEANS_API_PATH . 'actions/class-beans-anonymous-action.php';

	return new _Beans_Anonymous_Action( $hook, $callback, $priority, $number_args );
}

/**
 * Render action which can therefore be stored in a variable.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param mixed $hook Hook and possibly sub-hooks to be rendered.
 *
 * @return bool|null|string
 */
function _beans_render_action( $hook ) {
	$args = func_get_args();

	// Return simple action if no sub-hook(s) is(are) set.
	if ( ! preg_match_all( '#\[(.*?)\]#', $args[0], $sub_hooks ) ) {
		return _beans_when_has_action_do_render( $args );
	}

	$output          = null;
	$prefix          = current( explode( '[', $args[0] ) );
	$variable_prefix = $prefix;
	$suffix          = preg_replace( '/^.*\]\s*/', '', $args[0] );

	// Base hook.
	$args[0] = $prefix . $suffix;

	// If the base hook is registered, render it.
	_beans_when_has_action_do_render( $args, $output );

	foreach ( (array) $sub_hooks[0] as $index => $sub_hook ) {
		$variable_prefix .= $sub_hook;

		$levels = array( $prefix . $sub_hook . $suffix );

		// Cascade sub-hooks.
		if ( $index > 0 ) {
			$levels[] = $variable_prefix . $suffix;
		}

		// Apply sub-hooks.
		foreach ( $levels as $level ) {
			$args[0] = $level;

			// If the level is registered, render it.
			_beans_when_has_action_do_render( $args, $output );

			// Apply filter without square brackets for backwards compatibility.
			$args[0] = preg_replace( '#(\[|\])#', '', $args[0] );

			// If the backwards compatible $args[0] is registered, render it.
			_beans_when_has_action_do_render( $args, $output );
		}
	}

	return $output;
}

/**
 * Render all hooked action callbacks by firing {@see do_action()}.  The output is captured in the buffer and then
 * returned.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param array  $args   Array of arguments.
 * @param string $output The output to be updated.
 *
 * @return string|bool
 */
function _beans_when_has_action_do_render( array $args, &$output = '' ) {

	if ( ! has_action( $args[0] ) ) {
		return false;
	}

	ob_start();
	call_user_func_array( 'do_action', $args );
	$output .= ob_get_clean();

	return $output;
}

/**
 * Make sure the action ID is unique.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @param mixed $callback Callback to convert into a unique ID.
 *
 * @return array|string
 */
function _beans_unique_action_id( $callback ) {

	if ( is_string( $callback ) ) {
		return $callback;
	}

	if ( is_object( $callback ) ) {
		$callback = array( $callback, '' );
	} else {
		$callback = (array) $callback;
	}

	// Treat object.
	if ( is_object( $callback[0] ) ) {

		if ( function_exists( 'spl_object_hash' ) ) {
			return spl_object_hash( $callback[0] ) . $callback[1];
		}

		return get_class( $callback[0] ) . $callback[1];
	}

	// Treat static method.
	if ( is_string( $callback[0] ) ) {
		return $callback[0] . '::' . $callback[1];
	}

	return md5( $callback );
}
