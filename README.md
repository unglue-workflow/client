# Unglue Client

![Tests](https://github.com/unglue-workflow/client/workflows/Tests/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/unglue/client/v/stable)](https://packagist.org/packages/unglue/client)
[![Total Downloads](https://poser.pugx.org/unglue/client/downloads)](https://packagist.org/packages/unglue/client)
[![Test Coverage](https://api.codeclimate.com/v1/badges/7a7f18ea0ebc8556637d/test_coverage)](https://codeclimate.com/github/unglue-workflow/client/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/7a7f18ea0ebc8556637d/maintainability)](https://codeclimate.com/github/unglue-workflow/client/maintainability)

Documentation: [https://docs.unglue.io](https://docs.unglue.io)

## Using the Phar File

1. `wget -O unglue.phar https://github.com/unglue-workflow/client/raw/master/unglue.phar`
2. chmod +x unglue.phar
3. `./unglue.phar compile`

## Phar Builder

In order to build the unglue client phar file `unglue.phar` run:

> BUG: Until fixd, ensure you cleanup the vendor/luyadev/installer.php file and remove the LUYA modules which are part of the testsuite.

```
php -d phar.readonly=0 vendor/bin/phar-builder package composer.json --no-interaction && chmod +x unglue.phar
```