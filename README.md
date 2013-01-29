### Welcome to the *Zend Framework 2.1* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
Develop: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.1.0*

This is the first minor (feature) release for the version 2 series.

29 Jan 2013

### UPDATES IN 2.1.0

#### Backwards Compatibility Break: Session Storage

The default session storage object has changed from
`Zend\Session\Storage\SessionStorage` to an array adapter
(`Zend\Session\Storage\SessionArrayStorage`; this is a minimal break in
compatibility.  Most developers are not working directly with the storage
object, but rather a `Zend\Session\Container`; as a result, switching out the
default will not cause compatibility problems for most developers.

The change was introduced to alleviate issues when working with 3rd party
libraries that access nested members of the `$_SESSION` superglobal.

Those affected will be those (a) directly accessing the storage instance, and
(b) using object notation to access session members:

```php
    $foo = null;

    /** @var $storage Zend\Session\Storage\SessionStorage */
    if (isset($storage->foo)) {
        $foo = $storage->foo;
    }
```

If you are using array notation, as in the following example, your code remains
forwards compatible:

```php
    $foo = null;

    /** @var $storage Zend\Session\Storage\SessionStorage */
    if (isset($storage['foo'])) {
        $foo = $storage['foo'];
    }
```

If you are not working directly with the storage instance, you will be
unaffected.

For those affected, the following courses of action are possible:

- Update your code to replace object property notation with array notation, OR
- Initialize and register a `Zend\Session\Storage\SessionStorage` object
  explicitly with the session manager instance.

#### Other updates in 2.1.0

Please see CHANGELOG.md.

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see INSTALL.md.

### CONTRIBUTING

If you wish to contribute to Zend Framework, please read both the
CONTRIBUTING.md and README-GIT.md file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/archives/subscribe/

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in our GitHub
issue tracker:

https://github.com/zendframework/zf2/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
<fw-announce-subscribe@lists.zend.com>.

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
