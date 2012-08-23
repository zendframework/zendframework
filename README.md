### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0rc5*

This is the fifth release candidate for 2.0.0. We will be releasing RCs
on a weekly basis until we feel all critical issues are addressed. At
this time, we anticipate few API changes before the stable release, and
recommend testing your production applications against it.

23 August 2012

### UPDATES IN RC5

- Zend\Db
  - Now handles null values properly in execute mode.
  - Added SqlSrv integration tests (prompted by a bug report of issues with
    establishing a connection).
- Zend\Form
  - The FormButton helper now allows translation. However, to make this work, it
    now requires that the label value is set in the element.
  - Fixed an issue with the MultiCheckBox helper to ensure checked/unchecked
    values are properly populated.
- Zend\Log
  - The table name constructor option is now optional, allowing you to pass it
    in a configuration array.
  - Now allows using DateTime with the Db Writer.
- Zend\Http
  - Added ability to set the SSL capath option.
  - Added a check for the sslcapath being set if sslverifypeer is enabled when
    first connecting to an SSL-enabled site; an exception is thrown if all
    conditions are not met.
  - PhpEnvironment\Request object now falls back to '/' for the base path if the
    SCRIPT_FILENAME server variable is not present (true in PHP 5.4 web server
    and several others).
- Zend\I18n
  - Caching translations now works.
- Zend\Mvc
  - forward() plugin no longer results in double template rendering.
- Zend\ServiceManager
  - Now ensures that when $allowOverride is enabled that services registered
    with the same name overwrite properly.
- Zend\Validator
  - The Uri validator is now listed in the validator plugin manager.
  - The EmailAddress validator now allows setting custom error messages.
- General fixes
  - Removed all locations of error suppression remaining in the framework.
  - Synced the implementations of Zend\Mvc\Controller\Plugin\Url and
    Zend\View\Helper\Url.

More than 20 pull requests for a variety of features and bugfixes were handled
since RC4, as well as around 30 documentation changes!

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
