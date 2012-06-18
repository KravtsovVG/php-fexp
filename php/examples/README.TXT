PHP File Exchange Platform (PHP-FEXP)
=====================================

This directory contains usage and configuration examples for the PHP File
Exchange Platform.

The 'config.php' file contains all the configuration directives that are
used to setup the PHP File Exchange Platform and modify its behavior
according to your requirements.
You should copy this file to some configuration path (e.g. /etc/FEXP/.).
The path to your actual configuration file MUST then be saved in Apache's
'PHP_FEXP_CONFIG' environment variable (using a 'setenv' directive).
CRITICAL: THIS FILE MUST NOT BE ACCESSIBLE BY HTTP CLIENTS!!!
In other words, it must be accessible by PHP by NOT through any Apache's
URL location.

The 'apache.conf' file contains the recommended Apache configuration for
the server hosting PHP-FEXP. PHP upload and timeout settings must in
particular be adjusted to allow large file uploads.

The 'index.php' file contains the PHP/HTML code necessary to make the PHP
File Exchange Platform accessible via an Apache's URL location.
You should copy this file into your web application's path and then modify
its HTML content to match your needs (but try not to modify its PHP code,
unless you know what you're doing).

The '<dmbs>.sql' files contain the SQL source code required to setup the
(REQUIRED) backing SQL database for the PHP File Exchange Platform.
