### Welcome to the *Zend Framework 2.3* Release!

Master:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.png?branch=master)](https://coveralls.io/r/zendframework/zf2)
Develop:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.png?branch=develop)](https://coveralls.io/r/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.3.2*

This is the second maintenance release for the version 2.3 series.

12 Aug 2014

### UPDATES IN 2.3.2

Notable changes include:

- [#6295](https://github.com/zendframework/zf2/pull/6295) introduces a slight change to how `Zend\Form\Fieldset` handles disabled values. Previously, they were represented in the form, and still processed on submit, which allowed the possibility of changing the value. This pull request modifies the behavior to extract the original value from any bound data if present and use that value instead, which is the correct behavior.
- [#6423](https://github.com/zendframework/zf2/pull/6423) modifies the behavior of `Zend\Validator\File\UploadFile` to only return the `FILE_NOT_FOUND` error if upload was successful; previously, it incorrectly would report this error even if an error occurred during upload.

In all, over 120 issues were resolved for this release.

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.23 or later; we recommend using the
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

## Reporting Potential Security Issues

If you have encountered a potential security vulnerability in Zend Framework, please report it to us at [zf-security@zend.com](mailto:zf-security@zend.com). We will work with you to verify the vulnerability and patch it.

When reporting issues, please provide the following information:

- Component(s) affected
- A description indicating how to reproduce the issue
- A summary of the security vulnerability and impact

We request that you contact us via the email address above and give the project contributors a chance to resolve the vulnerability and issue a new release prior to any public exposure; this helps protect Zend Framework users and provides them with a chance to upgrade and/or update in order to protect their applications.

For sensitive email communications, please use [our PGP key](http://framework.zend.com/zf-security-pgp-key.asc).

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
