# CONTRIBUTING

## RESOURCES

If you wish to contribute to Zend Framework, please be sure to
read/subscribe to the following resources:

 -  [Coding Standards](http://framework.zend.com/wiki/display/ZFDEV2/Coding+Standards)
 -  [ZF Git Guide](README-GIT.md)
 -  [Contributor's Guide](http://framework.zend.com/participate/contributor-guide)
 -  ZF Contributor's mailing list:
    Archives: http://zend-framework-community.634137.n4.nabble.com/ZF-Contributor-f680267.html
    Subscribe: zf-contributors-subscribe@lists.zend.com
 -  ZF Contributor's IRC channel:
    #zftalk.dev on Freenode.net

If you are working on new features, or refactoring an existing
component, please [create a proposal](https://github.com/zendframework/zf2/issues/new).

## Reporting Potential Security Issues

If you have encountered a potential security vulnerability in Zend Framework, please **DO NOT** report it on the public
issue tracker: send it to us at [zf-security@zend.com](mailto:zf-security@zend.com) instead.
We will work with you to verify the vulnerability and patch it as soon as possible.

When reporting issues, please provide the following information:

- Component(s) affected
- A description indicating how to reproduce the issue
- A summary of the security vulnerability and impact

We request that you contact us via the email address above and give the project contributors a chance to resolve the vulnerability and issue a new release prior to any public exposure; this helps protect Zend Framework users and provides them with a chance to upgrade and/or update in order to protect their applications.

For sensitive email communications, please use [our PGP key](http://framework.zend.com/zf-security-pgp-key.asc).

## RUNNING TESTS

To run tests:

- Clone the zf2 repository (or download it, if you do not have GIT installed):

  ```sh
  % git clone git@github.com:zendframework/zf2.git
  % cd
  ```

- Install dependencies via composer:

  ```sh
  % curl -sS https://getcomposer.org/installer | php --
  % ./composer.phar install
  ```

  If you don't have `curl` installed, you can also download `composer.phar` from https://getcomposer.org/

- Run the tests via `phpunit` and the provided PHPUnit config, like in this example:

  ```sh
  % ./../vendor/bin/phpunit -c tests/phpunit.xml.dist tests/ZendTest/Http
  % ./../vendor/bin/phpunit -c tests/phpunit.xml.dist tests/ZendTest/Http/Header/EtagTest.php
  ```

  Note that the entire test suite is not designed to be run in a single pass.
  Run tests for the single components instead. You can do it by using the `run-tests.php` utility provided
  with the repository:

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
