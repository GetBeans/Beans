<?php
/**
 * The Beans HTML component contains a powerful set of functions to create flexible and easy overwritable HTML markup,
 * attributes and content.
 *
 * @package Beans\Framework\API\HTML
 *
 * @since   1.5.0
 */

/**
 * Register output by ID.
 *
 * The output can be modified using the available Beans HTML "output" functions.
 *
 * HTML comments containing the ID are added before and after the output if the development mode is enabled.
 * This makes it very easy to find a content ID when inspecting an element in your web browser.
 *
 * Since this function uses {@see beans_apply_filters()}, the $id argument may contain sub-hook(s).
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string $id     A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string $output Content to output.
 *
 * @return string The output.
 */
function beans_output( $id, $output ) {
	$args    = func_get_args();
	$args[0] = $id . '_output';

	$output = call_user_func_array( 'beans_apply_filters', $args );

	if ( empty( $output ) ) {
		return;
	}

	if ( _beans_is_html_dev_mode() ) {
		$output = "<!-- open output: $id -->" . $output . "<!-- close output: $id -->";
	}

	return $output;
}

/**
 * Echo output registered by ID.
 *
 * The output can be modified using the available Beans HTML "output" functions.
 *
 * HTML comments containing the ID are added before and after the output if the development mode is enabled.
 * This makes it very easy to find a content ID when inspecting an element in your web browser.
 *
 * Since this function uses {@see beans_apply_filters()}, the $id argument may contain sub-hook(s).
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.4.0
 * @uses beans_output()  To register output by ID.
 *
 * @param string $id     A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string $output Content to output.
 */
function beans_output_e( $id, $output ) {
	$args = func_get_args();
	echo call_user_func_array( 'beans_output', $args ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.
}

/**
 * Remove output.
 *
 * HTML comments containing the ID are added before and after the output if the development mode is enabled.
 * This makes it very easy to find a content ID when inspecting an element in your web browser.
 *
 * @since 1.0.0
 *
 * @param string $id The output ID.
 *
 * @return bool Will always return true.
 */
function beans_remove_output( $id ) {
	return beans_add_filter( $id . '_output', false );
}

/**
 * Register open markup and attributes by ID.
 *
 * The Beans HTML "markups" and "attributes" functions make it really easy to modify, replace, extend,
 * remove or hook into registered markup or attributes.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * Since this function uses {@see beans_apply_filters()}, the $id argument may contain sub-hook(s).
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string       $id         A unique string used as a reference. The $id argument may contain sub-hooks(s).
 * @param string|bool  $tag        The HTML tag. If set to False or empty, the markup HTML tag will be removed but
 *                                 the actions hook will be called. If set the Null, both markup HTML tag and actions
 *                                 hooks will be removed.
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value defines the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return string The output.
 */
function beans_open_markup( $id, $tag, $attributes = array() ) {
	global $_temp_beans_selfclose_markup;

	$args            = func_get_args();
	$attributes_args = $args;

	// Set markup tag filter id.
	$args[0] = $id . '_markup';

	if ( isset( $args[2] ) ) {
		unset( $args[2] );
	}

	// Remove function $tag argument.
	unset( $attributes_args[1] );

	// Stop here if the tag is set to false, the before and after actions won't run in this case.
	$tag = call_user_func_array( 'beans_apply_filters', $args );

	if ( null === $tag ) {
		return;
	}

	// Remove function $tag argument.
	unset( $args[1] );

	// Set before action id.
	$args[0] = $id . '_before_markup';

	$output = call_user_func_array( '_beans_render_action', $args );

	// Don't output the tag if empty, the before and after actions still run.
	if ( $tag ) {
		$output .= '<' . $tag . ' ' . call_user_func_array( 'beans_add_attributes', $attributes_args ) . ( _beans_is_html_dev_mode() ? ' data-markup-id="' . $id . '"' : null ) . ( $_temp_beans_selfclose_markup ? '/' : '' ) . '>';
	}

	// Set after action id.
	$args[0] = $id . ( $_temp_beans_selfclose_markup ? '_after_markup' : '_prepend_markup' );

	$output .= call_user_func_array( '_beans_render_action', $args );

	// Reset temp selfclose global to reduce memory usage.
	unset( $GLOBALS['_temp_beans_selfclose_markup'] );

	return $output;
}

/**
 * Echo open markup and attributes registered by ID.
 *
 * The Beans HTML "markups" and "attributes" functions make it really easy to modify, replace, extend,
 * remove or hook into registered markup or attributes.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * Since this function uses {@see beans_apply_filters()}, the $id argument may contain sub-hook(s).
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.4.0
 *
 * @param string       $id         A unique string used as a reference. The $id argument may contain sub-hooks(s).
 * @param string|bool  $tag        The HTML tag. If set to False or empty, the markup HTML tag will be removed but
 *                                 the actions hook will be called. If set the Null, both markup HTML tag and actions
 *                                 hooks will be removed.
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value defines the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return void
 */
function beans_open_markup_e( $id, $tag, $attributes = array() ) {
	$args = func_get_args();
	echo call_user_func_array( 'beans_open_markup', $args ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.
}

/**
 * Register self-close markup and attributes by ID.
 *
 * This function is shortcut of {@see beans_open_markup()}. It should be used for self-closing HTML markup such as
 * images or inputs.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string       $id         A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string|bool  $tag        The HTML self-close tag.If set to False or empty, the markup HTML tag will
 *                                 be removed but the actions hook will be called. If set the Null, both
 *                                 markup HTML tag and actions hooks will be removed.
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value defines the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return string The output.
 */
function beans_selfclose_markup( $id, $tag, $attributes = array() ) {
	global $_temp_beans_selfclose_markup;

	$_temp_beans_selfclose_markup = true; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Used in function scope.
	$args                         = func_get_args();

	return call_user_func_array( 'beans_open_markup', $args );
}

/**
 * Echo self-close markup and attributes registered by ID.
 *
 * This function is shortcut of {@see beans_open_markup()}. It should be used for self-closing HTML markup such as
 * images or inputs.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.4.0
 *
 * @param string       $id         A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string|bool  $tag        The HTML self-close tag.If set to False or empty, the markup HTML tag will
 *                                 be removed but the actions hook will be called. If set the Null, both
 *                                 markup HTML tag and actions hooks will be removed.
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value defines the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return void
 */
function beans_selfclose_markup_e( $id, $tag, $attributes = array() ) {
	$args = func_get_args();
	echo call_user_func_array( 'beans_selfclose_markup', $args ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.
}

/**
 * Register close markup by ID.
 *
 * This function is similar to {@see beans_open_markup()}, but does not accept HTML attributes. The $id
 * argument must be the identical to the opening markup.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string $id  Identical to the opening markup ID.
 * @param string $tag The HTML tag.
 *
 * @return string The output.
 */
function beans_close_markup( $id, $tag ) {
	// Stop here if the tag is set to false, the before and after actions won't run in this case.
	$tag = beans_apply_filters( $id . '_markup', $tag );

	if ( null === $tag ) {
		return;
	}

	$args = func_get_args();

	// Remove function $tag argument.
	unset( $args[1] );

	// Set before action id.
	$args[0] = $id . '_append_markup';

	$output = call_user_func_array( '_beans_render_action', $args );

	// Don't output the tag if empty, the before and after actions still run.
	if ( $tag ) {
		$output .= '</' . $tag . '>';
	}

	// Set after action id.
	$args[0] = $id . '_after_markup';

	$output .= call_user_func_array( '_beans_render_action', $args );

	return $output;
}

/**
 * Echo close markup registered by ID.
 *
 * This function is similar to {@see beans_open_markup()}, but does not accept HTML attributes. The $id
 * argument must be the identical to the opening markup.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.4.0
 *
 * @param string $id  Identical to the opening markup ID.
 * @param string $tag The HTML tag.
 */
function beans_close_markup_e( $id, $tag ) {
	$args = func_get_args();
	echo call_user_func_array( 'beans_close_markup', $args ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Pending security audit.
}

/**
 * Modify opening and closing HTML tag. Also works for self-closing markup.
 *
 * This function will automatically modify the opening and the closing HTML tag. If the markup is self-closing,
 * the HTML tag will be modified accordingly.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.0.0
 *
 * @param string          $id       The markup ID.
 * @param string|callback $markup   The replacement HTML tag. A callback is accepted if conditions needs
 *                                  to be applied. If arguments are available, they are passed to the callback.
 * @param int             $priority Optional. Used to specify the order in which the functions
 *                                  associated with a particular action are executed. Default 10.
 *                                  Lower numbers correspond with earlier execution,
 *                                  and functions with the same priority are executed
 *                                  in the order in which they were added to the action.
 * @param int             $args     Optional. The number of arguments the function accepts. Default 1.
 *
 * @return bool Will always return true.
 */
function beans_modify_markup( $id, $markup, $priority = 10, $args = 1 ) {
	return beans_add_filter( $id . '_markup', $markup, $priority, $args );
}

/**
 * Remove markup.
 *
 * This function will automatically remove the opening and the closing HTML tag. If the markup is self-closing,
 * the HTML tag will be removed accordingly.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.0.0
 *
 * @param string $id             The markup ID.
 * @param bool   $remove_actions Optional. Whether elements attached to a markup should be removed or not. This must be used
 *                               with absolute caution.
 *
 * @return bool Will always return true.
 */
function beans_remove_markup( $id, $remove_actions = false ) {

	if ( $remove_actions ) {
		return beans_add_filter( $id . '_markup', null );
	}

	return beans_add_filter( $id . '_markup', false );
}

/**
 * Reset markup.
 *
 * This function will automatically reset the opening and the closing HTML tag to its original value. If the markup is self-closed,
 * the HTML tag will be reset accordingly.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.3.1
 *
 * @param string $id The markup ID.
 *
 * @return void
 */
function beans_reset_markup( $id ) {
	remove_all_filters( $id . '_markup' );
	remove_all_filters( preg_replace( '#(\[|\])#', '', $id ) . '_markup' );
}

/**
 * Wrap markup.
 *
 * This function calls {@see beans_open_markup()} before the opening markup and {@see beans_close_markup()}
 * after the closing markup.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string       $id         The markup ID.
 * @param string       $new_id     A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string       $tag        The HTML wrap tag.
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value define the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return bool Will always return true.
 */
function beans_wrap_markup( $id, $new_id, $tag, $attributes = array() ) {
	$args = func_get_args();
	unset( $args[0] );

	_beans_add_anonymous_action( $id . '_before_markup', array( 'beans_open_markup', $args ), 9999 );

	unset( $args[3] );

	_beans_add_anonymous_action( $id . '_after_markup', array( 'beans_close_markup', $args ), 1 );

	return true;
}

/**
 * Wrap markup inner content.
 *
 * This function calls {@see beans_open_markup()} after the opening markup and {@see beans_close_markup()}
 * before the closing markup.
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string       $id         The markup ID.
 * @param string       $new_id     A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string       $tag        The HTML wrap tag.
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value define the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return bool Will always return true.
 */
function beans_wrap_inner_markup( $id, $new_id, $tag, $attributes = array() ) {
	$args = func_get_args();
	unset( $args[0] );

	_beans_add_anonymous_action( $id . '_prepend_markup', array( 'beans_open_markup', $args ), 1 );

	unset( $args[3] );

	_beans_add_anonymous_action( $id . '_append_markup', array( 'beans_close_markup', $args ), 9999 );

	return true;
}

/**
 * Register attributes by ID.
 *
 * The Beans HTML "attributes" functions make it really easy to modify, replace, extend, remove or hook
 * into registered attributes.
 *
 * Since this function uses {@see beans_apply_filters()}, the $id argument may contain sub-hook(s).
 *
 * Note: You can pass additional arguments to the functions that are hooked to <tt>$id</tt>.
 *
 * @since 1.0.0
 *
 * @param string       $id         A unique string used as a reference. The $id argument may contain sub-hook(s).
 * @param string|array $attributes Optional. Query string or array of attributes. The array key defines the
 *                                 attribute name and the array value define the attribute value. Setting
 *                                 the array value to '' will display the attribute value as empty
 *                                 (e.g. class=""). Setting it to 'false' will only display
 *                                 the attribute name (e.g. data-example). Setting it to 'null' will not
 *                                 display anything.
 *
 * @return string The HTML attributes.
 */
function beans_add_attributes( $id, $attributes = array() ) {
	$args    = func_get_args();
	$args[0] = $id . '_attributes';

	if ( ! isset( $args[1] ) ) {
		$args[1] = array();
	}

	$args[1] = wp_parse_args( $args[1] );

	$attributes = call_user_func_array( 'beans_apply_filters', $args );

	return beans_esc_attributes( $attributes );
}

/**
 * Reset markup attributes.
 *
 * This function will reset the targeted markup attributes to their original values. It must be called before
 * the targeted markup is called.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.3.1
 *
 * @param string $id The markup ID.
 *
 * @return void
 */
function beans_reset_attributes( $id ) {
	remove_all_filters( $id . '_attributes' );
	remove_all_filters( preg_replace( '#(\[|\])#', '', $id ) . '_attributes' );
}

/**
 * Add a value to an existing attribute or add a new attribute.
 *
 * This function must be called before the targeted markup is called.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.0.0
 * @since 1.5.0 Return the object.
 *
 * @param string $id        The markup ID.
 * @param string $attribute Name of the HTML attribute.
 * @param string $value     Value of the HTML attribute. If set to '' will display the attribute value as empty
 *                          (e.g. class=""). Setting it to 'false' will only display the attribute name
 *                          (e.g. data-example). Setting it to 'null' will not display anything.
 *
 * @return _Beans_Attribute
 */
function beans_add_attribute( $id, $attribute, $value ) {
	$attribute = new _Beans_Attribute( $id, $attribute, $value );

	return $attribute->init( 'add' );
}

/**
 * Replace the attribute's value. If the attribute does not exist, it is added with the new value.
 *
 * This function must be called before the targeted markup is called.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.0.0
 * @since 1.5.0 Return the object. Allows replacement of all values.
 *
 * @param string      $id        The markup ID.
 * @param string      $attribute Name of the HTML attribute to target.
 * @param string      $value     Value of the HTML attribute to be replaced. Setting it to an empty (i.e. empty string,
 *                               false, or null) replaces all of the values for this attribute.
 * @param string|null $new_value Optional. Replacement (new) value of the HTML attribute. Setting it to an empty string
 *                               ('') or null will remove the $value (e.g. class=""). Setting it to 'false', the
 *                               browser will display only the attribute name
 *                               (e.g. data-example).
 *
 * @return _Beans_Attribute
 */
function beans_replace_attribute( $id, $attribute, $value, $new_value = null ) {
	$attribute = new _Beans_Attribute( $id, $attribute, $value, $new_value );

	return $attribute->init( 'replace' );
}

/**
 * Remove a specific value from the attribute or remove the entire attribute.
 *
 * This function must be called before the targeted markup is called.
 *
 * The "data-markup-id" is added as a HTML attribute if the development mode is enabled. This makes it very
 * easy to find the content ID when inspecting an element in a web browser.
 *
 * @since 1.0.0
 * @since 1.5.0 Return the object.
 *
 * @param string      $id        The markup ID.
 * @param string      $attribute Name of the HTML attribute to target.
 * @param string|null $value     Optional. The attribute value to remove. Set it to 'false' or null to completely
 *                               remove the attribute.
 *
 * @return _Beans_Attribute
 */
function beans_remove_attribute( $id, $attribute, $value = null ) {
	$attribute = new _Beans_Attribute( $id, $attribute, $value, '' );

	return $attribute->init( 'remove' );
}

/**
 * Check if development mode is enabled taking in consideration legacy constant.
 *
 * @since  1.5.0
 * @ignore
 * @access private
 *
 * @return bool
 */
function _beans_is_html_dev_mode() {

	if ( defined( 'BEANS_HTML_DEV_MODE' ) ) {
		return (bool) BEANS_HTML_DEV_MODE;
	}

	return (bool) get_option( 'beans_dev_mode', false );
}
