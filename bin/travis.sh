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

    # Run sniffers.
    lint_js_files
    lint_php_files

    # Run unit tests.
    echo '## Running unit tests:'
    phpunit --testsuite unit

    # Run integration tests.
    echo '## Running integration tests:'
    export PHPUNIT_CONFIG=tests/phpunit/integration/phpunit.xml.dist
    run_phpunit_travisci
fi
