### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0dev-rc1*

6 July 2012

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

### NEW FEATURES / UPDATES IN RC1

 - Documentation
   - is now in a new repository,
     https://github.com/zendframework/zf2-documentation
   - Documentation has been converted from DocBook5 to ReStructured Text
     (reST or rst).
 - Form
   - Select-style elements now have options populated as value => label
     pairs instead of label => value pairs. This is done to ensure that
     option values are unique.
 - Http
   - set/getServer() and set/getEnv() were removed from Http\Request
     and now part of Http\PhpEnvironment\Request
   - set/getFile() methods in Http\PhpEnvironment\Request
     were renamed to set/getFiles(). Also above methods
   - When submitted form has file inputs with brackets (name="file[]")
     $fileParams parameters in Http\PhpEnvironment\Request will be
     re-structured to have the same look as query/post/server/envParams

Over *XXX* pull requests for a variety of features and bugfixes were handled
since beta5!

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see [INSTALL.md](INSTALL.md).

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
[README-DEV.md](README-DEV.md) and [README-GIT.md](README-GIT.md) file.

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
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
