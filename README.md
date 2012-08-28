### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0rc6*

This is the fifth release candidate for 2.0.0. We will be releasing RCs
on a weekly basis until we feel all critical issues are addressed. At
this time, we anticipate few API changes before the stable release, and
recommend testing your production applications against it.

XX August 2012

### UPDATES IN RC6

- Zend\Db
  - ResultInterface adds isBuffered() method for checking if the resultset is
    buffered or not. Allows for more fine grained control of result set
    buffering, including using the database engine's native buffering.
- Zend\Mvc
  - Application no longer defines the "application" identifier for its composed
    EventManager instance. If you had listeners listening on that context,
    update them to use "Zend\Mvc\Application". See this thread for more details:

      http://zend-framework-community.634137.n4.nabble.com/Change-to-Zend-Mvc-Application-s-event-identifiers-tp4656517.html

- Zend\Paginator
  - Removes the factory() and related methods. This was done to be more
    consistent with other components, and also because the utility was not
    terribly useful; in most cases, developers needed to configure the adapter
    up-front anyways.
- Zend\Stdlib
  - ClassMethods Hydrator now supports boolean getters prefixed with "is"

More than XX pull requests for a variety of features and bugfixes were handled
since RC5, as well as around XX documentation changes!

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see INSTALL.md.

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
README-DEV.md and README-GIT.md file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues/browse/ZF2

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
