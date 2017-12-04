#!/bin/bash
set -e

export PATH="./vendor/bin:$PATH"

# Exit if the dev lib isn't installed
if [ ! -f $DEV_LIB_TRAVIS_PATH ]; then
    echo "Oops, the Dev Library is not install, please run composer install!"
else
    echo "## Checking files, scope $CHECK_SCOPE:"
    if [[ $CHECK_SCOPE != "all" ]]; then
        cat "$TEMP_DIRECTORY/paths-scope"
    fi

    # Run sniffers and unit tests.
    lint_js_files
	lint_php_files
    phpunit --testsuite unit

    # Run integration tests.
    export DEV_LIB_ONLY=phpunit
    run_phpunit_travisci
fi
