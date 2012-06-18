PHP File Exchange Platform (PHP-FEXP)
=====================================


Synopsis
--------

The PHP File Exchange Platform is a PHP class which allows to implement simple
(large) files exchange via common web browsers or download managers, as a
complement to (size-limited) e-mail messages.

Sharing a new file requires the following actions:
 - uploader visits and authenticates on the file exchange server
 - uploader uploads a file, configure its sharing options and specify recipients
 - recipients receive download URL via e-mail
 - recipients recover the file using web browser or download manager
 - file is automatically deleted after expiration delay (or first successfull
   download if configured to do so)

PHP File Exchange Plarform sole (unusual/streneous) requirement is that
authentication MUST be performed by the server hosting the service.
This requirements allows to:
 - benefit from the many, reliable and hard-proven authentication backends
   offered by common HTTP servers (such as Apache's many 'mod_auth' modules)
 - protect the PHP code itself (and potential vulnerabilities) from anonymous
   hacking attempts
For public usage, the PHP Generic Registration Module (PHP-GEREMO) stands as an
ideal companion, allowing secure and foolproof registration of users.


Dependencies
------------

 - [MUST] PHP 5.2 or later (it may work on earlier PHP 5 versions; this is untested though)
 - [MUST] PHP mhash extension
 - [MUST] PHP mcrypt extension
 - [MUST] PHP PEAR::Mail and PEAR::Mail_Mime extensions (and dependencies)
 - [MUST] PHP PDO extension


Features/Requirements
---------------------

 - web (HTTP) file sharing along with automated e-mail notifications
 - mandatory authentication by web server (using any supported backend: file,
   LDAP, MySQL, PostgreSQL, ...)
 - support for SSL (certificate) authentication
 - file integrity verification (thanks to MD5 checksumming)
 - index-friendly usage of backing filesystem (thanks to multi-level directory
   hierarchy)
 - encrypted and user-bound access links
 - download speed limit (throttling)
 - download resume support ('Content-Range' HTTP header)
 - fully configurable activity logging via e-mail, PHP log, external log file,
   system logger (syslog) or database
 - localized and customizable HTML, e-mail templates and text messages

