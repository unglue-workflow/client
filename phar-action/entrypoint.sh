#!/bin/sh -l

pwd
ls -alh

echo "INSTSALL"
composer install

echo "BUILD PHAR"

./vendor/bin/phar-builder package composer.json