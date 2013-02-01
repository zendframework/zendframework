#!/bin/bash
cd "$(dirname $(dirname "$0"))"
php ./tests/run-tests.php
testresults=$?
cslibrary=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 library); if [[ $cslibrary ]];then while read -r line;do echo -e "\e[00;31m$line\e[00m"; done <<< "$cslibrary"; false; fi;
cstests=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 tests); if [[ $cstests ]]; then while read -r line; do echo -e "\e[00;31m$line\e[00m"; done <<< "$cstests"; false; fi;
csbin=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 bin); if [[ $csbin ]]; then while read -r line; do echo -e "\e[00;31m$line\e[00m"; done <<< "$csbin"; false; fi;

if [[ "$testresults" ]]; then
    exit 1 ;
fi
if [[ "$cslibrary" ]]; then
    exit 1 ;
fi
if [[ "$cstests" ]]; then
    exit 1 ;
fi
if [[ "$csbin" ]]; then
    exit 1 ;
fi

exit 0
