<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper\Navigation;

use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View;
use Zend\View\Exception;

/**
 * Helper for rendering menus from navigation containers
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class Menu extends AbstractHelper
{
    /**
     * CSS class to use for the ul element
     *
     * @var string
     */
    protected $ulClass = 'navigation';

    /**
     * Whether only active branch should be rendered
     *
     * @var bool
     */
    protected $onlyActiveBranch = false;

    /**
     * Whether labels should be escaped
     *
     * @var bool
     */
    protected $escapeLabels = true;

    /**
     * Whether parents should be rendered when only rendering active branch
     *
     * @var bool
     */
    protected $renderParents = true;

    /**
     * Partial view script to use for rendering menu
     *
     * @var string|array
     */
    protected $partial = null;

    /**
     * View helper entry point:
     * Retrieves helper and optionally sets container to operate on
     *
     * @param  AbstractContainer $container [optional] container to operate on
     * @return Menu      fluent interface, returns self
     */
    public function __invoke($container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }

    /**
     * Sets CSS class to use for the first 'ul' element when rendering
     *
     * @param  string $ulClass CSS class to set
     * @return Menu  fluent interface, returns self
     */
    public function setUlClass($ulClass)
    {
        if (is_string($ulClass)) {
            $this->ulClass = $ulClass;
        }

        return $this;
    }

    /**
     * Returns CSS class to use for the first 'ul' element when rendering
     *
     * @return string  CSS class
     */
    public function getUlClass()
    {
        return $this->ulClass;
    }

    /**
     * Sets a flag indicating whether only active branch should be rendered
     *
     * @param  bool $flag [optional] render only active branch. Default is true.
     * @return Menu  fluent interface, returns self
     */
    public function setOnlyActiveBranch($flag = true)
    {
        $this->onlyActiveBranch = (bool) $flag;
        return $this;
    }

    /**
     * Returns a flag indicating whether only active branch should be rendered
     *
     * By default, this value is false, meaning the entire menu will be
     * be rendered.
     *
     * @return bool  whether only active branch should be rendered
     */
    public function getOnlyActiveBranch()
    {
        return $this->onlyActiveBranch;
    }

    /**
     * Sets a flag indicating whether labels should be escaped
     *
     * @param bool $flag [optional] escape labels. Default is true.
     * @return Menu  fluent interface, returns self
     */
    public function escapeLabels($flag = true)
    {
        $this->escapeLabels = (bool) $flag;
        return $this;
    }

    /**
     * Enables/disables rendering of parents when only rendering active branch
     *
     * See {@link setOnlyActiveBranch()} for more information.
     *
     * @param  bool $flag [optional] render parents when rendering active branch.
     *                    Default is true.
     * @return Menu  fluent interface, returns self
     */
    public function setRenderParents($flag = true)
    {
        $this->renderParents = (bool) $flag;
        return $this;
    }

    /**
     * Returns flag indicating whether parents should be rendered when rendering
     * only the active branch
     *
     * By default, this value is true.
     *
     * @return bool  whether parents should be rendered
     */
    public function getRenderParents()
    {
        return $this->renderParents;
    }

    /**
     * Sets which partial view script to use for rendering menu
     *
     * @param  string|array $partial partial view script or null. If an array is
     *                               given, it is expected to contain two
     *                               values; the partial view script to use,
     *                               and the module where the script can be
     *                               found.
     * @return Menu  fluent interface, returns self
     */
    public function setPartial($partial)
    {
        if (null === $partial || is_string($partial) || is_array($partial)) {
            $this->partial = $partial;
        }

        return $this;
    }

    /**
     * Returns partial view script to use for rendering menu
     *
     * @return string|array|null
     */
    public function getPartial()
    {
        return $this->partial;
    }

    // Public methods:

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param  AbstractPage $page   page to generate HTML for
     * @param bool $escapeLabel     Whether or not to escape the label
     * @return string               HTML string for the given page
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        // translate label and title?
        if (null !== ($translator = $this->getTranslator())) {
            $textDomain = $this->getTranslatorTextDomain();
            if (is_string($label) && !empty($label)) {
                $label = $translator->translate($label, $textDomain);
            }
            if (is_string($title) && !empty($title)) {
                $title = $translator->translate($title, $textDomain);
            }
        }

        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $title,
            'class'  => $page->getClass()
        );

        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        $html = '<' . $element . $this->htmlAttribs($attribs) . '>';
        if ($escapeLabel === true) {
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }
        $html .= '</' . $element . '>';

        return $html;
    }

    /**
     * Normalizes given render options
     *
     * @param  array $options  [optional] options to normalize
     * @return array           normalized options
     */
    protected function normalizeOptions(array $options = array())
    {
        if (isset($options['indent'])) {
            $options['indent'] = $this->getWhitespace($options['indent']);
        } else {
            $options['indent'] = $this->getIndent();
        }

        if (isset($options['ulClass']) && $options['ulClass'] !== null) {
            $options['ulClass'] = (string) $options['ulClass'];
        } else {
            $options['ulClass'] = $this->getUlClass();
        }

        if (array_key_exists('minDepth', $options)) {
            if (null !== $options['minDepth']) {
                $options['minDepth'] = (int) $options['minDepth'];
            }
        } else {
            $options['minDepth'] = $this->getMinDepth();
        }

        if ($options['minDepth'] < 0 || $options['minDepth'] === null) {
            $options['minDepth'] = 0;
        }

        if (array_key_exists('maxDepth', $options)) {
            if (null !== $options['maxDepth']) {
                $options['maxDepth'] = (int) $options['maxDepth'];
            }
        } else {
            $options['maxDepth'] = $this->getMaxDepth();
        }

        if (!isset($options['onlyActiveBranch'])) {
            $options['onlyActiveBranch'] = $this->getOnlyActiveBranch();
        }

        if (!isset($options['escapeLabels'])) {
            $options['escapeLabels'] = $this->escapeLabels;
        }

        if (!isset($options['renderParents'])) {
            $options['renderParents'] = $this->getRenderParents();
        }

        return $options;
    }

    // Render methods:

    /**
     * Renders the deepest active menu within [$minDepth, $maxDepth], (called
     * from {@link renderMenu()})
     *
     * @param  AbstractContainer         $container  container to render
     * @param  array                     $active     active page and depth
     * @param  string                    $ulClass    CSS class for first UL
     * @param  string                    $indent     initial indentation
     * @param  int|null                  $minDepth   minimum depth
     * @param  int|null                  $maxDepth   maximum depth
     * @return string                                rendered menu
     */
    protected function renderDeepestMenu(AbstractContainer $container,
                                         $ulClass,
                                         $indent,
                                         $minDepth,
                                         $maxDepth,
                                         $escapeLabels
    ) {
        if (!$active = $this->findActive($container, $minDepth - 1, $maxDepth)) {
            return '';
        }

        // special case if active page is one below minDepth
        if ($active['depth'] < $minDepth) {
            if (!$active['page']->hasPages()) {
                return '';
            }
        } elseif (!$active['page']->hasPages()) {
            // found pages has no children; render siblings
            $active['page'] = $active['page']->getParent();
        } elseif (is_int($maxDepth) && $active['depth'] +1 > $maxDepth) {
            // children are below max depth; render siblings
            $active['page'] = $active['page']->getParent();
        }

        $ulClass = $ulClass ? ' class="' . $ulClass . '"' : '';
        $html = $indent . '<ul' . $ulClass . '>' . self::EOL;

        foreach ($active['page'] as $subPage) {
            if (!$this->accept($subPage)) {
                continue;
            }
            $liClass = $subPage->isActive(true) ? ' class="active"' : '';
            $html .= $indent . '    <li' . $liClass . '>' . self::EOL;
            $html .= $indent . '        ' . $this->htmlify($subPage, $escapeLabels) . self::EOL;
            $html .= $indent . '    </li>' . self::EOL;
        }

        $html .= $indent . '</ul>';

        return $html;
    }

    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  AbstractContainer                 $container   container to render
     * @param  string                    $ulClass     CSS class for first UL
     * @param  string                    $indent      initial indentation
     * @param  int|null                  $minDepth    minimum depth
     * @param  int|null                  $maxDepth    maximum depth
     * @param  bool                      $onlyActive  render only active branch?
     * @return string
     */
    protected function renderNormalMenu(AbstractContainer $container,
                                   $ulClass,
                                   $indent,
                                   $minDepth,
                                   $maxDepth,
                                   $onlyActive,
                                   $escapeLabels
    ) {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);
        if ($found) {
            $foundPage  = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
                            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } elseif ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages() ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClass = ' class="' . $ulClass . '"';
                } else {
                    $ulClass = '';
                }
                $html .= $myIndent . '<ul' . $ulClass . '>' . self::EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . self::EOL;
                    $html .= $ind . '</ul>' . self::EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . self::EOL;
            }

            // render li tag and page
            $liClass = $isActive ? ' class="active"' : '';
            $html .= $myIndent . '    <li' . $liClass . '>' . self::EOL
                   . $myIndent . '        ' . $this->htmlify($page, $escapeLabels) . self::EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . self::EOL
                       . $myIndent . '</ul>' . self::EOL;
            }
            $html = rtrim($html, self::EOL);
        }

        return $html;
    }

    /**
     * Renders helper
     *
     * Renders a HTML 'ul' for the given $container. If $container is not given,
     * the container registered in the helper will be used.
     *
     * Available $options:
     *
     *
     * @param  AbstractContainer $container [optional] container to create menu from.
     *                              Default is to use the container retrieved
     *                              from {@link getContainer()}.
     * @param  array     $options   [optional] options for controlling rendering
     * @return string    rendered menu
     */
    public function renderMenu($container = null, array $options = array())
    {
        $this->parseContainer($container);
        if (null === $container) {
            $container = $this->getContainer();
        }


        $options = $this->normalizeOptions($options);

        if ($options['onlyActiveBranch'] && !$options['renderParents']) {
            $html = $this->renderDeepestMenu($container,
                                              $options['ulClass'],
                                              $options['indent'],
                                              $options['minDepth'],
                                              $options['maxDepth'],
                                              $options['escapeLabels']);
        } else {
            $html = $this->renderNormalMenu($container,
                                       $options['ulClass'],
                                       $options['indent'],
                                       $options['minDepth'],
                                       $options['maxDepth'],
                                       $options['onlyActiveBranch'],
                                       $options['escapeLabels']);
        }

        return $html;
    }

    /**
     * Renders the inner-most sub menu for the active page in the $container
     *
     * This is a convenience method which is equivalent to the following call:
     * <code>
     * renderMenu($container, array(
     *     'indent'           => $indent,
     *     'ulClass'          => $ulClass,
     *     'minDepth'         => null,
     *     'maxDepth'         => null,
     *     'onlyActiveBranch' => true,
     *     'renderParents'    => false
     * ));
     * </code>
     *
     * @param  AbstractContainer                 $container  [optional] container to
     *                                               render. Default is to render
     *                                               the container registered in
     *                                               the helper.
     * @param  string                    $ulClass    [optional] CSS class to
     *                                               use for UL element. Default
     *                                               is to use the value from
     *                                               {@link getUlClass()}.
     * @param  string|int                $indent     [optional] indentation as
     *                                               a string or number of
     *                                               spaces. Default is to use
     *                                               the value retrieved from
     *                                               {@link getIndent()}.
     * @return string                                rendered content
     */
    public function renderSubMenu(AbstractContainer $container = null,
                                  $ulClass = null,
                                  $indent = null
    ) {
        return $this->renderMenu($container, array(
            'indent'           => $indent,
            'ulClass'          => $ulClass,
            'minDepth'         => null,
            'maxDepth'         => null,
            'onlyActiveBranch' => true,
            'renderParents'    => false,
            'escapeLabels'     => true
        ));
    }

    /**
     * Renders the given $container by invoking the partial view helper
     *
     * The container will simply be passed on as a model to the view script
     * as-is, and will be available in the partial script as 'container', e.g.
     * <code>echo 'Number of pages: ', count($this->container);</code>.
     *
     * @param  AbstractContainer     $container [optional] container to pass to view
     *                                  script. Default is to use the container
     *                                  registered in the helper.
     * @param  string|array  $partial   [optional] partial view script to use.
     *                                  Default is to use the partial
     *                                  registered in the helper. If an array
     *                                  is given, it is expected to contain two
     *                                  values; the partial view script to use,
     *                                  and the module where the script can be
     *                                  found.
     * @return string                   helper output
     * @throws Exception\RuntimeException if no partial provided
     * @throws Exception\InvalidArgumentException if partial is invalid array
     */
    public function renderPartial($container = null, $partial = null)
    {
        $this->parseContainer($container);
        if (null === $container) {
            $container = $this->getContainer();
        }

        if (null === $partial) {
            $partial = $this->getPartial();
        }

        if (empty($partial)) {
            throw new Exception\RuntimeException(
                'Unable to render menu: No partial view script provided'
            );
        }

        $model = array(
            'container' => $container
        );

        if (is_array($partial)) {
            if (count($partial) != 2) {
                throw new Exception\InvalidArgumentException(
                    'Unable to render menu: A view partial supplied as '
                    .  'an array must contain two values: partial view '
                    .  'script and module where script can be found'
                );
            }

            $partialHelper = $this->view->plugin('partial');
            return $partialHelper($partial[0], /*$partial[1], */$model);
        }

        $partialHelper = $this->view->plugin('partial');
        return $partialHelper($partial, $model);
    }

    // Zend\View\Helper\Navigation\Helper:

    /**
     * Renders menu
     *
     * Implements {@link HelperInterface::render()}.
     *
     * If a partial view is registered in the helper, the menu will be rendered
     * using the given partial script. If no partial is registered, the menu
     * will be rendered as an 'ul' element by the helper's internal method.
     *
     * @see renderPartial()
     * @see renderMenu()
     *
     * @param  AbstractContainer $container [optional] container to render. Default is
     *                              to render the container registered in the
     *                              helper.
     * @return string               helper output
     */
    public function render($container = null)
    {
        $partial = $this->getPartial();
        if ($partial) {
            return $this->renderPartial($container, $partial);
        }
        return $this->renderMenu($container);
    }
}
