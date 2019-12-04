# Unglue Client

[![Latest Stable Version](https://poser.pugx.org/unglue/client/v/stable)](https://packagist.org/packages/unglue/client)
[![Total Downloads](https://poser.pugx.org/unglue/client/downloads)](https://packagist.org/packages/unglue/client)
[![Build Status](https://travis-ci.org/unglue-workflow/client.svg?branch=master)](https://travis-ci.org/unglue-workflow/client)
[![Test Coverage](https://api.codeclimate.com/v1/badges/7a7f18ea0ebc8556637d/test_coverage)](https://codeclimate.com/github/unglue-workflow/client/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/7a7f18ea0ebc8556637d/maintainability)](https://codeclimate.com/github/unglue-workflow/client/maintainability)

Documentation: [https://docs.unglue.io](https://docs.unglue.io)

## Phar Builder

In order to build the unglue client phar file `unglue.phar` run:

```
php -d phar.readonly=0 vendor/bin/phar-builder package composer.json --no-interaction
```