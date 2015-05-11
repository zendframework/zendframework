![Logo](https://raw.githubusercontent.com/zendframework/zf2/234b554f2ca202095aea32e4fa557553f8849664/resources/ZendFramework-logo.png)

# Welcome to the *Zend Framework 2.4* Release!

Master:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.svg?branch=master)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.svg?branch=master)](https://coveralls.io/r/zendframework/zf2)
Develop:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.svg?branch=develop)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.svg?branch=develop)](https://coveralls.io/r/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.4.2*

This is the second maintenance release in the version 2.4 series.

11 May 2015

### UPDATES IN 2.4.2

This release fixes issues introduced when correcting
[ZF2015-04](http://framework.zend.com/security/advisory/ZF2015-04), including:

- [#7506](https://github.com/zendframework/zf2/pull/7506) resolves issues when UTF-8 values are used in Mail headers, particularly addresses.
- [#7507](https://github.com/zendframework/zf2/pull/7507) ensures that array values can be used with cookies.
- [#7514](https://github.com/zendframework/zf2/pull/7514) ensures that multipart MIME messages can be added to `Zend\Mail\Message` instances in such a way that they do not conflict with ZF2015-04.

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
