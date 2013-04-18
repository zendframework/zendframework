#!/bin/bash
cd "$(dirname $(dirname "$0"))"

libraryCS=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 ./library/)
testsCS=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 ./tests/)

output="$libraryCS
$testsCS"

if [[ "$output" ]];
then
    echo   -en '\E[31m'"$output\033[1m\033[0m";
    printf "\n";
    echo   -en '\E[31;47m'"\033[1mCoding standards check failed!\033[0m"   # Red
    printf "\n";
    exit   2;
fi

echo   -en '\E[32m'"\033[1mCoding standards check passed!\033[0m"   # Green
printf "\n";
