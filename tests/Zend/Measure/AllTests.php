<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Measure
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Measure_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

require_once 'Zend/Measure/Cooking/VolumeTest.php';
require_once 'Zend/Measure/Cooking/WeightTest.php';

require_once 'Zend/Measure/Flow/MassTest.php';
require_once 'Zend/Measure/Flow/MoleTest.php';
require_once 'Zend/Measure/Flow/VolumeTest.php';

require_once 'Zend/Measure/Viscosity/DynamicTest.php';
require_once 'Zend/Measure/Viscosity/KinematicTest.php';
require_once 'Zend/Measure/AccelerationTest.php';
require_once 'Zend/Measure/AngleTest.php';
require_once 'Zend/Measure/AreaTest.php';
require_once 'Zend/Measure/BinaryTest.php';
require_once 'Zend/Measure/CapacitanceTest.php';
require_once 'Zend/Measure/CurrentTest.php';
require_once 'Zend/Measure/DensityTest.php';
require_once 'Zend/Measure/EnergyTest.php';
require_once 'Zend/Measure/ForceTest.php';
require_once 'Zend/Measure/FrequencyTest.php';
require_once 'Zend/Measure/IlluminationTest.php';
require_once 'Zend/Measure/LengthTest.php';
require_once 'Zend/Measure/LightnessTest.php';
require_once 'Zend/Measure/NumberTest.php';
require_once 'Zend/Measure/PowerTest.php';
require_once 'Zend/Measure/PressureTest.php';
require_once 'Zend/Measure/SpeedTest.php';
require_once 'Zend/Measure/TemperatureTest.php';
require_once 'Zend/Measure/TimeTest.php';
require_once 'Zend/Measure/TorqueTest.php';
require_once 'Zend/Measure/VolumeTest.php';
require_once 'Zend/Measure/WeightTest.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

class Zend_Measure_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Measure');

        $suite->addTestSuite('Zend_Measure_Cooking_VolumeTest');
        $suite->addTestSuite('Zend_Measure_Cooking_WeightTest');

        $suite->addTestSuite('Zend_Measure_Flow_MassTest');
        $suite->addTestSuite('Zend_Measure_Flow_MoleTest');
        $suite->addTestSuite('Zend_Measure_Flow_VolumeTest');

        $suite->addTestSuite('Zend_Measure_Viscosity_DynamicTest');
        $suite->addTestSuite('Zend_Measure_Viscosity_KinematicTest');

        $suite->addTestSuite('Zend_Measure_AccelerationTest');
        $suite->addTestSuite('Zend_Measure_AngleTest');
        $suite->addTestSuite('Zend_Measure_AreaTest');
        $suite->addTestSuite('Zend_Measure_BinaryTest');
        $suite->addTestSuite('Zend_Measure_CapacitanceTest');
        $suite->addTestSuite('Zend_Measure_CurrentTest');
        $suite->addTestSuite('Zend_Measure_DensityTest');
        $suite->addTestSuite('Zend_Measure_EnergyTest');
        $suite->addTestSuite('Zend_Measure_ForceTest');
        $suite->addTestSuite('Zend_Measure_FrequencyTest');
        $suite->addTestSuite('Zend_Measure_IlluminationTest');
        $suite->addTestSuite('Zend_Measure_LengthTest');
        $suite->addTestSuite('Zend_Measure_LightnessTest');
        $suite->addTestSuite('Zend_Measure_NumberTest');
        $suite->addTestSuite('Zend_Measure_PowerTest');
        $suite->addTestSuite('Zend_Measure_PressureTest');
        $suite->addTestSuite('Zend_Measure_SpeedTest');
        $suite->addTestSuite('Zend_Measure_TemperatureTest');
        $suite->addTestSuite('Zend_Measure_TimeTest');
        $suite->addTestSuite('Zend_Measure_TorqueTest');
        $suite->addTestSuite('Zend_Measure_VolumeTest');
        $suite->addTestSuite('Zend_Measure_WeightTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Measure_AllTests::main') {
    Zend_Measure_AllTests::main();
}
