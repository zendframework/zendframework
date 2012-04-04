#!/bin/bash
travisdir=$(dirname $(readlink /proc/$$/fd/255))
testdir="$travisdir/../tests"
testedcomponents=(`cat "$travisdir/tested-components"`)
result=0

for tested in "${testedcomponents[@]}"
    do
        echo "$tested:"
        phpunit -c $testdir/phpunit.xml $testdir/$tested
        let "result = $result || $?"
done

exit $result