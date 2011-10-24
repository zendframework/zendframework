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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Project\Context\Zf;

use Zend\Code\Generator\FileGenerator,
    Zend\Tool\Project\Context\Filesystem\File as FileContext;

/**
 * Generates and manages PHPUnit test bootstrap files
 *
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TestPHPUnitBootstrapFile extends FileContext
{
    /**
     * @var string
     */
    protected $_filesystemName = 'bootstrap.php';

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'TestPHPUnitBootstrapFile';
    }
   
    /**
     * getContents()
     *
     * @return string
     */
    public function getContents()
    {
        $codeGenerator = new FileGenerator();
        $codeGenerator->setBody( <<<EOS
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Loader/StandardAutoloader.php';
\$loader = new Zend\Loader\StandardAutoloader(array(
    'fallback_autoloader' => true,
))
\$loader->register();

EOS
            );
        return $codeGenerator->generate();
    }

}
