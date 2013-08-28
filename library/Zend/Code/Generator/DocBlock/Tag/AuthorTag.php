<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Generator\DocBlock\Tag;

use Zend\Code\Generator\AbstractGenerator;
use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionTagInterface;

class AuthorTag extends AbstractGenerator implements TagInterface
{
    /**
     * @var string
     */
    protected $authorName = null;

    /**
     * @var string
     */
    protected $authorEmail = null;

    /**
     * @param  ReflectionTagInterface $reflectionTagReturn
     * @return ReturnTag
     * @deprecated Use TagManager::createTag() instead
     */
    public static function fromReflection(ReflectionTagInterface $reflectionTagReturn)
    {
        // @todo TagManager
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'author';
    }

    /**
     * @param string $authorEmail
     * @return AuthorTag
     */
    public function setAuthorEmail($authorEmail)
    {
        $this->authorEmail = $authorEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorEmail()
    {
        return $this->authorEmail;
    }

    /**
     * @param string $authorName
     * @return AuthorTag
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $output = '@author'
            . (!empty($this->authorName)) ? ' ' . $this->authorName : ''
            . (!empty($this->authorEmail)) ? ' <' . $this->authorEmail . '>' : '';

        return $output;
    }

}
