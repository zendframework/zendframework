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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Tool\Project\Context\Zf;
use Zend\CodeGenerator\Php;

/**
 * This class is the front most class for utilizing Zend_Tool_Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\CodeGenerator\Php\PhpClass
 * @uses       \Zend\CodeGenerator\Php\PhpFile
 * @uses       \Zend\CodeGenerator\Php\PhpMethod
 * @uses       \Zend\Filter\Word\DashToCamelCase
 * @uses       \Zend\Tool\Project\Context\Filesystem\File
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TestLibraryFile extends \Zend\Tool\Project\Context\Filesystem\File
{

    /**
     * @var string
     */
    protected $_forClassName = '';

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'TestLibraryFile';
    }

    /**
     * init()
     *
     * @return \Zend\Tool\Project\Context\Zf\TestLibraryFile
     */
    public function init()
    {
        $this->_forClassName = $this->_resource->getAttribute('forClassName');
        $this->_filesystemName = ucfirst(ltrim(strrchr($this->_forClassName, '_'), '_')) . 'Test.php';
        parent::init();
        return $this;
    }

    /**
     * getContents()
     *
     * @return string
     */
    public function getContents()
    {

        $filter = new \Zend\Filter\Word\DashToCamelCase();

        $className = $filter->filter($this->_forClassName) . 'Test';

        $codeGenFile = new Php\PhpFile(array(
            'requiredFiles' => array(
                'PHPUnit/Framework/TestCase.php'
                ),
            'classes' => array(
                new Php\PhpClass(array(
                    'name' => $className,
                    'extendedClass' => 'PHPUnit_Framework_TestCase',
                    'methods' => array(
                        new Php\PhpMethod(array(
                            'name' => 'setUp',
                            'body' => '        /* Setup Routine */'
                            )),
                        new Php\PhpMethod(array(
                            'name' => 'tearDown',
                            'body' => '        /* Tear Down Routine */'
                            ))
                        )
                    ))
                )
            ));

        return $codeGenFile->generate();
    }

}
