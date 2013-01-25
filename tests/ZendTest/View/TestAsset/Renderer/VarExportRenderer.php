<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\TestAsset\Renderer;

use Zend\View\Model\ModelInterface as Model;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 */
class VarExportRenderer implements Renderer
{
    public function getEngine()
    {
        return 'var_export';
    }

    public function setResolver(Resolver $resolver)
    {
        // Deliberately empty
    }

    public function render($nameOrModel, $values = null)
    {
        if (!$nameOrModel instanceof Model) {
            return var_export($nameOrModel, true);
        }

        $values = $nameOrModel->getVariables();
        return var_export($values, true);
    }
}
