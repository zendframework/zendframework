### Welcome to the *Zend Framework 2.3* Release!

Master:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.png?branch=master)](https://coveralls.io/r/zendframework/zf2)
Develop:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.png?branch=develop)](https://coveralls.io/r/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.3.0dev*

This is the third minor (feature) release for the version 2 series.

DD MMM YYY

### UPDATES IN 2.3.0

- [#5043](https://github.com/zendframework/zf2/pull/5043) introduced changes in
  how DocBlock tag instances are returned via the `Zend\Code\Reflection`
  component. These instances are rarely created manually; however, if you are
  doing so, please note the following API changes:
  - `Zend\Code\Generator\DocBlock\Tag\AuthorTag`: removed `set/getDatatype()` and
    `set/getParamName()`
  - `Zend\Code\Generator\DocBlock\Tag\AuthorTag`: `__construct` changed from
    `($options = array())` to `($authorName = null, $authorEmail = null)`
  - `Zend\Code\Generator\DocBlock\Tag\LicenseTag`: `__construct` changed from
    `($options = array())` to `($url = null, $licenseName = null)`
  - `Zend\Code\Generator\DocBlock\Tag\ReturnTag`: `__construct` changed from
    `($options = array())` to `($types = array(), $description = null)`
  - `Zend\Code\Generator\DocBlock\Tag\ParamTag`: `__construct` changed from
    `($options = array())` to `($variableName = null, $types = array(),
    $description = null)`
  - Using `DocBlockGenerator::fromReflection()` and afterwards `getTags()` is now
    returning the new `Tag` classes (`ReturnTag`, `AuthorTag`, `ParamTag`, ...)
    where applicable and otherwise `GenericTag`. The deprecated class `Tag` will
    not be returned anymore.
- [#5101](https://github.com/zendframework/zf2/pull/5101) introduces a behavior
  change in the FormLabel view helper: it now escapes the label content by
  default. If you wish to disable escaping, you need to either pass the label
  option `disable_html_escape` to the form element, or call the
  `setEscapeHtmlHelper(false)` method on the `formLabel()` view helper.
- [#4962](https://github.com/zendframework/zf2/pull/4962) adds a service alias
  from "ControllerManager" to "ControllerLoader", and updates code to reference

This is the sixth maintenance release for the 2.2 series.

DD MMM YYYY

### UPDATES IN 2.2.6
>>>>>>> version/bump

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
