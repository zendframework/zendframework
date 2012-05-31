#!/bin/bash
travisdir=$(dirname "$0")
testdir="$travisdir/../tests"
testedcomponents=(`cat "$travisdir/tested-components"`)
result=0

for tested in "${testedcomponents[@]}"
    do
        echo "$tested:"
        phpunit -c $testdir/phpunit.xml.dist $testdir/$tested
        result=$(($result || $?))
done

exit $result
