<?php

namespace ZendTest\Db;

use Exception;

use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;

class IntegrationTestListener implements PHPUnit_Framework_TestListener
{

    protected $adapters = array(
        'mysqli' => null,
        'pdo_mysql' => null,
        'pgsql' => null,
        'pdo_pgsql' => null,
        'pdo_sqlite' => null,
    );

    public function __construct()
    {
        if (isset($GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'])) {
            if (extension_loaded('mysqli')) {
                $this->adapters['mysqli'] = new \mysqli(
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_USERNAME'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_PASSWORD'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE']
                );
            }
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_mysql'] = new \Pdo(
                    'mysql:host=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_HOSTNAME'] . ';dbname=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_DATABASE'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_USERNAME'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_MYSQL_PASSWORD']
                );
            }
        }
        if (isset($GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'])) {
            if (extension_loaded('pgsql')) {
                $this->adapters['pgsql'] = pg_connect(
                    'host=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME']
                        . ' dbname=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_DATABASE']
                        . ' user=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_USERNAME']
                        . ' password=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_PASSWORD']
                );
            }
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_pgsql'] = new \Pdo(
                    'pgsql:host=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_HOSTNAME'] . ';dbname=' . $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_DATABASE'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_USERNAME'],
                    $GLOBALS['ZEND_DB_ADAPTER_DRIVER_PGSQL_PASSWORD']
                );
            }
        }
        if (isset($GLOBALS['ZEND_DB_ADAPTER_DRIVER_SQLITE_MEMORY'])) {
            if (extension_loaded('pdo')) {
                $this->adapters['pdo_sqlite'] = new \Pdo(
                    'sqlite::memory:'
                );
            }
        }
    }

    /** methods required for the listener interface compliance */
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {}
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {}
    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}
    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time) {}
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite) {}
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {}

    /**
     * A test started.
     *
     * @param  PHPUnit_Framework_Test $test
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        /** @var $test \PHPUnit_Framework_TestCase */
        $testcase = get_class($test);
        if (strpos($testcase, 'ZendTest\Db') === 0 && strpos($testcase, 'Integration')) {
            $refObj = new \ReflectionObject($test);
            if ($refObj->hasProperty('adapters')) {
                $refProp = $refObj->getProperty('adapters');
                $refProp->setAccessible(true);
                $refProp->setValue($test, $this->adapters);
            }
        }
    }

    /**
     * A test ended.
     *
     * @param  PHPUnit_Framework_Test $test
     * @param  float                  $time
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        /** @var $test \PHPUnit_Framework_TestCase */
        $testcase = get_class($test);
        if (strpos($testcase, 'ZendTest\Db') === 0 && strpos($testcase, 'Integration')) {
            $refObj = new \ReflectionObject($test);
            if ($refObj->hasProperty('adapters')) {
                $refProp = $refObj->getProperty('adapters');
                $refProp->setAccessible(true);
                $refProp->setValue($test, array());
            }
        }
    }
}