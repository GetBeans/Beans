#!/bin/bash
DEV_LIB_TRAVIS_PATH='./vendor/xwp/wp-dev-lib/travis.after_script.sh'

# Exit if the dev lib isn't installed
if [ ! -f $DEV_LIB_TRAVIS_PATH ]; then
   echo "Oops, the Dev Library is not install, please run composer install!"
else
   # Run sniffers and unit tests
   export PATH="./vendor/bin:$PATH"
   export WP_TESTS_DIR='exclude'
   $DEV_LIB_TRAVIS_PATH

   # Run integration tests.
   export WP_TESTS_DIR=''
   export DEV_LIB_ONLY=phpunit
   $DEV_LIB_TRAVIS_PATH
fi
