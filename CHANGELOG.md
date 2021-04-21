# Changelog

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## 1.5.3 (21. April 2021)

+ Added PHP 8 tests, allow new LUYA core version.

## 1.5.2 (3. November 2020)

+ Improved verbosity for connection errors. Updated phar file. Moved to Github Actions

## 1.5.1 (5. April 2020)

+ [#17](https://github.com/unglue-workflow/client/pull/17) Fix excluded directories with trailing slashes.

## 1.5.0 (4. December 2019)

+ [#16](https://github.com/unglue-workflow/client/issues/16) Add retry option to wait for server.
+ Added phar file `unglue.phar`
+ Enabled php 7.4 travis testing.

## 1.4.1 (20. March 2019)

+ [#13](https://github.com/unglue-workflow/client/issues/13) Fixed bug where css files where not added to the list of transmitted files (only scss files where sent).

## 1.4.0 (18. March 2019)

+ [#12](https://github.com/unglue-workflow/client/issues/12) Disable following symlinks by default. Add option `--symlinks=1` in order to enable following symlinks.

## 1.3.0 (11. Feburary 2019)

+ [#11](https://github.com/unglue-workflow/client/issues/11) Allow wildcard js file paths like `/lib/*.js` in order to define js and sprite sections.
+ [#10](https://github.com/unglue-workflow/client/issues/10) Follow symlink paths (read symlink).

## 1.2.0 (22. January 2019)

+ [#8](https://github.com/unglue-workflow/client/issues/8) Reindex file map and clear temporary config file to detect new entries.

## 1.1.1 (22. January 2019)

+ [#9](https://github.com/unglue-workflow/client/issues/9) Fix issue where delete files do not crash watch command.
+ [#7](https://github.com/unglue-workflow/client/issues/7) Close curl connection after request.

## 1.1.0 (9. January 2019)

+ [#6](https://github.com/unglue-workflow/client/issues/6) Added a new exclude option to filter unglue files in certain folders. By default it will now filter unglue files in vendor folders.

## 1.0.1 (3. January 2019)

+ [#5](https://github.com/unglue-workflow/client/issues/5) Do not run css compile when css section is empty in unglue config file.
+ Added unit tests
+ Add option to provide message for the prefix.

## 1.0.0 (21. December 2018)

+ First stable release.
