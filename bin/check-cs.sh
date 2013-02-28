#!/bin/bash
cd "$(dirname $(dirname "$0"))"

output=$(php php-cs-fixer.phar fix -v --dry-run --level=psr2 .)
echo $output
cs=0
if [[ "$output" -ne "" ]];then
    cs=2
fi
echo "Coding standards exited with status $cs"

if [[ "$cs" -eq "2" ]];then
    echo "Exiting with status 2 due to CS";
    exit 2;
fi

exit 0
