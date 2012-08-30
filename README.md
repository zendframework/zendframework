### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0rc7*

This is the seventh release candidate for 2.0.0. At this time, we anticipate
that this will be the final release candidate before issuing a stable release.
We highly recommend testing your production applications against it.

XX August 2012

### UPDATES IN RC7

- Zend\Di
  - Fixes ArrayDefinition and ClassDefinition hasMethods() methods to return
    boolean values.
- Zend\Log
  - Fixes an issue with Zend\Log\Formatter\Simple whereby it was using a legacy
    key ("info") instead of the key standardized upon in ZF2 ("extra"). 
  - Simple formatter now defaults to JSON-encoding for array and object
    serialization (prevents issues with some writers.)
- Zend\Mvc
  - Fixes an issue in the ViewHelperManagerFactory whereby a condition was
    testing against an uninitialized value.
  - Added zend-console to composer.json dependencies.

More than XX pull requests for a variety of features and bugfixes were handled
since RC6, as well as almost XX documentation changes!

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
