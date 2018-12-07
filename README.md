# unglue client

[![Build Status](https://travis-ci.org/unglue-workflow/client.svg?branch=master)](https://travis-ci.org/unglue-workflow/client)
[![Test Coverage](https://api.codeclimate.com/v1/badges/7a7f18ea0ebc8556637d/test_coverage)](https://codeclimate.com/github/unglue-workflow/client/test_coverage)
[![Maintainability](https://api.codeclimate.com/v1/badges/7a7f18ea0ebc8556637d/maintainability)](https://codeclimate.com/github/unglue-workflow/client/maintainability)

The client binary which sends the data to the server and creates the output from the API response.

## Usage

```sh
composer require --dev unglue/client
```

Add the `.unglue` files, for example `main.unglue`:

```json
{
    "css" : [
        "../../src/scss/main.scss"
    ],
    "js" : [
        "js/jquery.js",
        "js/app.js",
        "js/datepicker.js"
    ],
    "options": {
        "compress" : true,
        "maps" : true
    }
}
```


Run inside current directory and all sub directories:

```sh
./vendor/bin/unglue watch
```

Listen inside a certain folder:

```sh
./vendor/bin/unglue watch resources/
````

Run only once

```sh
./vendor/bin/unglue compile
```