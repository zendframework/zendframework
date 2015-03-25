<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Paginator\Adapter;

class Null extends NullFill
{
    /**
     * {@inheritdoc}
     */
    public function __construct($count = 0)
    {
        trigger_error(
            sprintf(
                'The class %s has been deprecated; please use %s\\NullFill',
                __CLASS__,
                __NAMESPACE__
            ),
            E_USER_DEPRECATED
        );

        parent::__construct($count);
    }
}
