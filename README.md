# client

The client binary which sends the data to the server and creates the output from the API response.

## Usage

```sh
composer require fwcc/client
```

Add the `.fwcc` files, for example `main.fwcc`:

```json
{
    "scss" : [
        "../../src/scss/main.scss"
    ]
}
```


Run inside current directory and all sub directories:

```sh
./vendor/bin/fwcc listen
```

Listen inside a certain folder:

```sh
./vendor/bin/fwcc listen resources/
````