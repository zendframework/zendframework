# CONTRIBUTING

## RESOURCES

If you wish to contribute to Zend Framework, please be sure to
read/subscribe to the following resources:

 -  Coding Standards:
    http://framework.zend.com/wiki/display/ZFDEV2/Coding+Standards
 -  ZF Git Guide:
    [README-GIT.md](README-GIT.md)
 -  Contributor's Guide:
    http://framework.zend.com/participate/contributor-guide
 -  ZF Contributor's mailing list:
    Archives: http://zend-framework-community.634137.n4.nabble.com/ZF-Contributor-f680267.html
    Subscribe: zf-contributors-subscribe@lists.zend.com
 -  ZF Contributor's IRC channel:
    #zftalk.dev on Freenode.net

If you are working on new features, or refactoring an existing
component, please create a proposal. You can do this in on the RFC's
page, http://framework.zend.com/wiki/display/ZFDEV2/RFC%27s.

## Reporting Potential Security Issues

If you have encountered a potential security vulnerability in Zend Framework, please report it to us at [zf-security@zend.com](mailto:zf-security@zend.com). We will work with you to verify the vulnerability and patch it.

When reporting issues, please provide the following information:

- Component(s) affected
- A description indicating how to reproduce the issue
- A summary of the security vulnerability and impact

We request that you contact us via the email address above and give the project contributors a chance to resolve the vulnerability and issue a new release prior to any public exposure; this helps protect Zend Framework users and provides them with a chance to upgrade and/or update in order to protect their applications.

For sensitive email communications, please use [our PGP key](http://framework.zend.com/zf-security-pgp-key.asc).

## RUNNING TESTS

To run tests:

- Make sure you have a recent version of PHPUnit installed; 3.7.0
  minimally.
- Enter the `tests/` subdirectory.
- Execute PHPUnit, providing a path to a component directory for which
  you wish to run tests, or a specific test class file.

  ```sh
  % phpunit ZendTest/Http
  % phpunit ZendTest/Http/Header/EtagTest.php
  ```

- You may also provide the `--group` switch; in such cases, provide the
  top-level component name:

  ```sh
  % phpunit --group Zend_Http
  ```

  This will likely lead to errors, so it's usually best to specify a
  specific component in which to run test:

  ```sh
  % phpunit --group ZF-XYZ Zend/Http
  ```
- Alternately, use the `run-tests.php` script. This can be executed with no
  arguments to run all tests:

  ```sh
  % php run-tests.php
  ```

  You can also provide top-level component names to run tests for individual
  components or several components at a time. The component name is the the
  component namespace, without the `Zend\` prefix:

  ```sh
  % php run-tests.php Mvc
  ```

  ```sh
  % php run-tests.php ModuleManager Mvc View Navigation
  ```

You can turn on conditional tests with the TestConfiguration.php file.
To do so:

 -  Enter the `tests/` subdirectory.
 -  Copy `TestConfiguration.php.dist` file to `TestConfiguration.php`
 -  Edit `TestConfiguration.php` to enable any specific functionality you
    want to test, as well as to provide test values to utilize.
