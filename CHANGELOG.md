# Beans Changelog

2018.06.21 - version 1.5.0-beta

* Made a11y accessible by adding skip links, ARIA, labels, tabs, and more
* Upgraded UIkit to version 2.27.5
* Optimized the APIs
* Made APIs more robust
* Made WPCS compliant
* Fully tested the APIs
* Improved documentation
* Updated the tm-beans.pot file
* Improved CSS compiler to have one block of declarations per line
* Improved escaping and sanitizing of data per rendering to browser or saving to the database
* Fixed Customizer Preview Tools
* Fixed UIkit API bug when not returning all dependency components
* Fixed Beans Image Editor for ARRAY_A
* Fixed `beans_get_post_meta`
* Fixed `beans_get_term_meta`
* Fixed Compiler to recompile when a fragment changes and not in developer mode
* Fixed replacing action to remove from WordPress
* Fixed Actions API to allow priority of 0 to be modified
* Fixed Actions API double subhook calls
* Fixed `beans_path_to_url` to bail out when relative URL
* Fixed count for `beans_count_recursive`
* Fixed removing tilde from `beans_url_to_path`
* Fixed processing relative URLs in `beans_url_to_path`
* Fixed altering of non-internal URLs in `beans_url_to_path`
* Fixed `beans_get_layout_class` not returning correct classes when secondary is no longer registered
* Fixed 'Next Post' icon close markup ID
* Fixed 'Read More' icon markup IDs
* Eliminated storing actions in encoded strings
