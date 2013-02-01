#!/bin/bash
cd "$(dirname $(dirname "$0"))"
php ./tests/run-tests.php
testresults=$?
echo "Unit tests exited with status $testresults"

output=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 library)
echo $output
cslibrary=0
if [[ "$output" -ne "" ]];then
    cslibrary=2
fi
echo "Coding standards (library) exited with status $cslibrary"

output=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 tests)
echo $output
cstests=0
if [[ "$output" -ne "" ]];then
    cstests=2
fi
echo "Coding standards (tests) exited with status $cstests"

output=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 bin)
echo $output
csbin=0
if [[ "$output" -ne "" ]];then
    csbin=2
fi
echo "Coding standards (bin) exited with status $csbin"

if [[ "$testresults" -ne "0" ]]; then
    echo "Exiting with status 2 due to test failures" ; 
    exit 2 ;
fi
if [[ "$cslibrary" -eq "2" ]]; then
    echo "Exiting with status 2 due to CS (library)" ; 
    exit 2 ;
fi
if [[ "$cstests" -eq "2" ]]; then
    echo "Exiting with status 2 due to CS (tests)" ; 
    exit 2 ;
fi
if [[ "$csbin" -eq "2" ]]; then
    echo "Exiting with status 2 due to CS (bin)" ; 
    exit 2 ;
fi

exit 0
