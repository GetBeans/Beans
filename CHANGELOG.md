# Beans Changelog

## 2018.06.21 - version 1.5.0-beta

This pre-release includes security, web accessibility, code quality improvements, performance improvements, and bug fixes.  The entire framework is now WPCS compliant.  The APIs are now fully and thoroughly well-tested. All found issues are resolved.  New functionality is added. 

### Improved
* Improved web accessibility by adding skip links, ARIA, labels, tabs, and more.
* Improved the performance of the APIs by reducing expensive processes, memory usage, and execution time.
* Improved the APIs by adding thorough unit and integration test suites.
* Improved the APIs by adding type hinting, reducing redundant code, and removing unused code.
* Improved Beans by making it WPCS compliant.
* Improved CSS compiler to have one block of declarations per line.
* Improved security by auditing and applying escaping and sanitizing best practices.
* Improved the Actions API by no longer storing actions in encoded strings.
* Improved code documentation and readability.

### Updated
* Updated the language's tm-beans.pot file.
* Updated UIkit to version 2.27.5 (see [UIkit changelog](https://github.com/uikit/uikit/blob/v2/master/CHANGELOG.md#2275) for more information).

### Added
* Added `beans_str_ends_with()` to check if the given string ends with the given substring(s).
* Added `beans_str_starts_with()` to check if the given string starts with the given substring(s).
* Added `beans_multi_array_key_exists()` to check if a key or index exists in a multi-dimensional array.
* Added `beans_join_arrays()` to join two arrays together.
* Added `beans_array_unique()` to remove duplicate values and re-index the array.
* Added `beans_join_arrays_clean()` to join two arrays, remove the duplicate values and empties, and provide an option to re-index the clean array.
* Added `beans_scandir()` to scan the given directory path, list all the files and directories, and remove the `.` and `..` files.
* Added `beans_uikit_get_all_components()` to check all of the UIkit components for the given type.
* Added `beans_uikit_get_all_dependencies()` to check all of the UIkit dependencies for the given component(s).
* Added `beans_get_widget_area_output()` to replace `beans_get_widget_area()`.
* Added `beans_add_compiler_options_to_settings()` to instantiate `_Beans_Compiler_Options` through a hook instead of on file load.
* Added `beans_add_page_assets_compiler()` to instantiate `__Beans_Page_Compiler` through a hook instead of on file load.
* Added `beans_add_image_options_to_settings()` to instantiate `___Beans_Image_Options` through a hook instead of on file load.
* Added `beans_has_primary_sidebar()` to check if the given layout has a primary sidebar.
* Added `beans_has_secondary_sidebar()` to check if the given layout has a secondary sidebar.
* Added `beans_skip_links_list()` to filter the skip links.
* Added `beans_output_skip_links()` to render the skip links.
* Added `beans_accessibility_skip_link_fix()` to enqueue skip link fix script for IE 11.
* Added many new private functions.

### Changes
* `beans_add_action()` returns `false` when the action is not added via `add_action`.
* `beans_add_smart_action()` returns `false` when the action is not added via `add_action`.
* `beans_modify_action()` returns `false` when hook, callback, priority, and args are not given.
* `beans_modify_action_hook()` returns `false` when the hook is empty or not a string.
* `beans_modify_action_callback()` returns `false` when the callback is empty.
* `beans_replace_action()` returns `false` when hook, callback, priority, and args are not given.
* `beans_replace_action_hook()` returns `false` when the hook is empty or not a string.
* `beans_replace_action_callback()` returns `false` when the callback is empty.
* `beans_remove_action()` sets "removed" to the default when no current action exists.
* `beans_reset_action()` bails out if the action does not need to be reset.
* Unset the global variable in `beans_selfclose_markup()` to reduce memory usage.
* `beans_wrap_markup()` bails out and returns `false` if the given `$tag` is empty.
* `beans_wrap_inner_markup()` bails out and returns `false` if the given `$tag` is empty.
* `beans_add_attribute()` returns an instance of `_Beans_Attribute`.
* `beans_replace_attribute()` replaces all values when the `value` argument is empty.
* `beans_replace_attribute()` returns an instance of `_Beans_Attribute`.
* `beans_remove_attribute()` returns an instance of `_Beans_Attribute`.
* Moved the API's HTML to view files to improve code quality.
* Changes to private functions and methods are not noted here.

### Fixed
* Fixed `depedencies` typo in Compiler's configuration (now `dependencies`). Provided fallback.
* Fixed Customizer Preview Tools.
* Fixed UIkit API bug when not returning all dependency components.
* Fixed Beans Image Editor for ARRAY_A.
* Fixed `beans_get_post_meta()`.
* Fixed `beans_get_term_meta()`.
* Fixed Compiler to recompile when a fragment changes and not in development mode.
* Fixed replacing action to remove from WordPress.
* Fixed Actions API to allow priority of 0 to be modified.
* Fixed Actions API double subhook calls.
* Fixed `beans_path_to_url()` to bail out when relative URL.
* Fixed count for `beans_count_recursive()`.
* Fixed removing tilde from `beans_url_to_path()`.
* Fixed processing relative URLs in `beans_url_to_path()`.
* Fixed altering of non-internal URLs in `beans_url_to_path()`.
* Fixed `beans_get_layout_class()` not returning correct classes when secondary is no longer registered.
* Fixed 'Next Post' icon close markup ID.
* Fixed 'Read More' icon markup IDs.
* Fixed `beans_get_widget_area()` to return `false` on fail.
* Fixed `beans_get_widget()` to return `false` on fail.
* Fixes to private functions and methods are not noted here.

### Deprecated
* Deprecated `beans_count_recursive()` as it is unused in Beans.
* Deprecated `beans_widget_area()` by replacing it with a renamed function `beans_get_widget_area_output()` that better describes the expected behavior.
* Deprecated the image toolbar's dashicons' class attributes.

## 2016.10.12 - version 1.4.0

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.4.0) for more information.

## 2016.10.10 - version 1.4.0-rc

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.4.0-rc) for more information.

## 2016.09.30 - version 1.4.0-beta

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.4.0-beta) for more information.

## 2016.04.28 - version 1.3.1

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.1) for more information.

## 2016.04.25 - version 1.3.1-rc2

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.1-rc2) for more information.

## 2016.04.22 - version 1.3.1-rc

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.1-rc) for more information.

## 2016.04.09 - version 1.3.1-beta

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.1-beta) for more information.

## 2016.02.18 - version 1.3.0

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.0) for more information.

## 2016.02.15 - version 1.3.0-rc

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.0-rc) for more information.

## 2016.02.10 - version 1.3.0-beta

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.3.0-beta) for more information.

## 2016.01.05 - version 1.2.5

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.5) for more information.

## 2015.12.13 - version 1.2.4

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.4) for more information.

## 2015.11.30 - version 1.2.3

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.3) for more information.

## 2015.11.28 - version 1.2.2

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.2) for more information.

## 2015.11.11 - version 1.2.1

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.1) for more information.

## 2015.09.28 - version 1.2.0

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.0) for more information.

## 2016.01.15 - version 1.2.0-rc

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.0-rc) for more information.

## 2016.01.15 - version 1.2.0-beta

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.2.0-beta) for more information.

## 2015.09.08- version 1.1.2

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.1.2) for more information.

## 2015.09.08- version 1.1.1

See the [release changelog](https://github.com/Getbeans/Beans/releases/tag/1.1.1) for more information.