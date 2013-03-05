### Welcome to the *Zend Framework 2.1* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
Develop: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.1.4dev*

This is the fourth maintenance release for the version 2.1 series.

DD MMM YYYY

### UPDATES IN 2.1.4

Better polyfill support in `Zend\Session` and `Zend\Stdlib`. Polyfills
(version-specific class replacements) have caused some issues in the 2.1 series.
In particular, users who were not using Composer were unaware/uncertain about
what extra files needed to be included to load polyfills, and those users who
were generating classmaps were running into issues since the same class was
being generated twice.

New polyfill support was created which does the following:

- A stub class file was created for each class needing polyfill support. A
  conditional is present in each that uses `class_alias` to alias an appropriate
  version-specific class to the class requested. A stub class is created in each
  stub following a `__halt_compiler()` directive to ensure that classmap
  generators will pick up the "class" and put it in the map.
- New, uniquely named classes were created for each polyfill.
- `Zend\File\ClassFileLocator` was altered to look for class definitions
  following a `halt_compiler()` directive.

The only issue discovered so far is that you cannot use PHPUnit's mock object
generator to mock a class that only exists via `class_alias`.

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see [INSTALL.md](INSTALL.md).

### CONTRIBUTING

If you wish to contribute to Zend Framework, please read both the
[CONTRIBUTING.md](CONTRIBUTING.md) and [README-GIT.md](README-GIT.md) file.

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
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
