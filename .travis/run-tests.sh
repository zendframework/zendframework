#!/bin/bash
travisdir=$(dirname $(readlink /proc/$$/fd/255))
testdir="$travisdir/../tests"
testedcomponents=(`cat "$travisdir/tested-components"`)

for tested in "${testedcomponents[@]}"
    do phpunit -c $testdir/phpunit.xml $testdir/$tested
done
