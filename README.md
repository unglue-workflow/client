# client

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