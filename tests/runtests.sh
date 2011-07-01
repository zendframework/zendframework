:
# Zend Framework
#
# testgroup.sh - Launch PHPUnit for specific test group(s).
#
# Usage: testgroup.sh [ -h <html-dir> ] [ -c <clover-xml-file> ]
#     [ ALL | <test-group> [ <test-group> ... ] ]
#
# This script makes it easier to execute PHPUnit test runs from the
# shell, using @group tags defined in the test suite files to run
# subsets of tests.
#
# To get a list of all @group tags:
#     phpunit --list-groups AllTests.php
#
# LICENSE
#
# This source file is subject to the new BSD license that is bundled
# with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# http://framework.zend.com/license/new-bsd
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@zend.com so we can send you a copy immediately.
#
# @category   Zend
# @package    UnitTests
# @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License

: ${PHPUNIT:="phpunit"}
: ${PHPUNIT_OPTS:="--verbose"}
: ${PHPUNIT_GROUPS:=}

while [ -n "$1" ] ; do
  case "$1" in 
    -h|--html)
      PHPUNIT_COVERAGE="--coverage-html $2" 
      shift 2 ;;

    -c|--clover)
      PHPUNIT_COVERAGE="--coverage-clover $2" 
      shift 2 ;;

    ALL|all|MAX|max)
     PHPUNIT_GROUPS="" 
     break ;;

    Akismet|Amazon|Amazon_Ec2|Amazon_S3|Amazon_Sqs|Audioscrobbler|Delicious|Flickr|GoGrid|LiveDocx|Nirvanix|Rackspace|ReCaptcha|Simpy|SlideShare|StrikeIron|Technorati|Twitter|WindowsAzure|Yahoo)
     PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_Service_$1" 
     shift ;;
    Ec2|S3)
     PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_Service_Amazon_$1"
     shift ;;

    Search)
     PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_Search_Lucene" 
     shift ;;

    Zend*)
     PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}$1" 
     shift ;;

    *)
     PHPUNIT_GROUPS="${PHPUNIT_GROUPS:+"$PHPUNIT_GROUPS,"}Zend_$1" 
     shift ;;
  esac
done

set -x
${PHPUNIT} ${PHPUNIT_OPTS} ${PHPUNIT_COVERAGE} ${PHPUNIT_DB} \
  ${PHPUNIT_GROUPS:+--group $PHPUNIT_GROUPS}
