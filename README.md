### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0rc4*

This is the fourth release candidate for 2.0.0. We will be releasing RCs
on a weekly basis until we feel all critical issues are addressed. At
this time, we anticipate few API changes before the stable release, and
recommend testing your production applications against it.

17 August 2012

### UPDATES IN RC4

- Zend\Db
  - RowGateway:  delete() now works; RowGateway objects now no longer duplicates
    the content internally leading to a larger than necessary memory footprint.
  - Adapter for PDO: fixed such that all calls to rowCount() will always be an
    integer; also fixed disconnect() from unsetting property
  - Zend\Validator\Db: fixed such that TableIdentifier can be used to promote
    schema.table identifiers
  - Sql\Select: added reset() API to reset parts of a Select object, also
    includes updated constants to refer to the parts by
  - Sql\Select and others: Added subselect support in Select, In Expression and
    the processExpression() abstraction for Zend\Db\Sql
  - Metadata: fixed various incorrect keys when refering to contstraint data in
    metadata value objects
- Zend\Filter
  - StringTrim filter now properly handles unicode whitespace
- Zend\Form
  - FieldsetInterface now defines the methods allowObjectBinding() and
    allowValueBinding().
  - New interface, FieldsetPrepareAwareInterface. Collection and Fieldset both
    implement this.
    - See https://github.com/zendframework/zf2/pull/2184 for details
  - Select elements now handle options and validation more consistently with
    other multi-value elements.
- Zend\Http
  - SSL options are now propagated to all Socket Adapter subclasses
- Zend\InputFilter
  - Allows passing ValidatorChain and FilterChain instances to the factory
- Zend\Log
  - Fixed xml formatter to not display empty extra information
- Zend\Loader
  - SplAutoloader was renamed to SplAutoloaderInterface (consistency issue)
- Zend\Mvc
  - params() helper now allows fetching full parameter containers if no
    arguments are provided to its various methods (consistency issue)
- Zend\Paginator
  - The DbSelect adapter now works
- Zend\View
  - ViewModel now allows unsetting variables properly
- Security
  - Fixed issues in Zend\Dom, Zend\Soap, Zend\Feed, and Zend\XmlRpc with regards
    to the way libxml2 allows xml entity expansion from DOCTYPE entities when it
    is provided.

Around 50 pull requests for a variety of features and bugfixes were handled
since RC3, as well as almost 30 documentation changes!

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
