<?php // INDENTING (emacs/vi): -*- mode:html; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:

################################################################################
# GENERAL SETTINGS
################################################################################

# Secret used to hash/encrypt local data (eg. file names).
#$_CONFIG['secret_local'] = ''; // [string]

# Secret used to hash/encrypt public data (eg. file handles).
#$_CONFIG['secret_public'] = ''; // [string]

# The fully qualified URL to reach the FEXP server.
# NOTE: This URL is used to construct administrative and download links sent by e-mail.
#$_CONFIG['home_url'] = ''; // [string]

# Enforce encrypted channel (SSL) usage
#$_CONFIG['force_ssl'] = 1; // [integer:boolean]

# Comman-separated list of supported locales, the first being the default.
#$_CONFIG['locales'] = 'en,fr'; // [string]

# Timezone string
# NOTE: This is used only to complete displayed timestamp data
#$_CONFIG['timezone'] = date_default_timezone_get(); // [string]

# Resources (localized HTML snippets, e-mail templates, text lables/messages;
# fonts) directory.
# ATTENTION: This directory MUST be readable (but NOT writable) by PHP!
#$_CONFIG['dir_resources'] = dirname( __FILE__ ).'/data/FEXP/resources'; // [is_readable(path)]
$_CONFIG['dir_resources'] = '/usr/share/php/data/FEXP/resources';

# Files (storage) directory.
# ATTENTION: This directory MUST be writable (and readable) by PHP!
# CRITICAL: THIS DIRECTORY MUST NOT BE ACCESSIBLE FROM THE WEB!!!
#$_CONFIG['dir_files'] = dirname( __FILE__ ).'/data/FEXP/files'; // [is_writable(path)]
$_CONFIG['dir_files'] = '/var/lib/fexp';

# Logs directory.
# ATTENTION: This directory MUST be writable (and readable) by PHP!
# CRITICAL: THIS DIRECTORY MUST NOT BE ACCESSIBLE FROM THE WEB!!!
#$_CONFIG['dir_logs'] = dirname( __FILE__ ).'/data/FEXP/logs'; // [is_writable(path)]
$_CONFIG['dir_logs'] = '/var/log/fexp';


################################################################################
# FILE SETTINGS
################################################################################

# Maximum uploaded file size
# ATTENTION: Make PHP sure to configure PHP (php.ini) accordingly!
#$_CONFIG['file_max_size'] = 10000000; // [integer] bytes (10MB)

# Maximum file download speed
#$_CONFIG['file_max_speed'] = 100000; // [integer] bytes per second (100kB/s)

# Download chunk size
# NOTE: Download speed is unlimited within each chunk, throttling taking place
#       at the end of each chunk.
#$_CONFIG['file_chunk_size'] = 100000; // [integer] bytes (100kB)

# File grace delay (in seconds)
# NOTE: Delay within which a blocked file may be still be downloaded (again) after
#       a successful download, to provision for file saving problems on the client
#       side. This does not work for files that allow only a 'unique' download.
#$_CONFIG['file_grace_delay'] = 180; // [integer] seconds (3 minutes)

# File expirations delay (in days)
# NOTE: Expired files are automatically purged (deleted from the filesystem)
# CRITICAL: MAKE TO SURE TO CONFIGURE THE CORRESPONDING CRON JOB!!!
#$_CONFIG['file_expire_delay'] = 7; // [integer] days

# Database file deletion delay (in days)
# NOTE: This allows to keep track of files after they have been deleted from the filesystem
#$_CONFIG['file_delete_delay'] = 7; // [integer] days

# Default value for the file 'public' status
# NOTE: When a file is 'public', any (authenticated) user may download the file
#       (no permission check is enforced, apart from the web server's provided
#       authentication)
#$_CONFIG['option_public_default'] = 0; // [integer:boolean]

# Allow uploader to change the file 'public' status
#$_CONFIG['option_public_allow'] = 0; // [integer:boolean]

# Default value for the file 'unique' status
# NOTE: When a file is 'unique', it is deleted immediately after the first
#       successful download (this works only for non-public and single
#       downloader/recipient's files)
#$_CONFIG['option_unique_default'] = 1; // [integer:boolean]

# Allow uploader to change the file 'unique' status
#$_CONFIG['option_unique_allow'] = 0; // [integer:boolean]

# Default value for the file 'multiple' status
# NOTE: When a file is 'multiple',
#$_CONFIG['option_multiple_default'] = 0; // [integer:boolean]

# Allow uploader to change the file 'multiple' status
#$_CONFIG['option_multiple_allow'] = 0; // [integer:boolean]


################################################################################
# NOTIFICATIONS SETTINGS
################################################################################

# E-mail address to send administrative notifications FROM.
# ATTENTION: Make sure that you mail system is configured to allow this
#            parameter to be overriden!
#$_CONFIG['notify_sender_address'] = ''; // [string:e-mail]

# E-mail address to send administrative notifications TO.
#$_CONFIG['notify_recipient_address'] = ''; // [string:e-mail]

# Send administrative notification on file upload
#$_CONFIG['notify_upload'] = 1; // [integer:boolean]

# Send administrative notification on file delete
#$_CONFIG['notify_delete'] = 0; // [integer:boolean]

# Send administrative notification on access authorization
#$_CONFIG['notify_authorize'] = 0; // [integer:boolean]

# Send administrative notification on access block
#$_CONFIG['notify_block'] = 0; // [integer:boolean]

# Send administrative notification on access (download) start
#$_CONFIG['notify_start'] = 0; // [integer:boolean]

# Send administrative notification on access (download) completion
#$_CONFIG['notify_complete'] = 0; // [integer:boolean]


################################################################################
# LOG SETTINGS
################################################################################

# Log activity to database
#$_CONFIG['log_database'] = 0; // [integer:boolean]

# Log activity to PHP
#$_CONFIG['log_php'] = 0; // [integer:boolean]

# Log activity to separate file
# NOTE: Also see "$_CONFIG['dir_logs']" configuration setting
#$_CONFIG['log_file'] = 0; // [integer:boolean]

# Log activity to system logger
#$_CONFIG['log_syslog'] = 0; // [integer:boolean]

# Log activity to given system logger facility
#$_CONFIG['log_syslog_facility'] = LOG_DAEMON; // [integer]

# Log file upload activity
#$_CONFIG['log_event_upload'] = 1; // [integer:boolean]

# Log file delete activity
#$_CONFIG['log_event_delete'] = 1; // [integer:boolean]

# Log access authorization activity
#$_CONFIG['log_event_authorize'] = 1; // [integer:boolean]

# Log access block activity
#$_CONFIG['log_event_block'] = 1; // [integer:boolean]

# Log access (download) start activity
#$_CONFIG['log_event_start'] = 1; // [integer:boolean]

# Log access (download) progress activity
#$_CONFIG['log_event_progress'] = 1; // [integer:boolean]

# Log access (download) progress activity delay
#$_CONFIG['log_event_progress_delay'] = 5; // [integer] seconds

# Log access (download) completion activity
#$_CONFIG['log_event_complete'] = 1; // [integer:boolean]


################################################################################
# USER SETTINGS
################################################################################

# Domain to use for user credentials that miss one
#$_CONFIG['user_email_domain_default'] = ''; // [string]

# User domains from/to file exchange is allowed (PERL regular expression)
# ATTENTION: You MUST specify at least one domain for file exchange to be possible
# CRITICAL: DO NOT USE /.*/ AND TURN YOUR SERVER IN AN OPEN RELAY!!!
#$_CONFIG['user_email_domain_relay'] = '/^$/'; // [string:preg]

# User credentials white list (PERL regular expression)
#$_CONFIG['user_email_whitelist'] = ''; // [string:preg]

# User credentials black list (PERL regular expression)
#$_CONFIG['user_email_blacklist'] = ''; // [string:preg]


################################################################################
# SQL SETTINGS
################################################################################

# Database connection parameters (matching PHP Data Objects [PDO] stanza).
#$_CONFIG['sql_dsn'] = 'mysql:host=localhost;dbname=fexp'; // string
#$_CONFIG['sql_username'] = 'fexp'; // string
#$_CONFIG['sql_password'] = ''; // string
#$_CONFIG['sql_options'] = array(); // array (of scalar)

# Database statement(s) to execute to prepare the database before registration.
# Example: 'SET NAMES \'ISO-8859-1\'' when using a non-ISO-8859-1 database.
#$_CONFIG['sql_prepare'] = ''; // string


################################################################################
# MISC SETTINGS
################################################################################

# Super users
# NOTE: Super users may access/use any file handle or perform system cleanup
#$_CONFIG['superusers'] = array(); // [array]

