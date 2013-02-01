#!/bin/bash
cd "$(dirname $(dirname "$0"))"
php ./tests/run-tests.php
testresults=$?
echo "Unit tests exited with $testresults"

php php-cs-fixer.phar fix -v --dry-run --level=psr2 library
cslibrary=$?
echo "Coding standards (library) exited with $cslibrary"

php php-cs-fixer.phar fix -v --dry-run --level=psr2 tests
cstests=$?
echo "Coding standards (tests) exited with $cstests"

php php-cs-fixer.phar fix -v --dry-run --level=psr2 bin
csbin=$?
echo "Coding standards (bin) exited with $csbin"

if [[ $testresults ]]; then
    exit 1 ;
fi
if [[ $cslibrary ]]; then
    exit 1 ;
fi
if [[ $cstests ]]; then
    exit 1 ;
fi
if [[ $csbin ]]; then
    exit 1 ;
fi

exit 0
