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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper\Navigation;
use Zend\Navigation,
    Zend\Navigation\AbstractPage,
    Zend,
    Zend\Acl,
    Zend\View;

/**
 * Base class for navigational helpers
 *
 * @uses       RecursiveIteratorIterator
 * @uses       \Zend\Navigation\Navigation
 * @uses       \Zend\Registry
 * @uses       \Zend\View\Exception
 * @uses       \Zend\View\Helper\HtmlElement
 * @uses       \Zend\View\Helper\Navigation\Helper
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHelper
    extends View\Helper\HtmlElement
    implements Helper
{
    /**
     * Container to operate on by default
     *
     * @var \Zend\Navigation\Container
     */
    protected $_container;

    /**
     * The minimum depth a page must have to be included when rendering
     *
     * @var int
     */
    protected $_minDepth;

    /**
     * The maximum depth a page can have to be included when rendering
     *
     * @var int
     */
    protected $_maxDepth;

    /**
     * Indentation string
     *
     * @var string
     */
    protected $_indent = '';

    /**
     * Translator
     *
     * @var \Zend\Translator\Adapter
     */
    protected $_translator;

    /**
     * ACL to use when iterating pages
     *
     * @var \Zend\Acl\Acl
     */
    protected $_acl;

    /**
     * Wheter invisible items should be rendered by this helper
     *
     * @var bool
     */
    protected $_renderInvisible = false;

    /**
     * ACL role to use when iterating pages
     *
     * @var string|\Zend\Acl\Role
     */
    protected $_role;

    /**
     * Whether translator should be used for page labels and titles
     *
     * @var bool
     */
    protected $_useTranslator = true;

    /**
     * Whether ACL should be used for filtering out pages
     *
     * @var bool
     */
    protected $_useAcl = true;

    /**
     * Default ACL to use when iterating pages if not explicitly set in the
     * instance by calling {@link setAcl()}
     *
     * @var \Zend\Acl\Acl
     */
    protected static $_defaultAcl;

    /**
     * Default ACL role to use when iterating pages if not explicitly set in the
     * instance by calling {@link setRole()}
     *
     * @var string|\Zend\Acl\Role
     */
    protected static $_defaultRole;

    // Accessors:

    /**
     * Sets navigation container the helper operates on by default
     *
     * Implements {@link Zend_View_Helper_Navigation_Interface::setContainer()}.
     *
     * @param  \Zend\Navigation\Container $container        [optional] container
     *                                                     to operate on.
     *                                                     Default is null,
     *                                                     meaning container
     *                                                     will be reset.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setContainer(Navigation\Container $container = null)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Returns the navigation container helper operates on by default
     *
     * Implements {@link Zend_View_Helper_Navigation_Interface::getContainer()}.
     *
     * If a helper is not explicitly set in this helper instance by calling
     * {@link setContainer()} or by passing it through the helper entry point,
     * this method will look in {@link Zend_Registry} for a container by using
     * the key 'Zend_Navigation'.
     *
     * If no container is set, and nothing is found in Zend_Registry, a new
     * container will be instantiated and stored in the helper.
     *
     * @return \Zend\Navigation\Container  navigation container
     */
    public function getContainer()
    {
        if (null === $this->_container) {
            // try to fetch from registry first
            if (\Zend\Registry::isRegistered('Zend_Navigation')) {
                $nav = \Zend\Registry::get('Zend_Navigation');
                if ($nav instanceof Navigation\Container) {
                    return $this->_container = $nav;
                }
            }

            // nothing found in registry, create new container
            $this->_container = new \Zend\Navigation\Navigation();
        }

        return $this->_container;
    }

    /**
     * Sets the minimum depth a page must have to be included when rendering
     *
     * @param  int $minDepth                               [optional] minimum
     *                                                     depth. Default is
     *                                                     null, which sets
     *                                                     no minimum depth.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setMinDepth($minDepth = null)
    {
        if (null === $minDepth || is_int($minDepth)) {
            $this->_minDepth = $minDepth;
        } else {
            $this->_minDepth = (int) $minDepth;
        }
        return $this;
    }

    /**
     * Returns minimum depth a page must have to be included when rendering
     *
     * @return int|null  minimum depth or null
     */
    public function getMinDepth()
    {
        if (!is_int($this->_minDepth) || $this->_minDepth < 0) {
            return 0;
        }
        return $this->_minDepth;
    }

    /**
     * Sets the maximum depth a page can have to be included when rendering
     *
     * @param  int $maxDepth                               [optional] maximum
     *                                                     depth. Default is
     *                                                     null, which sets no
     *                                                     maximum depth.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setMaxDepth($maxDepth = null)
    {
        if (null === $maxDepth || is_int($maxDepth)) {
            $this->_maxDepth = $maxDepth;
        } else {
            $this->_maxDepth = (int) $maxDepth;
        }
        return $this;
    }

    /**
     * Returns maximum depth a page can have to be included when rendering
     *
     * @return int|null  maximum depth or null
     */
    public function getMaxDepth()
    {
        return $this->_maxDepth;
    }

    /**
     * Set the indentation string for using in {@link render()}, optionally a
     * number of spaces to indent with
     *
     * @param  string|int $indent                          indentation string or
     *                                                     number of spaces
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setIndent($indent)
    {
        $this->_indent = $this->_getWhitespace($indent);
        return $this;
    }

    /**
     * Returns indentation
     *
     * @return string
     */
    public function getIndent()
    {
        return $this->_indent;
    }

    /**
     * Sets translator to use in helper
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::setTranslator()}.
     *
     * @param  mixed $translator                           [optional] translator.
     *                                                     Expects an object of
     *                                                     type
     *                                                     {@link Zend_Translator_Adapter}
     *                                                     or {@link Zend_Translator},
     *                                                     or null. Default is
     *                                                     null, which sets no
     *                                                     translator.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setTranslator($translator = null)
    {
        if (null == $translator ||
            $translator instanceof \Zend\Translator\Adapter\AbstractAdapter) {
            $this->_translator = $translator;
        } elseif ($translator instanceof \Zend\Translator\Translator) {
            $this->_translator = $translator->getAdapter();
        }

        return $this;
    }

    /**
     * Returns translator used in helper
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::getTranslator()}.
     *
     * @return \Zend\Translator\Adapter\Adapter|null  translator or null
     */
    public function getTranslator()
    {
        if (null === $this->_translator) {
            if (\Zend\Registry::isRegistered('Zend_Translator')) {
                $this->setTranslator(\Zend\Registry::get('Zend_Translator'));
            }
        }

        return $this->_translator;
    }

    /**
     * Sets ACL to use when iterating pages
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::setAcl()}.
     *
     * @param  \Zend\Acl\Acl $acl                               [optional] ACL object.
     *                                                     Default is null.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setAcl(Acl\Acl $acl = null)
    {
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Returns ACL or null if it isn't set using {@link setAcl()} or
     * {@link setDefaultAcl()}
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::getAcl()}.
     *
     * @return \Zend\Acl\Acl|null  ACL object or null
     */
    public function getAcl()
    {
        if ($this->_acl === null && self::$_defaultAcl !== null) {
            return self::$_defaultAcl;
        }

        return $this->_acl;
    }

    /**
     * Sets ACL role(s) to use when iterating pages
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::setRole()}.
     *
     * @param  mixed $role                                 [optional] role to
     *                                                     set. Expects a string,
     *                                                     an instance of type
     *                                                     {@link Zend_Acl_Role_Interface},
     *                                                     or null. Default is
     *                                                     null, which will set
     *                                                     no role.
     * @throws \Zend\View\Exception                         if $role is invalid
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setRole($role = null)
    {
        if (null === $role || is_string($role) ||
            $role instanceof Acl\Role) {
            $this->_role = $role;
        } else {
            $e = new View\Exception(sprintf(
                '$role must be a string, null, or an instance of ' 
                .  'Zend_Acl_Role_Interface; %s given',
                gettype($role)
            ));
            $e->setView($this->view);
            throw $e;
        }

        return $this;
    }

    /**
     * Returns ACL role to use when iterating pages, or null if it isn't set
     * using {@link setRole()} or {@link setDefaultRole()}
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::getRole()}.
     *
     * @return string|\Zend\Acl\Role|null  role or null
     */
    public function getRole()
    {
        if ($this->_role === null && self::$_defaultRole !== null) {
            return self::$_defaultRole;
        }

        return $this->_role;
    }

    /**
     * Sets whether ACL should be used
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::setUseAcl()}.
     *
     * @param  bool $useAcl                                [optional] whether ACL
     *                                                     should be used.
     *                                                     Default is true.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setUseAcl($useAcl = true)
    {
        $this->_useAcl = (bool) $useAcl;
        return $this;
    }

    /**
     * Returns whether ACL should be used
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::getUseAcl()}.
     *
     * @return bool  whether ACL should be used
     */
    public function getUseAcl()
    {
        return $this->_useAcl;
    }

    /**
     * Return renderInvisible flag
     *
     * @return bool
     */
    public function getRenderInvisible()
    {
        return $this->_renderInvisible;
    }

    /**
     * Render invisible items?
     *
     * @param  bool $renderInvisible                       [optional] boolean flag
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface
     *                                                     returns self
     */
    public function setRenderInvisible($renderInvisible = true)
    {
        $this->_renderInvisible = (bool) $renderInvisible;
        return $this;
    }

    /**
     * Sets whether translator should be used
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::setUseTranslator()}.
     *
     * @param  bool $useTranslator                         [optional] whether
     *                                                     translator should be
     *                                                     used. Default is true.
     * @return \Zend\View\Helper\Navigation\AbstractHelper  fluent interface,
     *                                                     returns self
     */
    public function setUseTranslator($useTranslator = true)
    {
        $this->_useTranslator = (bool) $useTranslator;
        return $this;
    }

    /**
     * Returns whether translator should be used
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::getUseTranslator()}.
     *
     * @return bool  whether translator should be used
     */
    public function getUseTranslator()
    {
        return $this->_useTranslator;
    }

    // Magic overloads:

    /**
     * Magic overload: Proxy calls to the navigation container
     *
     * @param  string $method             method name in container
     * @param  array  $arguments          [optional] arguments to pass
     * @return mixed                      returns what the container returns
     * @throws \Zend\Navigation\Exception  if method does not exist in container
     */
    public function __call($method, array $arguments = array())
    {
        return call_user_func_array(
                array($this->getContainer(), $method),
                $arguments);
    }

    /**
     * Magic overload: Proxy to {@link render()}.
     *
     * This method will trigger an E_USER_ERROR if rendering the helper causes
     * an exception to be thrown.
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::__toString()}.
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (\Exception $e) {
            $msg = get_class($e) . ': ' . $e->getMessage();
            trigger_error($msg, E_USER_ERROR);
            return '';
        }
    }

    // Public methods:

    /**
     * Finds the deepest active page in the given container
     *
     * @param  \Zend\Navigation\Container $container  container to search
     * @param  int|null                  $minDepth   [optional] minimum depth
     *                                               required for page to be
     *                                               valid. Default is to use
     *                                               {@link getMinDepth()}. A
     *                                               null value means no minimum
     *                                               depth required.
     * @param  int|null                  $minDepth   [optional] maximum depth
     *                                               a page can have to be
     *                                               valid. Default is to use
     *                                               {@link getMaxDepth()}. A
     *                                               null value means no maximum
     *                                               depth required.
     * @return array                                 an associative array with
     *                                               the values 'depth' and
     *                                               'page', or an empty array
     *                                               if not found
     */
    public function findActive(Navigation\Container $container,
                               $minDepth = null,
                               $maxDepth = -1)
    {
        if (!is_int($minDepth)) {
            $minDepth = $this->getMinDepth();
        }
        if ((!is_int($maxDepth) || $maxDepth < 0) && null !== $maxDepth) {
            $maxDepth = $this->getMaxDepth();
        }

        $found  = null;
        $foundDepth = -1;
        $iterator = new \RecursiveIteratorIterator($container,
                \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($iterator as $page) {
            $currDepth = $iterator->getDepth();
            if ($currDepth < $minDepth || !$this->accept($page)) {
                // page is not accepted
                continue;
            }

            if ($page->isActive(false) && $currDepth > $foundDepth) {
                // found an active page at a deeper level than before
                $found = $page;
                $foundDepth = $currDepth;
            }
        }

        if (is_int($maxDepth) && $foundDepth > $maxDepth) {
            while ($foundDepth > $maxDepth) {
                if (--$foundDepth < $minDepth) {
                    $found = null;
                    break;
                }

                $found = $found->getParent();
                if (!$found instanceof AbstractPage) {
                    $found = null;
                    break;
                }
            }
        }

        if ($found) {
            return array('page' => $found, 'depth' => $foundDepth);
        } else {
            return array();
        }
    }

    /**
     * Checks if the helper has a container
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::hasContainer()}.
     *
     * @return bool  whether the helper has a container or not
     */
    public function hasContainer()
    {
        return null !== $this->_container;
    }

    /**
     * Checks if the helper has an ACL instance
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::hasAcl()}.
     *
     * @return bool  whether the helper has a an ACL instance or not
     */
    public function hasAcl()
    {
        return null !== $this->_acl;
    }

    /**
     * Checks if the helper has an ACL role
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::hasRole()}.
     *
     * @return bool  whether the helper has a an ACL role or not
     */
    public function hasRole()
    {
        return null !== $this->_role;
    }

    /**
     * Checks if the helper has a translator
     *
     * Implements {@link Zend\View\Helper\Navigation\Helper::hasTranslator()}.
     *
     * @return bool  whether the helper has a translator or not
     */
    public function hasTranslator()
    {
        return null !== $this->_translator;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page
     *
     * @param  \Zend\Navigation\AbstractPage $page  page to generate HTML for
     * @return string                      HTML string for the given page
     */
    public function htmlify(AbstractPage $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for anchor element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass(),
            'href'   => $page->getHref(),
            'target' => $page->getTarget()
        );

        return '<a' . $this->_htmlAttribs($attribs) . '>'
             . $this->view->vars()->escape($label)
             . '</a>';
    }

    // Iterator filter methods:

    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible it is not accepted, unless RenderInvisible has
     *   been set to true.
     * - If helper has no ACL, page is accepted
     * - If helper has ACL, but no role, page is not accepted
     * - If helper has ACL and role:
     *  - Page is accepted if it has no resource or privilege
     *  - Page is accepted if ACL allows page's resource or privilege
     * - If page is accepted by the rules above and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page.
     *
     * @param  \Zend\Navigation\AbstractPage $page      page to check
     * @param  bool                $recursive  [optional] if true, page will not
     *                                         be accepted if it is the
     *                                         descendant of a page that is not
     *                                         accepted. Default is true.
     * @return bool                            whether page should be accepted
     */
    public function accept(AbstractPage $page, $recursive = true)
    {
        // accept by default
        $accept = true;

        if (!$page->isVisible(false) && !$this->getRenderInvisible()) {
            // don't accept invisible pages
            $accept = false;
        } elseif ($this->getUseAcl() && !$this->_acceptAcl($page)) {
            // acl is not amused
            $accept = false;
        }

        if ($accept && $recursive) {
            $parent = $page->getParent();
            if ($parent instanceof AbstractPage) {
                $accept = $this->accept($parent, true);
            }
        }

        return $accept;
    }

    /**
     * Determines whether a page should be accepted by ACL when iterating
     *
     * Rules:
     * - If helper has no ACL, page is accepted
     * - If page has a resource or privilege defined, page is accepted
     *   if the ACL allows access to it using the helper's role
     * - If page has no resource or privilege, page is accepted
     *
     * @param  \Zend\Navigation\AbstractPage $page  page to check
     * @return bool                        whether page is accepted by ACL
     */
    protected function _acceptAcl(AbstractPage $page)
    {
        if (!$acl = $this->getAcl()) {
            // no acl registered means don't use acl
            return true;
        }

        $role = $this->getRole();
        $resource = $page->getResource();
        $privilege = $page->getPrivilege();

        if ($resource || $privilege) {
            // determine using helper role and page resource/privilege
            return $acl->isAllowed($role, $resource, $privilege);
        }

        return true;
    }

    // Util methods:

    /**
     * Retrieve whitespace representation of $indent
     *
     * @param  int|string $indent
     * @return string
     */
    protected function _getWhitespace($indent)
    {
        if (is_int($indent)) {
            $indent = str_repeat(' ', $indent);
        }

        return (string) $indent;
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * Overloads {@link Zend_View_Helper_HtmlElement::_htmlAttribs()}.
     *
     * @param  array $attribs  an array where each key-value pair is converted
     *                         to an attribute name and value
     * @return string          an attribute string
     */
    protected function _htmlAttribs($attribs)
    {
        // filter out null values and empty string values
        foreach ($attribs as $key => $value) {
            if ($value === null || (is_string($value) && !strlen($value))) {
                unset($attribs[$key]);
            }
        }

        return parent::_htmlAttribs($attribs);
    }

    /**
     * Normalize an ID
     *
     * Overrides {@link Zend_View_Helper_HtmlElement::_normalizeId()}.
     *
     * @param  string $value
     * @return string
     */
    protected function _normalizeId($value)
    {
        $prefix = get_class($this);
        $prefix = strtolower(trim(substr($prefix, strrpos($prefix, '\\')), '\\'));

        return $prefix . '-' . $value;
    }

    // Static methods:

    /**
     * Sets default ACL to use if another ACL is not explicitly set
     *
     * @param  \Zend\Acl\Acl $acl  [optional] ACL object. Default is null, which
     *                        sets no ACL object.
     * @return void
     */
    public static function setDefaultAcl(Acl\Acl $acl = null)
    {
        self::$_defaultAcl = $acl;
    }

    /**
     * Sets default ACL role(s) to use when iterating pages if not explicitly
     * set later with {@link setRole()}
     *
     * @param  midex $role               [optional] role to set. Expects null,
     *                                   string, or an instance of
     *                                   {@link Zend_Acl_Role_Interface}.
     *                                   Default is null, which sets no default
     *                                   role.
     * @throws \Zend\View\Exception       if role is invalid
     * @return void
     */
    public static function setDefaultRole($role = null)
    {
        if (null === $role ||
            is_string($role) ||
            $role instanceof Acl\Role) {
            self::$_defaultRole = $role;
        } else {
            throw new View\Exception(
                '$role must be null|string|Zend_Acl_Role_Interface'
            );
        }
    }
}
