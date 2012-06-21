<?php // INDENTING (emacs/vi): -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
/** PHP File Exchange Platform (PHP-FEXP)
 *
 * <P><B>COPYRIGHT:</B></P>
 * <PRE>
 * PHP File Exchange Platform (PHP-FEXP)
 * Copyright (C) 2012 Cedric Dufour <http://cedric.dufour.name>
 * Author(s): Cedric Dufour <cedric.dufour@network.net>
 *
 * This file is part of the PHP File Exchange Platform (PHP-FEXP).
 *
 * The PHP File Exchange Platform (PHP-FEXP) is free software:
 * you can redistribute it and/or modify it under the terms of the GNU General
 * Public License as published by the Free Software Foundation, Version 3.
 *
 * The PHP File Exchange Platform (PHP-FEXP) is distributed in the hope
 * that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License (LICENSE.TXT) for more details.
 * </PRE>
 *
 * @package    FEXP
 * @subpackage Main
 * @copyright  2012 Cedric Dufour <http://cedric.dufour.name>
 * @author     Cedric Dufour <cedric.dufour@ced-network.net>
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) Version 3
 * @version    @version@
 * @link       http://www.ced-network.net/php-fexp
 */

/** PHP File Exchange Platform
 *
 * @package    FEXP
 * @subpackage Main
 */
class FEXP
{

  /*
   * FIELDS
   ********************************************************************************/

  /** Configuration parameters
   * @var array|mixed */
  private $amCONFIG;

  /** Form data
   * @var array|mixed */
  private $amFORMDATA;

  /** Database connection object (PDO)
   * @var object */
  private $oPDO;


  /*
   * CONSTRUCTORS
   ********************************************************************************/

  /** Construct and inititalize a new FEXP object
   *
   * @param string $sConfigurationPath Configuration file path
   */
  public function __construct( $sConfigurationPath )
  {
    // Fields
    $this->initConfig( $sConfigurationPath );
    $this->amFORMDATA = array();
  }


  /*
   * METHODS: Configuration
   ********************************************************************************/

  /** Initialize (default or user-overriden) configuration parameters for this object
   *
   * @param string $sConfigurationPath Configuration file path (see the sample <SAMP>config.php</SAMP> file for further details)
   */
  private function initConfig( $sConfigurationPath )
  {
    // Set defaults
    $_CONFIG = array();
    $_CONFIG['secret_local'] = ''; // [string]
    $_CONFIG['secret_public'] = ''; // [string]
    $_CONFIG['home_url'] = ''; // [string]
    $_CONFIG['force_ssl'] = 1; // [integer:boolean]
    $_CONFIG['locales'] = 'en,fr'; // [string]
    $_CONFIG['timezone'] = date_default_timezone_get(); // [string]
    $_CONFIG['dir_resources'] = dirname( __FILE__ ).'/data/FEXP/resources'; // [is_readable(path)]
    $_CONFIG['dir_files'] = dirname( __FILE__ ).'/data/FEXP/files'; // [is_writable(path)]
    $_CONFIG['dir_logs'] = dirname( __FILE__ ).'/data/FEXP/logs'; // [is_writable(path)]
    $_CONFIG['file_max_size'] = 10000000; // [integer] bytes (10MB)
    $_CONFIG['file_max_speed'] = 100000; // [integer] bytes per second (100kB/s)
    $_CONFIG['file_chunk_size'] = 100000; // [integer] bytes (100kB)
    $_CONFIG['file_grace_delay'] = 180; // [integer] seconds (3 minutes)
    $_CONFIG['file_expire_delay'] = 7; // [integer] days
    $_CONFIG['file_delete_delay'] = 7; // [integer] days
    $_CONFIG['option_public_default'] = 0; // [integer:boolean]
    $_CONFIG['option_public_allow'] = 0; // [integer:boolean]
    $_CONFIG['option_unique_default'] = 1; // [integer:boolean]
    $_CONFIG['option_unique_allow'] = 0; // [integer:boolean]
    $_CONFIG['option_multiple_default'] = 0; // [integer:boolean]
    $_CONFIG['option_multiple_allow'] = 0; // [integer:boolean]
    $_CONFIG['notify_sender_address'] = ''; // [string:e-mail]
    $_CONFIG['notify_recipient_address'] = ''; // [string:e-mail]
    $_CONFIG['notify_upload'] = 1; // [integer:boolean]
    $_CONFIG['notify_delete'] = 0; // [integer:boolean]
    $_CONFIG['notify_authorize'] = 0; // [integer:boolean]
    $_CONFIG['notify_block'] = 0; // [integer:boolean]
    $_CONFIG['notify_start'] = 0; // [integer:boolean]
    $_CONFIG['notify_complete'] = 0; // [integer:boolean]
    $_CONFIG['log_database'] = 0; // [integer:boolean]
    $_CONFIG['log_php'] = 0; // [integer:boolean]
    $_CONFIG['log_file'] = 0; // [integer:boolean]
    $_CONFIG['log_syslog'] = 0; // [integer:boolean]
    $_CONFIG['log_syslog_facility'] = LOG_DAEMON; // [integer]
    $_CONFIG['log_event_upload'] = 1; // [integer:boolean]
    $_CONFIG['log_event_delete'] = 1; // [integer:boolean]
    $_CONFIG['log_event_authorize'] = 1; // [integer:boolean]
    $_CONFIG['log_event_block'] = 1; // [integer:boolean]
    $_CONFIG['log_event_start'] = 1; // [integer:boolean]
    $_CONFIG['log_event_progress'] = 1; // [integer:boolean]
    $_CONFIG['log_event_progress_delay'] = 5; // [integer] seconds
    $_CONFIG['log_event_complete'] = 1; // [integer:boolean]
    $_CONFIG['user_email_domain_default'] = ''; // [string]
    $_CONFIG['user_email_domain_relay'] = '/^$/'; // [string:preg]
    $_CONFIG['user_email_whitelist'] = ''; // [string:preg]
    $_CONFIG['user_email_blacklist'] = ''; // [string:preg]
    $_CONFIG['sql_dsn'] = 'mysql:host=localhost;dbname=fex'; // [string]
    $_CONFIG['sql_username'] = 'fex'; // [string]
    $_CONFIG['sql_password'] = ''; // [string]
    $_CONFIG['sql_options'] = array(); // [array]
    $_CONFIG['sql_prepare'] = ''; // [string]
    $_CONFIG['superusers'] = array(); // [array]

    // Load user configuration
    if( ( include $sConfigurationPath ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to load configuration', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }

    // Validation
    // NOTE: we perform quite thorough validation, which is a performances hit, but should not be
    //       a problem since the service is by nature not subject to heavy traffic
    //echo nl2br( var_export( $_CONFIG, true ) ); // DEBUG
    // ... is integer
    foreach( array( 'force_ssl',
                    'file_max_size', 'file_max_speed', 'file_chunk_size', 'file_grace_delay', 'file_expire_delay',
                    'option_public_default', 'option_public_allow',
                    'option_unique_default', 'option_unique_allow',
                    'option_multiple_default', 'option_multiple_allow',
                    'notify_upload', 'notify_delete', 'notify_authorize', 'notify_block', 'notify_start', 'notify_complete',
                    'log_database', 'log_php', 'log_file', 'log_syslog', 'log_syslog_facility',
                    'log_event_upload', 'log_event_delete', 'log_event_authorize', 'log_event_block', 'log_event_start', 'log_event_progress', 'log_event_progress_delay', 'log_event_complete'
                    ) as $p )
      if( !is_int( $_CONFIG[$p] ) )
        trigger_error( '['.__METHOD__.'] Parameter must be an integer; '.$p, E_USER_ERROR );
    // ... is string
    foreach( array( 'secret_local', 'secret_public', 'home_url', 'locales', 'timezone',
                    'dir_resources', 'dir_files', 'dir_logs',
                    'notify_sender_address', 'notify_recipient_address',
                    'user_email_domain_default', 'user_email_domain_relay', 'user_email_whitelist', 'user_email_blacklist',
                    'sql_dsn', 'sql_username', 'sql_password', 'sql_prepare'
                    ) as $p )
      if( !is_string( $_CONFIG[$p] ) )
        trigger_error( '['.__METHOD__.'] Parameter must be a string; '.$p, E_USER_ERROR );
    // ... is e-mail
    foreach( array( 'user_email_sender', 'user_email_notify' ) as $p )
      if( !empty( $_CONFIG[$p] ) and !preg_match( '/^(\w+[-_.])*\w+@(\w+[-_.])*\w+\.\w+$/AD', $_CONFIG[$p] ) )
        trigger_error( '['.__METHOD__.'] Parameter must be a valid E-mail address; '.$p, E_USER_ERROR );
    // ... is preg
    foreach( array( 'user_email_domain_relay', 'user_email_whitelist', 'user_email_blacklist' ) as $p )
      if( !empty( $_CONFIG[$p] ) and preg_match( $_CONFIG[$p], '' ) === false )
        trigger_error( '['.__METHOD__.'] Parameter must be a valid Perl regular expression; '.$p, E_USER_ERROR );
    // ... is readable
    foreach( array( 'dir_resources' ) as $p )
      if( !is_readable( $_CONFIG[$p] ) )
        trigger_error( '['.__METHOD__.'] Parameter must be a readable path; '.$p, E_USER_ERROR );
    // ... is writeable
    foreach( array( 'dir_files', 'dir_logs' ) as $p )
      if( !is_writable( $_CONFIG[$p] ) )
        trigger_error( '['.__METHOD__.'] Parameter must be a writable path; '.$p, E_USER_ERROR );
    // ... is array
    foreach( array( 'sql_options', 'superusers' ) as $p )
      if( !is_array( $_CONFIG[$p] ) )
        trigger_error( '['.__METHOD__.'] Parameter must be an array; '.$p, E_USER_ERROR );

    // Done
    $this->amCONFIG = $_CONFIG;
  }


  /*
   * METHODS: Locale
   ********************************************************************************/

  /** Retrieve the supported locales
   *
   * @return array|string Array of locale IDs
   */
  public function getLocaleSupported()
  {
    return explode( ',', $this->amCONFIG['locales'] );
  }

  /** Retrieve the default locale
   *
   * @return string Locale ID
   */
  public function getLocaleDefault()
  {
    static $sLocale;
    if( is_null( $sLocale ) )
    {
      $i = strpos( $this->amCONFIG['locales'], ',' );
      if( $i === false ) $sLocale = $this->amCONFIG['locales'];
      else $sLocale = substr( $this->amCONFIG['locales'], 0, $i );
    }
    return $sLocale;
  }

  /** Retrieve the current locale
   *
   * @return string Locale ID
   */
  public function getLocaleCurrent()
  {
    static $sLocale;
    if( is_null( $sLocale ) )
    {
      if( isset( $_SESSION['FEXP_Locale'] ) )
      {
        $sLocale = $_SESSION['FEXP_Locale'];
      }
      else
      {
        $sLocale = $this->getLocaleDefault();
      }
    }
    return $sLocale;
  }

  /** Retrieve (localized) text
   *
   * @param string $sTextID Text ID
   * @return string Text
   */
  public function getLocaleText( $sTextID )
  {
    static $_TEXT;
    
    // Initialize message array
    if( is_null( $_TEXT ) )
    {
      // Default (English messages)
      $_TEXT = array();
      $_TEXT['title:file_exchange_platform'] = 'PHP File Exchange Platform (PHP-FEXP)';
      $_TEXT['label:language'] = 'Language';
      $_TEXT['label:timezone'] = 'Timezone';
      $_TEXT['label:file'] = 'File';
      $_TEXT['label:file_size'] = 'Size';
      $_TEXT['label:file_max_size'] = 'Max.Size';
      $_TEXT['label:file_md5'] = 'MD5';
      $_TEXT['label:upload_user'] = 'Uploaded By';
      $_TEXT['label:upload_timestamp'] = 'Uploaded On';
      $_TEXT['label:expire_timestamp'] = 'Expiration';
      $_TEXT['label:option_public'] = 'Public';
      $_TEXT['label:option_unique'] = 'Unique';
      $_TEXT['label:option_multiple'] = 'Multiple';
      $_TEXT['label:download_user'] = 'Recipient';
      $_TEXT['label:message'] = 'Message';
      $_TEXT['label:download_start'] = 'Download Started';
      $_TEXT['label:download_complete'] = 'Download Completed';
      $_TEXT['label:download_usercount'] = 'Recipients';
      $_TEXT['label:download_count'] = 'Download Count';
      $_TEXT['label:download_block'] = 'Blocked';
      $_TEXT['label:filter'] = 'Filter';
      $_TEXT['tooltip:file_max_size'] = 'The maximum file size accepted by the system. Do NOT attempt to send a larger file, it WILL be discarded!';
      $_TEXT['tooltip:file_md5'] = 'If provided, the MD5 checksum will be used to check the file integrity after the upload.';
      $_TEXT['tooltip:expire_timestamp'] = 'Date/time after which the file will be automatically deleted by the system.';
      $_TEXT['tooltip:option_public'] = 'Allow any (authenticated) user to download the file';
      $_TEXT['tooltip:option_unique'] = 'Delete the file immediately after successful download (only for non-public and single downloader/recipient\'s files)';
      $_TEXT['tooltip:option_multiple'] = 'Allow multiple downloads by the same downloader/recipient';
      $_TEXT['tooltip:download_user'] = 'Recipient(s) which shall be granted download permission (and receive a download notification e-mail).';
      $_TEXT['tooltip:message'] = 'Message to add to the download notification e-mail.';
      $_TEXT['tooltip:filter'] = 'Display uploaded files which match the given filter.';
      $_TEXT['button:upload'] = 'Upload';
      $_TEXT['button:update'] = 'Update';
      $_TEXT['button:delete'] = 'Delete';
      $_TEXT['button:add'] = 'Add';
      $_TEXT['button:block'] = 'Block';
      $_TEXT['button:unblock'] = 'Unblock';
      $_TEXT['button:filter'] = 'Filter';
      $_TEXT['confirm:delete'] = 'Are you sure you want to delete the selected file(s)?';
      $_TEXT['message:public'] = 'THIS FILE HAS BEEN GRANTED PUBLIC ACCESS. PLEASE FORWARD THIS DOWNLOAD NOTIFICATION TO THE DESIRED RECIPIENTS.';
      $_TEXT['error:internal_error'] = 'Internal error. Please contact the system administrator.';
      $_TEXT['error:unsecure_channel'] = 'Unsecure channel. Please use an encrypted channel (SSL).';
      $_TEXT['error:unauthenticated_channel'] = 'Unauthenticated channel. Please use an authenticated channel.';
      $_TEXT['error:not_authorized'] = 'You are not authorized to access this resource or perform this action.';
      $_TEXT['error:file_corrupted'] = 'File upload corruption. Please check the provided MD5 checksum and try again.';
      $_TEXT['error:file_missing'] = 'The file is no longer available on the system.';
      $_TEXT['error:invalid_credentials'] = 'Your credentials do not allow you to use this system.';
      $_TEXT['error:invalid_handle'] = 'This file link belongs to another user or does not allow you to perform this action.';
      $_TEXT['error:invalid_data'] = 'Invalid data. Please contact the system administrator.';
      $_TEXT['error:invalid_file_size'] = 'The file is larger than maximum allowed size.';
      $_TEXT['error:invalid_file_exists'] = 'File already exists.';
      $_TEXT['error:invalid_email'] = 'Invalid e-mail address.';
      $_TEXT['error:invalid_email_whitelist'] = 'Your e-mail address has not been whitelisted.';
      $_TEXT['error:invalid_email_blacklist'] = 'Your e-mail address has been blacklisted.';
      $_TEXT['error:invalid_email_relay'] = 'File exchange with this downloader/recipient is not allowed.';
      $_TEXT['error:missing_required_field'] = 'Please fill-in the missing required field.';

      // Include localized messages
      $sLocale = $this->getLocaleCurrent();
      if( $sLocale != 'en' )
        include_once $this->amCONFIG['dir_resources'].'/'.$sLocale.'/text.php';
    }

    // Done
    return $_TEXT[$sTextID];
  }


  /*
   * METHODS: Path
   ********************************************************************************/

  /** Retrieve the resources directory path
   *
   * @return string Directory path
   */
  public function getDirResources()
  {
    return $this->amCONFIG['dir_resources'];
  }


  /*
   * METHODS: File
   ********************************************************************************/

  /** Retrieve file hash
   *
   * <P><B>SYNOPSIS:</B> This function returns the 64-character uppercase hash for the
   * given filename and uploader (and server's local secret).</P>
   *
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @return string File hash (hex-encoded)
   */
  private function getFileHash( $sFileName, $sUploadUser )
  {
    $sFileHash = $sFileName.$sUploadUser.$this->amCONFIG['secret_local'];
    return strtoupper( bin2hex( mhash( MHASH_SHA256, $sFileHash ) ) );
  }

  /** Encrypt file handle
   *
   * <P><B>SYNOPSIS:</B> This function returns the encrypted uppercase cipher for the
   * given file handle and user (and server's public secret).</P>
   *
   * @param integer $iFileHandle File handle
   * @param string $sUser User descriptor
   * @param boolean $bAdmin Administrative handle
   * @return string Encrypted file handle (hex-encoded)
   */
  private function encryptFileHandle( $iFileHandle, $sUser, $bAdmin )
  {
    $sFileCipher = '{ID}'.$iFileHandle.',{USER}'.$sUser.',{ADMIN}'.($bAdmin?'yes':'no').',{SALT}'.mcrypt_create_iv( 32, MCRYPT_DEV_URANDOM );
    $sKey = mhash_keygen_s2k ( MHASH_SHA256, $this->amCONFIG['secret_public'], null, 32 );
    return strtoupper( bin2hex( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $sKey, $sFileCipher, MCRYPT_MODE_ECB ) ) );
  }

  /** Decrypt file handle
   *
   * <P><B>SYNOPSIS:</B> This function returns the decrypted file handle for the
   * given file cipher (and server's public secret).</P>
   *
   * <P><B>ARRAY KEYS:</B><BR/></P>
   * - <SAMP>handle</SAMP>: File handle<BR/>
   * - <SAMP>user</SAMP>: User descriptor<BR/>
   * - <SAMP>admin</SAMP>: Administrative handle<BR/>
   *
   * @param string $sFileCipher Encrypted file handle (hex-encoded)
   * @return array|mixed File handle and authorization details
   */
  private function decryptFileHandle( $sFileCipher )
  {
    $sKey = mhash_keygen_s2k ( MHASH_SHA256, $this->amCONFIG['secret_public'], null, 32 );
    $asFileHandle = explode( ',', mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $sKey, pack("H*" , $sFileCipher ), MCRYPT_MODE_ECB ), 4 );
    if( count( $asFileHandle ) != 4 or
        substr( $asFileHandle[0], 0, 4 ) != '{ID}' or
        substr( $asFileHandle[1], 0, 6 ) != '{USER}' or
        substr( $asFileHandle[2], 0, 7 ) != '{ADMIN}' or
        substr( $asFileHandle[3], 0, 6 ) != '{SALT}' )
    {
      trigger_error( '['.__METHOD__.'] Invalid cipher', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
    }
    $amFileHandle['handle'] = (integer)substr( $asFileHandle[0], 4 );
    $amFileHandle['user'] = substr( $asFileHandle[1], 6 );
    $amFileHandle['admin'] = substr( $asFileHandle[2], 7 ) == 'yes' ? true : false;
    return $amFileHandle;
  }

  /** Validate file handle
   *
   * <P><B>SYNOPSIS:</B> This function validates the decrypted file handle for the
   * authenticated user and given context.</P>
   *
   * @param array|mixed $amFileHandle Decrypted file handle and authorization details
   * @param boolean $bAdmin Administrative context
   * @param boolean $bPublic Public file
   * @return integer File handle
   * @see FEXP::decryptFileHandle()
   */
  private function validateFileHandle( $amFileHandle, $bAdmin = false, $bPublic = false )
  {
    $sAuthenticatedUser = $this->getAuthenticatedUser();
    if( !in_array( $sAuthenticatedUser, $this->amCONFIG['superusers'] )
        and ( ( !$bPublic and $amFileHandle['user'] != $sAuthenticatedUser )
              or ( $bAdmin and !$amFileHandle['admin'] ) )
        )
    {
      trigger_error( '['.__METHOD__.'] Invalid handle; '.$sAuthenticatedUser.'@'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown' ), E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:invalid_handle' ) );
    }
    return $amFileHandle['handle'];
  }

  /** Retrieve file path
   *
   * <P><B>SYNOPSIS:</B> This function returns relative path for the given file hash,
   * dir-split every 4 characters (to avoid overloading the filesystem's directory indices).</P>
   *
   * @param string $sFileHash File hash (hex-encoded)
   * @return string File path (relative)
   * @see FEXP::getFileHash()
   */
  private function getFilePath( $sFilehash )
  {
    return preg_replace( '/([A-F0-9]{4})/', '/$1', $sFilehash );
  }

  /** Check whether the file exists
   *
   * @param string $sFilePath File path (relative)
   * @return boolean File existance status
   * @see FEXP::getFilePath()
   */
  private function fileExists( $sFilePath )
  {
    $sFilePath = $this->amCONFIG['dir_files'].$sFilePath;
    return file_exists( $sFilePath );
  }

  /** Store file
   *
   * <P><B>SYNOPSIS:</B> This function stores the given file (from its uploaded sibling).
   * The file integrity will be checked using the provided MD5 checksum before completion.</P>
   *
   * @param string $sFilePath File path (relative)
   * @param string $sUploadPath Uploaded file path (temporary)
   * @return string File path (absolute)
   * @see FEXP::getFilePath()
   */
  private function fileStore( $sFilePath, $sUploadPath )
  {
    // Absolute file path
    $sFilePath = $this->amCONFIG['dir_files'].$sFilePath;
    $sFileDir = dirname( $sFilePath );

    // Create directory hierarchy
    if( !is_dir( $sFileDir ) && mkdir( $sFileDir, 0700, true ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to create file directory; '.$sFileDir, E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }

    // Move uploaded file
    if( move_uploaded_file( $sUploadPath, $sFilePath ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to store uploaded file; '.$sUploadPath.'->'.$sFilePath, E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    if( chmod( $sFilePath, 0600 ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to change file permissions; '.$sFilePath, E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }

    // Return absolute path
    return $sFilePath;
  }

  /** Retrieve the file MD5 checksum
   *
   *
   * @param string $sFilePath File path (relative)
   * @return string File MD5 checksum (hex-encoded, lowercase)
   * @see FEXP::getFilePath()
   */
  private function fileMD5( $sFilePath )
  {
    $sFilePath = $this->amCONFIG['dir_files'].$sFilePath;
    $sFileMD5 = md5_file( $sFilePath );
    if( $sFileMD5 === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to retrieve file MD5 checksum; '.$sFilePath, E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    return strtolower( $sFileMD5 );
  }

  /** Delete file
   *
   * <P><B>SYNOPSIS:</B> This function deletes the given file and all its directory
   * hierarchy.</P>
   *
   * @param string $sFilePath File path (relative)
   * @see FEXP::getFilePath()
   */
  private function fileDelete( $sFilePath )
  {
    // Absolute file path
    $sFileDir = dirname( $sFilePath );
    $sFilePath = $this->amCONFIG['dir_files'].$sFilePath;

    // Delete file and directory hierarchy
    if( file_exists( $sFilePath ) and unlink( $sFilePath ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to delete file; '.$sFilePath, E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    $iMaxRecursion = 16;  // let's make sure we don't ge into an infinite loop
    while( substr( $sFileDir, -1 ) != '/' and $iMaxRecursion-- )
    {
      if( @rmdir( $this->amCONFIG['dir_files'].$sFileDir ) === false ) break;
      $sFileDir = dirname( $sFileDir );
    }
  }


  /*
   * METHODS: Database
   ********************************************************************************/

  /** Open the database connection
   */
  private function databaseOpen()
  {
    // Connect
    try
    {
      $oPDO = new PDO( $this->amCONFIG['sql_dsn'], $this->amCONFIG['sql_username'], $this->amCONFIG['sql_password'], $this->amCONFIG['sql_options'] );
      $oPDO->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      if( !empty( $this->amCONFIG['sql_prepare'] ) )
      {
        //echo nl2br( var_export( $this->amCONFIG['sql_prepare'], true ) ); // DEBUG
        $oPDO->exec( $this->amCONFIG['sql_prepare'] );
      }
    }
    catch( PDOException $e )
    {
      if( $oPDO instanceof PDO )
      {
        $amErrorInfo = $oPDO->errorInfo();
        trigger_error( '['.__METHOD__.'] Failed to connect to database; '.( is_array( $amErrorInfo ) ? $amErrorInfo[2] : 'no error code/info'), E_USER_WARNING );
      }
      else
      {
        trigger_error( '['.__METHOD__.'] Failed to connect to database; '.$e->getMessage(), E_USER_WARNING );
      }
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }

    // Save database handle (PDO object)
    $this->oPDO = $oPDO;
  }

  /** Execute the given SQL command (function) on the database
   *
   * @param string $sSQL SQL snippet
   * @return string Command result
   */
  private function databaseExecute( $sSQL )
  {
    // Execute
    try
    {
      // Query
      try
      {
        //echo nl2br( var_export( $sSQL, true ) ); // DEBUG
        $oQuery = $this->oPDO->query( $sSQL );
        $sResult = $oQuery->fetchColumn();
        //echo nl2br( var_export( $sResult, true ) ); // DEBUG
      }
      catch( PDOException $e )
      {
        if( $oQuery instanceof PDOStatement )
        {
          $amErrorInfo = $oQuery->errorInfo();
          trigger_error( '['.__METHOD__.'] Failed to send command to database; '.( is_array( $amErrorInfo ) ? $amErrorInfo[2] : 'no error code/info'), E_USER_WARNING );
        }
        else
        {
          trigger_error( '['.__METHOD__.'] Failed to send command to database', E_USER_WARNING );
        }
        throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
      }
      $oQuery = null;

      // Check result
      if( (boolean)(integer)$sResult !== true )
      {
        trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
        throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
      }

    }
    catch( Exception $e )
    {
      $oQuery = null;
      throw $e;
    }

    // Return result
    return $sResult;
  }

  /** Query (select) data from the database
   *
   * @param string $sSQL SQL snippet
   * @return PDOStatement Resultset
   */
  private function databaseQuery( $sSQL )
  {
    // Execute
    try
    {
      // Query
      try
      {
        //echo nl2br( var_export( $sSQL, true ) ); // DEBUG
        $oQuery = $this->oPDO->query( $sSQL );
      }
      catch( PDOException $e )
      {
        if( $oQuery instanceof PDOStatement )
        {
          $amErrorInfo = $oQuery->errorInfo();
          trigger_error( '['.__METHOD__.'] Failed to query database; '.( is_array( $amErrorInfo ) ? $amErrorInfo[2] : 'no error code/info'), E_USER_WARNING );
        }
        else
        {
          trigger_error( '['.__METHOD__.'] Failed to query database', E_USER_WARNING );
        }
        throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
      }

    }
    catch( Exception $e )
    {
      $oQuery = null;
      throw $e;
    }

    // Return resultset (PDOStatement)
    return $oQuery;
  }

  /** Submit file creation event to the database
   *
   * @param string $sFileHash File hash (hex-encoded)
   * @param string $sFileName File name
   * @param integer $iFileSize File size (bytes)
   * @param string $sFileMD5 File MD5 checksum (hex-encoded)
   * @param string $sUploadUser Uploader descriptor
   * @param integer $iExpireTimestamp Expiration (file automatic deletion) timestamp (Unix epoch)
   * @param boolean $bOptionPublic Public access (any authenticated user)
   * @param boolean $bOptionUnique Unique access (automatic deletion after completion; single downloader only)
   * @param boolean $bOptionMultiple Multiple access (per downloader)
   * @return integer File handle
   * @see FEXP::getFileHash()
   */
  private function databaseExecuteFileUpload( $sFileHash, $sFileName, $iFileSize, $sFileMD5, $sUploadUser, $iExpireTimestamp, $bOptionPublic, $bOptionUnique, $bOptionMultiple )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_File_upload(';
    $sSQL .= $this->oPDO->quote( $sFileHash, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( $sFileName, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( $iFileSize, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sFileMD5, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s' ), PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( $sUploadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown', PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iExpireTimestamp ), PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( $bOptionPublic, PDO::PARAM_BOOL );
    $sSQL .= ','.$this->oPDO->quote( $bOptionUnique, PDO::PARAM_BOOL );
    $sSQL .= ','.$this->oPDO->quote( $bOptionMultiple, PDO::PARAM_BOOL );
    $sSQL .= ')';

    // Execute
    $iFileHandle = (integer)$this->databaseExecute( $sSQL );
    if( $iFileHandle <= 0 )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }

    // Return file handle
    return $iFileHandle;
  }

  /** Submit file update event to the database
   *
   * @param integer $iFileHandle File handle
   * @param boolean $bOptionPublic Public access (any authenticated user)
   * @param boolean $bOptionUnique Unique access (automatic deletion after completion; single downloader only)
   * @param boolean $bOptionMultiple Multiple access (per downloader)
   */
  private function databaseExecuteFileUpdate( $iFileHandle, $bOptionPublic, $bOptionUnique, $bOptionMultiple )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_File_update(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $bOptionPublic, PDO::PARAM_BOOL );
    $sSQL .= ','.$this->oPDO->quote( $bOptionUnique, PDO::PARAM_BOOL );
    $sSQL .= ','.$this->oPDO->quote( $bOptionMultiple, PDO::PARAM_BOOL );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit file deletion event to the database
   *
   * @param integer $iFileHandle File handle
   */
  private function databaseExecuteFileDelete( $iFileHandle )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_File_delete(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Query file details from the database
   *
   * <P><B>COLUMNS</B>:<BR/>
   * - <SAMP>file_handle</SAMP>: File handle<BR/>
   * - <SAMP>file_hash</SAMP>: File hash (hex-encoded)<BR/>
   * - <SAMP>file_name</SAMP>: File name (original)<BR/>
   * - <SAMP>file_size</SAMP>: File size (bytes)<BR/>
   * - <SAMP>file_md5</SAMP>: File MD5 checksum (hex-encoded)<BR/>
   * - <SAMP>upload_user</SAMP>: Uploader descriptor<BR/>
   * - <SAMP>upload_ip</SAMP>: Uploader IP address<BR/>
   * - <SAMP>upload_timestamp</SAMP>: Upload timestamp (ISO-formatted)<BR/>
   * - <SAMP>expire_timestamp</SAMP>: Expiration (file automatic deletion) timestamp (ISO-formatted)<BR/>
   * - <SAMP>option_public</SAMP>: Public access (any authenticated user)<BR/>
   * - <SAMP>option_unique</SAMP>: Unique access (automatic deletion after completion; single downloader only)<BR/>
   * - <SAMP>option_multiple</SAMP>: Multiple access (per downloader)</P>
   * - <SAMP>download_usercount</SAMP>: Count of downloaders</P>
   * - <SAMP>download_count</SAMP>: Overall count of completed downloads</P>
   *
   * @param integer $iFileHandle File handle
   * @param boolean $bThrowException Throw exception if no data are found
   * @return array|mixed|PDOStatement File data
   */
  private function databaseQueryFileData( $mFileHandle, $bThrowException = true )
  {
    // SQL command
    $sSQL = 'SELECT ';
    $sSQL .= 'pk AS file_handle';
    $sSQL .= ',vc_File_hash AS file_hash';
    $sSQL .= ',vc_File_name AS file_name';
    $sSQL .= ',i_File_size AS file_size';
    $sSQL .= ',vc_File_md5 AS file_md5';
    $sSQL .= ',ts_Upload AS upload_timestamp';
    $sSQL .= ',vc_Upload_user AS upload_user';
    $sSQL .= ',vc_Upload_ip AS upload_ip';
    $sSQL .= ',ts_Expire AS expire_timestamp';
    $sSQL .= ',b_Option_public AS option_public';
    $sSQL .= ',b_Option_unique AS option_unique';
    $sSQL .= ',b_Option_multiple AS option_multiple';
    $sSQL .= ',i_Download_usercount AS download_usercount';
    $sSQL .= ',i_Download_count AS download_count';
    $sSQL .= ' FROM tb_FEXP_File ';
    if( is_int( $mFileHandle ) )
    {
      $sSQL .= ' WHERE pk = '.$this->oPDO->quote( $mFileHandle, PDO::PARAM_INT );
      $bReturnResultset = false;
    }
    else
    {
      $sWildCard = $this->oPDO->quote( '%', PDO::PARAM_STR );
      $sAuthenticatedUser = $this->getAuthenticatedUser();
      if( in_array( $sAuthenticatedUser, $this->amCONFIG['superusers'] ) )
      {
        if( !empty( $mFileHandle ) )
        {
          $sSQL .= ' WHERE vc_Upload_user LIKE CONCAT( '.$sWildCard.', '.$this->oPDO->quote( $mFileHandle, PDO::PARAM_STR ).', '.$sWildCard.' )';
          $sSQL .= ' OR LOWER( vc_File_name ) LIKE CONCAT( '.$sWildCard.', '.$this->oPDO->quote( strtolower( $mFileHandle ), PDO::PARAM_STR ).', '.$sWildCard.' )';
        }
      }
      else
      {
          $sSQL .= ' WHERE vc_Upload_user = '.$this->oPDO->quote( $sAuthenticatedUser, PDO::PARAM_STR );
          if( !empty( $mFileHandle ) )
          {
            $sSQL .= ' AND LOWER( vc_File_name ) LIKE CONCAT( '.$sWildCard.', '.$this->oPDO->quote( strtolower( $mFileHandle ), PDO::PARAM_STR ).', '.$sWildCard.' )';
          }
      }
      $sSQL .= ' LIMIT 100';
      $bReturnResultset = true;
    }

    // Query
    $oResultset = $this->databaseQuery( $sSQL );
    if( $bReturnResultset ) return $oResultset;
    $amFileData = $oResultset->fetch( PDO::FETCH_ASSOC );
    $oResultset = null;
    if( $amFileData === false and $bThrowException )
    {
      trigger_error( '['.__METHOD__.'] Failed to retrieve file data', E_USER_NOTICE );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    return $amFileData;
  }

  /** Query expired files from the database
   *
   * <P><B>COLUMNS</B>:<BR/>
   * - <SAMP>file_handle</SAMP>: File handle<BR/>
   * - <SAMP>file_hash</SAMP>: File hash (hex-encoded)<BR/>
   * - <SAMP>expire_timestamp</SAMP>: Expiration (file automatic deletion) timestamp (ISO-formatted)<BR/>
   *
   * @return PDOStatement File data
   */
  private function databaseQueryFileExpired()
  {
    // SQL command
    $sSQL = 'SELECT ';
    $sSQL .= 'pk AS file_handle';
    $sSQL .= ',vc_File_hash AS file_hash';
    $sSQL .= ',ts_Expire AS expire_timestamp';
    $sSQL .= ' FROM tb_FEXP_File ';
    $sSQL .= ' WHERE ts_Expire < '.$this->oPDO->quote( date( 'Y-m-d H:i:s' ), PDO::PARAM_STR );

    // Query
    return $this->databaseQuery( $sSQL );
  }

  /** Submit (file/downloader) access authorization event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iExpireTimestamp (Updated) expiration (file automatic deletion) timestamp (Unix epoch)
   */
  private function databaseExecuteAccessAuthorize( $iFileHandle, $sDownloadUser, $iExpireTimestamp )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Access_authorize(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iExpireTimestamp ), PDO::PARAM_STR );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit (file/downloader) access block event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   */
  private function databaseExecuteAccessBlock( $iFileHandle, $sDownloadUser )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Access_block(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit (file/downloader) access (download) start event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iTimestamp Event timestamp (Unix epoch)
   */
  private function databaseExecuteAccessStart( $iFileHandle, $sDownloadUser, $iTimestamp )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Access_start(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown', PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iTimestamp ), PDO::PARAM_INT );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit (file/downloader) access (download) progress event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iTimestamp Event timestamp (Unix epoch)
   * @param integer $iBytes Downloaded bytes
   */
  private function databaseExecuteAccessProgress( $iFileHandle, $sDownloadUser, $iTimestamp, $iBytes )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Access_progress(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iTimestamp ), PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $iBytes, PDO::PARAM_INT );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit (file/downloader) access (download) completion event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iTimestamp Event timestamp (Unix epoch)
   */
  private function databaseExecuteAccessComplete( $iFileHandle, $sDownloadUser, $iTimestamp )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Access_complete(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iTimestamp ), PDO::PARAM_INT );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Query (file/downloader) access details from the database
   *
   * <P><B>COLUMNS</B>:<BR/>
   * - <SAMP>file_handle</SAMP>: File handle<BR/>
   * - <SAMP>download_user</SAMP>: Downloader descriptor<BR/>
   * - <SAMP>download_ip</SAMP>: Downloader IP address<BR/>
   * - <SAMP>download_start</SAMP>: Last/current access (download) start timestamp (ISO-formatted)<BR/>
   * - <SAMP>download_progress</SAMP>: Last/current access (download) progress timestamp (ISO-formatted)<BR/>
   * - <SAMP>download_bytes</SAMP>: Last/current access (download) progress (downloaded bytes)<BR/>
   * - <SAMP>download_complete</SAMP>: Last access (download) completion timestamp (ISO-formatted)<BR/>
   * - <SAMP>download_count</SAMP>: Access (completed download) count<BR/>
   * - <SAMP>download_block</SAMP>: Access (download) block</P>
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param boolean $bThrowException Throw exception if no data are found
   * @return array|mixed|PDOStatement Access data
   */
  private function databaseQueryAccessData( $iFileHandle, $sDownloadUser = null, $bThrowException = true )
  {
    // SQL command
    $sSQL = 'SELECT ';
    $sSQL .= 'fk AS file_handle';
    $sSQL .= ',vc_Download_user AS download_user';
    $sSQL .= ',vc_Download_ip AS download_ip';
    $sSQL .= ',ts_Download_start AS download_start';
    $sSQL .= ',ts_Download_progress AS download_progress';
    $sSQL .= ',i_Download_progress AS download_bytes';
    $sSQL .= ',ts_Download_complete AS download_complete';
    $sSQL .= ',i_Download_count AS download_count';
    $sSQL .= ',b_Download_block AS download_block';
    $sSQL .= ' FROM tb_FEXP_Access';
    $sSQL .= ' WHERE fk = '.$this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    if( !is_null( $sDownloadUser ) )
    {
      $sSQL .= ' AND vc_Download_user = '.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
      $bReturnResultset = false;
    }
    else
    {
      $bReturnResultset = true;
    }

    // Query
    $oResultset = $this->databaseQuery( $sSQL );
    if( $bReturnResultset ) return $oResultset;
    $amAccessData = $oResultset->fetch( PDO::FETCH_ASSOC );
    $oResultset = null;
    if( $amAccessData === false and $bThrowException )
    {
      trigger_error( '['.__METHOD__.'] Failed to retrieve access data', E_USER_NOTICE );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    return $amAccessData;
  }

  /** Submit (file/downloader) access (download) start log event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iDownloadStart Access (download) start timestamp (Unix epoch)
   */
  private function databaseExecuteLogStart( $iFileHandle, $sDownloadUser, $iDownloadStart )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Log_start(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown', PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iDownloadStart ), PDO::PARAM_STR );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit (file/downloader) access (download) progress log event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iDownloadStart Access (download) start timestamp (Unix epoch)
   * @param integer $iTimestamp Event timestamp (Unix epoch)
   * @param integer $iBytes Downloaded bytes
   */
  private function databaseExecuteLogProgress( $iFileHandle, $sDownloadUser, $iDownloadStart, $iTimestamp, $iBytes )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Log_progress(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown', PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iDownloadStart ), PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iTimestamp ), PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $iBytes, PDO::PARAM_INT );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Submit (file/downloader) access (download) completion log event to the database
   *
   * @param integer $iFileHandle File handle
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iDownloadStart Access (download) start timestamp (Unix epoch)
   * @param integer $iTimestamp Event timestamp (Unix epoch)
   */
  private function databaseExecuteLogComplete( $iFileHandle, $sDownloadUser, $iDownloadStart, $iTimestamp )
  {
    // SQL command
    $sSQL = 'SELECT fn_FEXP_Log_complete(';
    $sSQL .= $this->oPDO->quote( $iFileHandle, PDO::PARAM_INT );
    $sSQL .= ','.$this->oPDO->quote( $sDownloadUser, PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown', PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iDownloadStart ), PDO::PARAM_STR );
    $sSQL .= ','.$this->oPDO->quote( date( 'Y-m-d H:i:s', $iTimestamp ), PDO::PARAM_INT );
    $sSQL .= ')';

    // Execute
    if( !(boolean)(integer)$this->databaseExecute( $sSQL ) )
    {
      trigger_error( '['.__METHOD__.'] Invalid result from database function', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
  }

  /** Close the database connection
   */
  private function databaseClose()
  {
    // Clear database handle (PDO object)
    $this->oPDO = null;
  }


  /*
   * METHODS: E-Mail
   ********************************************************************************/

  /** Parses and returns the components of the supplied e-mail template
   *
   * <P><B>SYNOPSIS:</B> This function takes an e-mail template as argument, similar
   * to example below (extra white-spaces are automatically stripped off):</P>
   * <PRE>
   * #{SUBJECT}
   * Subject line (including variable: name => @{name})
   *
   * #{TEXT}
   * Text-formatted message body (including variable: name => @{name})
   *
   * #{HTML}
   * <P>HTML-formatted message body (including variable: name => @{name})</P>
   * </PRE>
   * <P><B>RETURNS:</B> An array associating:</P>
   * <UL>
   * <LI><SAMP>subject</SAMP> => subject line (<SAMP>null</SAMP> if none), including variables substitutions</LI>
   * <LI><SAMP>text</SAMP> => text-formatted message body (<SAMP>null</SAMP> if none), including variables substitutions</LI>
   * <LI><SAMP>html</SAMP> => HTML-formatted message body (<SAMP>null</SAMP> if none), including variables substitutions</LI>
   * </UL>
   *
   * @param string $sTemplate E-mail template
   * @param array|string $asVariables Substitution variables (associating <SAMP>name</SAMP> => <SAMP>value</SAMP>)
   * @return array|string
   */
  private static function mailParseTemplate( $sTemplate, $asVariables = null )
  {
    // Sanitize input
    if( !is_array( $asVariables ) )
      $asVariables = array();

    // Output
    $asOutput = array( 'subject' => null, 'text' => null, 'html' => null );
    
    // ... search patterns
    $asSearch = array_keys( $asVariables );
    foreach( $asSearch as &$roSearch ) $roSearch = '@{'.$roSearch.'}';

    // ... split content
    $iPosition_subject = stripos( $sTemplate, '#{subject}' );
    $iPosition_text = stripos( $sTemplate, '#{text}' );
    $iPosition_html = stripos( $sTemplate, '#{html}' );

    // ... subject
    if( $iPosition_subject !== false )
    {
      $iPosition_subject += 10;
      $iPosition_end = strlen( $sTemplate );
      if( $iPosition_text !== false and $iPosition_text > $iPosition_subject and $iPosition_text < $iPosition_end )
        $iPosition_end = $iPosition_text;
      if( $iPosition_html !== false and $iPosition_html > $iPosition_subject and $iPosition_html < $iPosition_end )
        $iPosition_end = $iPosition_html;
      $asOutput['subject'] = str_ireplace( $asSearch, $asVariables, trim( substr( $sTemplate, $iPosition_subject, $iPosition_end - $iPosition_subject ) ) );
      
    }

    // ... body
    if( $iPosition_text !== false )
    {
      $iPosition_text += 7;
      $iPosition_end = strlen( $sTemplate );
      if( $iPosition_text !== false and $iPosition_text > $iPosition_text and $iPosition_text < $iPosition_end )
        $iPosition_end = $iPosition_text;
      if( $iPosition_html !== false and $iPosition_html > $iPosition_text and $iPosition_html < $iPosition_end )
        $iPosition_end = $iPosition_html;
      $asOutput['text'] = str_ireplace( $asSearch, $asVariables, trim( substr( $sTemplate, $iPosition_text, $iPosition_end - $iPosition_text ) ) );
    }

    // ... body (HTML)
    if( $iPosition_html !== false )
    {
      $iPosition_html += 7;
      $iPosition_end = strlen( $sTemplate );
      if( $iPosition_subject !== false and $iPosition_subject > $iPosition_html and $iPosition_subject < $iPosition_end )
        $iPosition_end = $iPosition_subject;
      if( $iPosition_text !== false and $iPosition_text > $iPosition_html and $iPosition_text < $iPosition_end )
        $iPosition_end = $iPosition_text;
      $asOutput['html'] = str_ireplace( $asSearch, array_map( 'nl2br', array_map( 'htmlentities', $asVariables ) ), trim( substr( $sTemplate, $iPosition_html, $iPosition_end - $iPosition_html ) ) );
    }

    // End
    return $asOutput;

  }

  /** Send e-mail message
   *
   * @param string $sTemplate E-mail template
   * @param string $sSender Sender's e-mail address (WARNING: make sure <SAMP>sendmail</SAMP> allows it to be overriden)
   * @param string $sRecipients Recipient(s) e-mail addresses (whitespace, comma, semi-colon or colon separated)
   * @param array|string $asVariables Substitution variables (associating <SAMP>name</SAMP> => <SAMP>value</SAMP>)
   * @param array|string $asHeaders Additional MIME headers (associating <SAMP>name</SAMP> => <SAMP>value</SAMP>)
   * @see FEXP::mailParseTemplate()
   */
  private function mailSend( $sTemplate, $sSender, $sRecipients, $asVariables = null, $asHeaders = null )
  {
    // Load PEAR::Mail and PEAR::Mail_Mime extensions
    require_once 'Mail.php';
    require_once 'Mail/mime.php';

    // Instantiate e-mail object
    $oMIME = new Mail_mime( "\n" );
    if( PEAR::isError( $oMIME ) )
    {
      trigger_error( '['.__METHOD__.'] Failed to instantiate MIME object; '.$oMIME->getMessage(), E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }

    // Parse e-mail template
    $sSubject = null;
    $asMailParts = self::mailParseTemplate( $sTemplate, $asVariables );
    if( isset( $asMailParts['subject'] ) )
      $sSubject = $asMailParts['subject'];
    if( isset( $asMailParts['text'] ) )
      $oMIME->setTXTBody( $asMailParts['text'] );
    if( isset( $asMailParts['html'] ) )
      $oMIME->setHTMLBody( $asMailParts['html'] );
    unset( $asMailParts );

    // Mail components
    // ... body
    $sBody = $oMIME->get();
    // ... headers
    if( !is_array( $asHeaders ) ) $asHeaders = array();
    if( !empty( $sSender ) ) $asHeaders['From'] = $sSender;
    if( !empty( $sSubject ) ) $asHeaders['Subject'] = $sSubject;
    if( empty( $asHeaders ) ) $asHeaders = null;
    $sHeaders = $oMIME->headers( $asHeaders );

    // Send
    $oMail = Mail::factory( 'mail' );
    if( PEAR::isError( $oMail ) )
    {
      trigger_error( '['.__METHOD__.'] Failed to instantiate mail object; '.$oMail->getMessage(), E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    if( is_array( $sRecipients ) ) $sRecipients = implode( ';', $sRecipients ); // make sure we have a string
    foreach( array_unique( preg_split( '/[ ,;:]+/', $sRecipients ) ) as $sRecipient )
    {
      if( empty( $sRecipient ) ) continue;
      //trigger_error( '['.__METHOD__.'] Sending mail; Recipient: '.$sRecipient, E_USER_NOTICE ); // DEBUG
      $oMail->send( $sRecipient, $sHeaders, $sBody );
    }
  }

  /** Send administrative e-mail
   *
   * @param string $sRecipients Recipient(s) e-mail addresses
   * @param array|mixed $amFileData File data
   * @see FEXP::databaseQueryFileData()
   */
  private function mailAdminLink( $sRecipient, $amFileData )
  {
    $sTemplate = file_get_contents( $this->amCONFIG['dir_resources'].'/'.$this->getLocaleCurrent().'/admin_link.email.tpl' );
    if( $sTemplate === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to load e-mail template', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    $asVariables = array( 'title' => $this->getLocaleText( 'title:file_exchange_platform' ),
                          'file_name' => $amFileData['file_name'],
                          'file_md5' => $amFileData['file_md5'],
                          'file_size' => $this->formatBytes( $amFileData['file_size'], false ).' / '.$this->formatBytes( $amFileData['file_size'], true ),
                          'upload_user' => $amFileData['upload_user'],
                          'upload_timestamp' => $amFileData['upload_timestamp'].( !empty( $this->amCONFIG['timezone'] ) ? ' - '.$this->amCONFIG['timezone'] : null ),
                          'expire_timestamp' => $amFileData['expire_timestamp'].( !empty( $this->amCONFIG['timezone'] ) ? ' - '.$this->amCONFIG['timezone'] : null ),
                          'url' => $this->amCONFIG['home_url'].'?view=admin&file='.$this->encryptFileHandle( $amFileData['file_handle'], $sRecipient, true )
                          );
    $this->mailSend( $sTemplate, $this->amCONFIG['notify_sender_address'], $sRecipient, $asVariables );
  }

  /** Send download e-mail
   *
   * @param string $sRecipients Recipient(s) e-mail addresses
   * @param array|mixed $amFileData File data
   * @param string $sMessage Additional message
   * @param boolean $bPublic Public file
   * @see FEXP::databaseQueryFileData()
   */
  private function mailDownloadLink( $sRecipient, $amFileData, $sMessage, $bPublic = false )
  {
    $sTemplate = file_get_contents( $this->amCONFIG['dir_resources'].'/'.$this->getLocaleCurrent().'/download_link.email.tpl' );
    if( $sTemplate === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to load e-mail template', E_USER_WARNING );
      throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
    }
    $asVariables = array( 'title' => $this->getLocaleText( 'title:file_exchange_platform' ),
                          'file_name' => $amFileData['file_name'],
                          'file_md5' => $amFileData['file_md5'],
                          'file_size' => $this->formatBytes( $amFileData['file_size'], false ).' / '.$this->formatBytes( $amFileData['file_size'], true ),
                          'upload_user' => $amFileData['upload_user'],
                          'upload_timestamp' => $amFileData['upload_timestamp'].( !empty( $this->amCONFIG['timezone'] ) ? ' - '.$this->amCONFIG['timezone'] : null ),
                          'expire_timestamp' => $amFileData['expire_timestamp'].( !empty( $this->amCONFIG['timezone'] ) ? ' - '.$this->amCONFIG['timezone'] : null ),
                          'url' => $this->amCONFIG['home_url'].'?download='.$this->encryptFileHandle( $amFileData['file_handle'], $bPublic ? 'PUBLIC' : $sRecipient, false ),
                          'message' => $sMessage
                          );
    $this->mailSend( $sTemplate, $this->amCONFIG['notify_sender_address'], $sRecipient, $asVariables );
  }

  /** Send event notification e-mail
   *
   * @param string $sEvent Event descriptor
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iBytes File size or downloaded bytes
   */
  private function mailEventNotification( $sEvent,$sFileName, $sUploadUser, $sDownloadUser, $iBytes )
  {
    $sTemplate = file_get_contents( $this->amCONFIG['dir_resources'].'/'.$this->getLocaleCurrent().'/event_notification.email.tpl' );
    if( $sTemplate === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to load e-mail template', E_USER_WARNING );
      return; // let's not throw an error and block the entire service at this level
    }
    $this->mailSend( $sTemplate, $this->amCONFIG['notify_sender_address'], $this->amCONFIG['notify_recipient_address'],
                     array( 'title' => $this->getLocaleText( 'title:file_exchange_platform' ),
                            'event' => $sEvent,
                            'timestamp' => date('Y-m-d H:i:s').( !empty( $this->amCONFIG['timezone'] ) ? ' - '.$this->amCONFIG['timezone'] : null ),
                            'file_name' => $sFileName,
                            'upload_user' => $sUploadUser,
                            'download_user' => $sDownloadUser,
                            'bytes' => is_int( $iBytes ) ? $this->formatBytes( $iBytes, false ).' / '.$this->formatBytes( $iBytes, true ) : $iBytes,
                            'ip' => isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown'
                            )
                     );
  }


  /*
   * METHODS: Notification/Logs
   ********************************************************************************/

  /** Send message to PHP log
   *
   * @param string $sMessage Log message
   */
  private function logEventToPhp( $sMessage )
  {
    trigger_error( '['.__CLASS__.'] '.$sMessage, E_USER_WARNING );
  }

  /** Send message to file log
   *
   * @param string $sMessage Log message
   */
  private function logEventToFile( $sMessage )
  {
    $sLogFile = $this->amCONFIG['dir_logs'].'/fexp.log';
    $rLogFile = fopen( $sLogFile, 'a' );
    if( $rLogFile === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to open log file; '.$sLogFile, E_USER_WARNING );
      return; // let's not throw an error and block the entire service at this level
    }
    if( fwrite( $rLogFile, date('c').'|'.$sMessage."\n" ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to open log file; '.$sLogFile, E_USER_WARNING );
    }
    fclose( $rLogFile );
  }

  /** Send message to system log
   *
   * @param string $sMessage Log message
   */
  private function logEventToSyslog( $sMessage )
  {
    $iFacility = $this->amCONFIG['log_syslog_facility'];
    if( openlog( __CLASS__, LOG_CONS|LOG_NDELAY|LOG_PID, $iFacility ) === false )
    {
      trigger_error( '['.__METHOD__.'] Failed to open syslog; '.$iFacility, E_USER_WARNING );
      return; // let's not throw an error and block the entire service at this level
    }
    syslog( LOG_INFO, $sMessage );
    closelog();
  }

  /** Check whether any log destination is enabled
   *
   * @return boolean Log enable status
   */
  private function logEventEnabled()
  {
    return( $this->amCONFIG['log_php']
            || $this->amCONFIG['log_file']
            || $this->amCONFIG['log_syslog'] );
  }

  /** Send message to all (enabled) log destination
   *
   * @param string $sMessage Log message
   */
  private function logEvent( $sMessage )
  {
    if( $this->amCONFIG['log_php'] ) $this->logEventToPhp( $sMessage );
    if( $this->amCONFIG['log_file'] ) $this->logEventToFile( $sMessage );
    if( $this->amCONFIG['log_syslog'] ) $this->logEventToSyslog( $sMessage );
  }

  /** File upload event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param integer $iFileSize File size (in bytes)
   */
  private function notifyEventFileUpload( $iFileHandle, $sFileName, $sUploadUser, $iFileSize )
  {
    if( !empty( $this->amCONFIG['notify_recipient_address'] ) and $this->amCONFIG['notify_upload'] )
    {
      $this->mailEventNotification( 'UPLOAD', $sFileName, $sUploadUser, '-', $iFileSize );
    }
    if( $this->amCONFIG['log_event_upload'] and $this->logEventEnabled() )
    {
      $sMessage = 'UPLOAD|'.$sFileName.'|'.$sUploadUser.'|-|'.$iFileSize.'|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
      $this->logEvent( $sMessage );
    }
  }

  /** File deletion event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   */
  private function notifyEventFileDelete( $iFileHandle, $sFileName, $sUploadUser )
  {
    if( !empty( $this->amCONFIG['notify_recipient_address'] ) and $this->amCONFIG['notify_delete'] )
    {
      $this->mailEventNotification( 'DELETE', $sFileName, $sUploadUser, '-', '-' );
    }
    if( $this->amCONFIG['log_event_delete'] and $this->logEventEnabled() )
    {
      $sMessage = 'DELETE|'.$sFileName.'|'.$sUploadUser.'|-|-|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
      $this->logEvent( $sMessage );
    }
  }

  /** Access authorization event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param string $sDownloadUser Downloader descriptor
   */
  private function notifyEventAccessAuthorize( $iFileHandle, $sFileName, $sUploadUser, $sDownloadUser )
  {
    if( !empty( $this->amCONFIG['notify_recipient_address'] ) and $this->amCONFIG['notify_authorize'] )
    {
      $this->mailEventNotification( 'AUTHORIZE', $sFileName, $sUploadUser, $sDownloadUser, '-' );
    }
    if( $this->amCONFIG['log_event_authorize'] and $this->logEventEnabled() )
    {
      $sMessage = 'AUTHORIZE|'.$sFileName.'|'.$sUploadUser.'|'.$sDownloadUser.'|-|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
      $this->logEvent( $sMessage );
    }
  }

  /** Access blocking event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param string $sDownloadUser Downloader descriptor
   */
  private function notifyEventAccessBlock( $iFileHandle, $sFileName, $sUploadUser, $sDownloadUser )
  {
    if( !empty( $this->amCONFIG['notify_recipient_address'] ) and $this->amCONFIG['notify_block'] )
    {
      $this->mailEventNotification( 'BLOCK', $sFileName, $sUploadUser, $sDownloadUser, '-' );
    }
    if( $this->amCONFIG['log_event_block'] and $this->logEventEnabled() )
    {
      $sMessage = 'BLOCK|'.$sFileName.'|'.$sUploadUser.'|'.$sDownloadUser.'|-|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
      $this->logEvent( $sMessage );
    }
  }

  /** Access (download) start event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iDownloadStart Download start timestamp (Unix epoch)
   */
  private function notifyEventAccessStart( $iFileHandle, $sFileName, $sUploadUser, $sDownloadUser, $iDownloadStart )
  {
    if( !empty( $this->amCONFIG['notify_recipient_address'] ) and $this->amCONFIG['notify_start'] )
    {
      $this->mailEventNotification( 'START', $sFileName, $sUploadUser, $sDownloadUser, '-' );
    }
    if( $this->amCONFIG['log_event_start'] )
    {
      if( $this->amCONFIG['log_database'] )
      {
        $this->databaseExecuteLogStart( $iFileHandle, $sDownloadUser, $iDownloadStart );
      }
      if( $this->logEventEnabled() )
      {
        $sMessage = 'START|'.$sFileName.'|'.$sUploadUser.'|'.$sDownloadUser.'|0|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
        $this->logEvent( $sMessage );
      }
    }
  }

  /** Access (download) progress event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iDownloadStart Download start timestamp (Unix epoch)
   * @param integer $iTimestamp Progress timestamp (Unix epoch)
   * @param integer $iBytes Downloaded bytes
   */
  private function notifyEventAccessProgress( $iFileHandle, $sFileName, $sUploadUser, $sDownloadUser, $iDownloadStart, $iTimestamp, $iBytes )
  {
    // NOTE: we do not notify progress events per e-mail (to prevent mailbox clogging)
    if( $this->amCONFIG['log_event_progress'] )
    {
      if( $this->amCONFIG['log_database'] )
      {
        $this->databaseExecuteLogProgress( $iFileHandle, $sDownloadUser, $iDownloadStart, $iTimestamp, $iBytes );
      }
      if( $this->logEventEnabled() )
      {
        $sMessage = 'PROGRESS|'.$sFileName.'|'.$sUploadUser.'|'.$sDownloadUser.'|'.$iBytes.'|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
        $this->logEvent( $sMessage );
      }
    }
  }

  /** Access (download) completion event notification
   *
   * @param integer $iFileHandle File handle
   * @param string $sFileName File name
   * @param string $sUploadUser Uploader descriptor
   * @param string $sDownloadUser Downloader descriptor
   * @param integer $iDownloadStart Download start timestamp (Unix epoch)
   * @param integer $iTimestamp Completion timestamp (Unix epoch)
   * @param integer $iBytes File size (in bytes)
   */
  private function notifyEventAccessComplete( $iFileHandle, $sFileName, $sUploadUser, $sDownloadUser, $iDownloadStart, $iTimestamp, $iBytes )
  {
    if( !empty( $this->amCONFIG['notify_recipient_address'] ) and $this->amCONFIG['notify_complete'] )
    {
      $this->mailEventNotification( 'COMPLETE', $sFileName, $sUploadUser, $sDownloadUser, $iBytes );
    }
    if( $this->amCONFIG['log_event_complete'] )
    {
      if( $this->amCONFIG['log_database'] )
      {
        $this->databaseExecuteLogComplete( $iFileHandle, $sDownloadUser, $iDownloadStart, $iTimestamp );
      }
      if( $this->logEventEnabled() )
      {
        $sMessage = 'COMPLETE|'.$sFileName.'|'.$sUploadUser.'|'.$sDownloadUser.'|'.$iBytes.'|'.( isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '-' );
        $this->logEvent( $sMessage );
      }
    }
  }


  /*
   * METHODS: General
   ********************************************************************************/

  /** Format bytes in a user-friendly way
   *
   *  <P><B>SYNOPSIS:</B> Format bytes quantity to most appropriate scale (kB, MB, GB, etc).</P>
   *
   * @param integer $iBytes Bytes quantity
   * @param boolean $bBinaryBase Use binary base (kiB, MiB, GiB, etc.)
   * @return string Formatted bytes value
   */
  private function formatBytes( $iBytes, $bBinaryBase = false )
  {
    $iDivisor = $bBinaryBase ? 1024 : 1000;
    $iDivided = 0;
    $fBytes = (float)$iBytes;
    while( $fBytes > $iDivisor )
    {
      $fBytes /= $iDivisor;
      $iDivided += 1;
    }
    $sBytes = trim( round( $fBytes, 2 ) );
    switch( $iDivided )
    {
    case 0: $sBytes .= 'B'; break;
    case 1: $sBytes .= $bBinaryBase ? 'kiB' : 'kB'; break;
    case 2: $sBytes .= $bBinaryBase ? 'MiB' : 'MB'; break;
    case 3: $sBytes .= $bBinaryBase ? 'GiB' : 'GB'; break;
    case 4: $sBytes .= $bBinaryBase ? 'TiB' : 'TB'; break;
    case 5: $sBytes .= $bBinaryBase ? 'PiB' : 'PB'; break;
    case 6: $sBytes .= $bBinaryBase ? 'EiB' : 'EB'; break;
    case 7: $sBytes .= $bBinaryBase ? 'ZiB' : 'ZB'; break;
    case 8: $sBytes .= $bBinaryBase ? 'YiB' : 'YB'; break;
    default: $sBytes = $iBytes.'B'; // what the f***!?...
    }
    return $sBytes;
  }

  /** Reset session
   *
   *  <P><B>SYNOPSIS:</B> Clear the current session and regenerate a new session ID.</P>
   */
  private function resetSession()
  {
    // Save session locale and login URL
    $sLocale = $this->getLocaleCurrent();

    // Clear session and start a new one.
    session_regenerate_id( true );

    // Restore session locale and login URL
    $_SESSION['FEXP_Locale'] = $sLocale;
  }


  /*
   * METHODS: HTML
   ********************************************************************************/

  /** Retrieve authenticated used
   *
   */
  private function getAuthenticatedUser()
  {
    static $sAuthenticatedUser;

    if( is_null( $sAuthenticatedUser ) )
    {
      $sAuthenticatedUser = $_SERVER['PHP_AUTH_USER'];
      // ... handle certifate-based authentication
      $sAuthenticatedUser = preg_replace( ':(^|[,/])(emailAddress|E)=([^,/]*):ADi', '$3', $sAuthenticatedUser );
      // ... handle missing domain
      if( strpos( $sAuthenticatedUser, '@' ) === false and !empty( $this->amCONFIG['user_email_domain_default'] ) )
      {
        $sAuthenticatedUser .= '@'.$this->amCONFIG['user_email_domain_default'];
      }
      // ... validate
      if( !preg_match( '/^(\w+[-_.])*\w+@(\w+[-_.])*\w+\.\w+$/AD', $sAuthenticatedUser ) )
      {
        throw new Exception( $this->getLocaleText( 'error:invalid_credentials' ) );
      }
    }
    return strtolower( $sAuthenticatedUser );
  }

  /** Redirect/replace current page
   *
   * <P><B>SYNOPSIS:</B> This function redirects the client (browser) to the given URL
   * and replaces the current browsing history entry.</P>
   *
   * <P><B>WARNING:</B> The URL is NOT encoded to prevent Javascript injection. Use ONLY
   * for truted URLs!</P>
   *
   * @param string View ID (to display)
   */
  private function htmlRedirect( $sURL )
  {
    echo '<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">document.location.replace(\''.$sURL.'\');</SCRIPT>';
    session_write_close();
    exit;
  }

  /** HTML page controller (Model/View Controller)
   *
   * <P><B>SYNOPSIS:</B> This function invokes the controller implementing the logic
   * for the file exchange process and returns the HTML view that must displayed as
   * result. See the sample <SAMP>index.php</SAMP> file for usage example.</P>
   *
   * @return string View ID (to display)
   */
  public function htmlControlPage()
  {
    // Controller
    $sError = null;
    $sView = 'default';
    $amFormData = array();
    $bSessionBailOut = true;

    try
    {
      // Check encryption
      if( $this->amCONFIG['force_ssl'] and !isset( $_SERVER['HTTPS'] ) )
      {
        throw new Exception( $this->getLocaleText( 'error:unsecure_channel' ) );
      }

      // Check authentication
      if( !isset( $_SERVER['PHP_AUTH_USER'] ) )
      {
        throw new Exception( $this->getLocaleText( 'error:unauthenticated_channel' ) );
      }

      // Get user
      $sAuthenticatedUser = $this->getAuthenticatedUser();
      $sRemoteIP = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

      // Detect view/action
      $sAction = null;
      if( isset( $_POST['locale'] ) ) $sAction = 'locale';
      elseif( isset( $_FILES['upload'] ) ) $sAction = 'upload';
      elseif( isset( $_GET['view'] ) ) $sView = $_GET['view'];
      elseif( isset( $_POST['do'] ) ) $sAction = $_POST['do'];

      // Action
      switch( $sAction )
      {

      case 'locale':
        // Retrieve form variables
        if( !is_scalar( $_POST['locale'] ) )
        {
          trigger_error( '['.__METHOD__.'][action:locale] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }
        $sLocale = trim( $_POST['locale'] );

        // Check and set locale
        if( !in_array( $sLocale, $this->getLocaleSupported() ) )
        {
          trigger_error( '['.__METHOD__.'][action:locale] Invalid locale; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }
        $_SESSION['FEXP_Locale'] = $sLocale;

        // View
        $sParameters = null;
        if( isset( $_POST['view'] ) ) $sParameters .= ( !empty( $sParameters ) ? '&' : null ).'view='.urlencode( $_POST['view'] );
        if( isset( $_POST['file'] ) ) $sParameters .= ( !empty( $sParameters ) ? '&' : null ).'file='.urlencode( $_POST['file'] );
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].(!empty( $sParameters ) ? '?'.$sParameters : null ) );
        break;

      case 'upload':
        // View
        $sView = 'default';

        // Arguments
        if( !( isset( $_FILES['upload']['tmp_name'], $_FILES['upload']['name'], $_FILES['upload']['size'], $_POST['md5'] )
               and is_scalar( $_POST['md5'] ) )
            )
        {
          trigger_error( '['.__METHOD__.'][action:upload] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }

        // Upload/file data
        $sUploadPath = $_FILES['upload']['tmp_name'];
        if( !is_uploaded_file( $sUploadPath ) )
        {
          trigger_error( '['.__METHOD__.'][action:upload] Invalid upload file; '.$sUploadPath, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:internal_error' ) );
        }
        $sFileName = basename( $_FILES['upload']['name'] );
        $iFileSize = $_FILES['upload']['size'];
        $sFileMD5 = strtolower( trim( $_POST['md5'] ) );

        // Store file
        $sFilePath = null;
        $iFileHandle = null;
        try
        {
          // Check size
          if( $iFileSize > $this->amCONFIG['file_max_size'] )
          {
            $bSessionBailOut = false;
            trigger_error( '['.__METHOD__.'][action:upload] File is larger than maximum allowed size; '.$sAuthenticatedUser.'@'.$sRemoteIP.', '.$iFileSize, E_USER_NOTICE );
            throw new Exception( $this->getLocaleText( 'error:invalid_file_size' )."\n[".$this->formatBytes($this->amCONFIG['file_max_size'],false).' / '.$this->formatBytes($this->amCONFIG['file_max_size'],true).']' );
          }

          // Check file
          $sFileHash = $this->getFileHash( $sFileName, $sAuthenticatedUser );
          $sFilePath = $this->getFilePath( $sFileHash );
          if( $this->fileExists( $sFilePath ) )
          {
            $bSessionBailOut = false;
            $sFilePath = null; // prevent deletion by error handler
            trigger_error( '['.__METHOD__.'][action:upload] File already exists; '.$sAuthenticatedUser.'@'.$sRemoteIP.', '.$sFileName, E_USER_NOTICE );
            throw new Exception( $this->getLocaleText( 'error:invalid_file_exists' ) );
          }

          // Pre-check integrity
          $bPostCheckIntegrity = true;
          if( empty( $sFileMD5 ) )
          {
            $bPostCheckIntegrity = false;
            $sFileMD5 = strtolower( md5_file( $sUploadPath ) ); // create checksum if user did not supply it (in which case file upload corruption will NOT be detected!)
          }

          // Open database
          $this->databaseOpen();

          // Submit file to database
          $iUploadTimestamp = time();
          $iExpireTimestamp = $iUploadTimestamp + 86400*$this->amCONFIG['file_expire_delay'];
          $iFileHandle = $this->databaseExecuteFileUpload( $sFileHash, $sFileName, $iFileSize, $sFileMD5, $sAuthenticatedUser,
                                                          $iExpireTimestamp,
                                                          $this->amCONFIG['option_public_default'],
                                                          $this->amCONFIG['option_unique_default'],
                                                          $this->amCONFIG['option_multiple_default'] );

          // Save file
          $this->fileStore( $sFilePath, $sUploadPath, $sFileMD5 );

          // Post-check integrity
          if( $bPostCheckIntegrity and $this->fileMD5( $sFilePath ) != $sFileMD5 )
          {
            $bSessionBailOut = false;
            trigger_error( '['.__METHOD__.'][action:upload] File integrity error; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
            throw new Exception( $this->getLocaleText( 'error:file_corrupted' ) );
          }

          // Send administrative link
          $this->mailAdminLink( $sAuthenticatedUser,
                                array( 'file_handle' => $iFileHandle,
                                       'file_name' => $sFileName,
                                       'file_md5' => $sFileMD5,
                                       'file_size' => $iFileSize,
                                       'upload_user' => $sAuthenticatedUser,
                                       'upload_timestamp' => date( 'Y-m-d H:i:s', $iUploadTimestamp ),
                                       'expire_timestamp' => date( 'Y-m-d H:i:s', $iExpireTimestamp )
                                       )
                                );

          // Notifification
          $this->notifyEventFileUpload( $iFileHandle, $sFileName, $sAuthenticatedUser, $iFileSize );

          // Close database
          $this->databaseClose();
        }
        catch( Exception $e )
        {
          @unlink( $sUploadPath );
          if( !is_null( $sFilePath ) ) $this->fileDelete( $sFilePath );
          if( !is_null( $iFileHandle ) ) $this->databaseExecuteFileDelete( $iFileHandle );
          $this->databaseClose();
          throw $e;
        }

        // View
        $sFileHandle_encrypted = $this->encryptFileHandle( $iFileHandle, $sAuthenticatedUser, true );
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].'?view=admin&file='.$sFileHandle_encrypted );
        break;

      case 'update':
        // View
        $sView = 'admin';

        // Arguments
        if( !( isset( $_POST['file'] ) and is_scalar( $_POST['file'] ) ) )
        {
          trigger_error( '['.__METHOD__.'][action:update] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }
        $asOptions = ( isset( $_POST['option'] ) and is_array( $_POST['option'] ) ) ? $_POST['option'] : array();

        // File handle
        $sFileHandle_encrypted = $_POST['file'];
        $amFileHandle = $this->decryptFileHandle( $sFileHandle_encrypted );
        $iFileHandle = $this->validateFileHandle( $amFileHandle, true );

        // Update settings
        try
        {
          // Check options
          $bOptionPublic = (boolean)( $this->amCONFIG['option_public_allow'] ? in_array( 'public', $asOptions ) : $this->amCONFIG['option_public_default'] );
          $bOptionUnique = (boolean)( $this->amCONFIG['option_unique_allow'] ? in_array( 'unique', $asOptions ) : $this->amCONFIG['option_unique_default'] );
          $bOptionMultiple = (boolean)( $this->amCONFIG['option_multiple_allow'] ? in_array( 'multiple', $asOptions ) : $this->amCONFIG['option_multiple_default'] );
          if( $bOptionPublic or $bOptionMultiple ) $bOptionUnique = false;

          // Open database
          $this->databaseOpen();

          // Retrieve file data (for action decision)
          $amFileData = $this->databaseQueryFileData( $iFileHandle );

          // Update options
          $this->databaseExecuteFileUpdate( $iFileHandle, $bOptionPublic, $bOptionUnique, $bOptionMultiple );

          // Handle public file
          if( $bOptionPublic and !$amFileData['option_public'] )
          {
            // Send download link
            $this->mailDownloadLink( $sAuthenticatedUser, $amFileData, $this->getLocaleText( 'message:public' ), true );

            // Notifification
            $this->notifyEventAccessAuthorize( $iFileHandle, $amFileData['file_name'], $sAuthenticatedUser, 'PUBLIC' );
          }

          // Send download link

          // Close database
          $this->databaseClose();
        }
        catch( Exception $e )
        {
          $this->databaseClose();
          throw $e;
        }

        // View
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].'?view=admin&file='.$sFileHandle_encrypted );
        break;

      case 'delete':
        // View
        $sView = 'admin';

        // Arguments
        if( !( isset( $_POST['file'] ) and is_scalar( $_POST['file'] ) ) )
        {
          trigger_error( '['.__METHOD__.'][action:delete] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }

        // File handle
        $sFileHandle_encrypted = $_POST['file'];
        $amFileHandle = $this->decryptFileHandle( $sFileHandle_encrypted );
        $iFileHandle = $this->validateFileHandle( $amFileHandle, true );

        // Delete file
        try
        {
          // Open database
          $this->databaseOpen();

          // Retrieve file data (for file path)
          $amFileData = $this->databaseQueryFileData( $iFileHandle );

          // Delete file from filesystem
          $sFileHash = $amFileData['file_hash'];
          $sFilePath = $this->getFilePath( $sFileHash );
          $bFileExists = $this->fileExists( $sFilePath );
          if( $bFileExists ) $this->fileDelete( $sFilePath );

          // Delete file from database
          $bDatabaseDelete = false;
          if( !$bFileExists or 86400*( time() - strtotime( $amFileData['expire_timestamp'] ) ) > $this->amCONFIG['file_delete_delay'] )
          {
            $this->databaseExecuteFileDelete( $iFileHandle );
            $bDatabaseDelete = true;
          }

          // Notifification
          if( $bFileExists ) $this->notifyEventFileDelete( $iFileHandle, $amFileData['file_name'], $sAuthenticatedUser );

          // Close database
          $this->databaseClose();
        }
        catch( Exception $e )
        {
          $this->databaseClose();
          throw $e;
        }

        // View
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].( $bDatabaseDelete ? null : '?view=admin&file='.$sFileHandle_encrypted ) );
        break;

      case 'authorize':
      case 'unblock':
        // View
        $sView = 'admin';

        // Arguments
        if( !( isset( $_POST['file'] ) and is_scalar( $_POST['file'] ) ) )
        {
          trigger_error( '['.__METHOD__.'][action:authorize] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }
        $amFormData['message'] = ( isset( $_POST['message'] ) and is_scalar( $_POST['message'] ) ) ? trim( $_POST['message'] ) : null;
        // ... recipient(s)
        $asRecipients = array();
        if( isset( $_POST['recipient'] ) )
        {
          if( is_scalar( $_POST['recipient'] ) )
          {
            $asRecipients = preg_split( '/[,; ]/', $_POST['recipient'], 0, PREG_SPLIT_NO_EMPTY );
            sort( $asRecipients, SORT_STRING );
            $asRecipients = array_unique( $asRecipients );
            $amFormData['recipient'] = implode( ',', $asRecipients );
          }
          else
          {
            $asRecipients = $_POST['recipient'];
          }
        }

        // File handle
        $sFileHandle_encrypted = $_POST['file'];
        $amFileHandle = $this->decryptFileHandle( $sFileHandle_encrypted );
        $iFileHandle = $this->validateFileHandle( $amFileHandle, true );

        // Authorize recipients
        if( count( $asRecipients ) )
        {
          // Validate
          $asRecipients = array_map( 'strtolower', $asRecipients );
          foreach( $asRecipients as $sRecipient )
          {
            // Check e-mail address
            if( !preg_match( '/^(\w+[-_.])*\w+@(\w+[-_.])*\w+\.\w+$/AD', $sRecipient ) )
            {
              $bSessionBailOut = false;
              throw new Exception( $this->getLocaleText( 'error:invalid_email' )."\n[".$sRecipient.']' );
            }

            // Check relaying
            $sDomain_sender = substr( $sAuthenticatedUser, strpos( $sAuthenticatedUser, '@' )+1 );
            $sDomain_recipient = substr( $sRecipient, strpos( $sRecipient, '@' )+1 );
            if( !preg_match( $this->amCONFIG['user_email_domain_relay'], $sDomain_sender )
                and !preg_match( $this->amCONFIG['user_email_domain_relay'], $sDomain_recipient ) )
            {
              $bSessionBailOut = false;
              trigger_error( '['.__METHOD__.'][action:authorize] Domain relaying violation; '.$sAuthenticatedUser.'@'.$sRemoteIP.', '.$sRecipient, E_USER_NOTICE );
              throw new Exception( $this->getLocaleText( 'error:invalid_email_relay' )."\n[".$sRecipient.']' );
            }
            
          }

          // Authorize
          try
          {
            // Open database
            $this->databaseOpen();

            // Retrieve file data (for download link e-mail)
            $amFileData = $this->databaseQueryFileData( $iFileHandle );

            // Authorize each user
            foreach( $asRecipients as $sRecipient )
            {
              // Save in database
              $this->databaseExecuteAccessAuthorize( $iFileHandle, $sRecipient, time()+86400*$this->amCONFIG['file_expire_delay'] );

              // Send download link
              if( $sAction == 'authorize' ) $this->mailDownloadLink( $sRecipient, $amFileData, $amFormData['message'] );

              // Notifification
              $this->notifyEventAccessAuthorize( $iFileHandle, $amFileData['file_name'], $sAuthenticatedUser, $sRecipient );
            }

            // Close database
            $this->databaseClose();
          }
          catch( Exception $e )
          {
            $this->databaseClose();
            throw $e;
          }

        }

        // View
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].'?view=admin&file='.$sFileHandle_encrypted );
        break;

      case 'block':
        // View
        $sView = 'admin';

        // Arguments
        if( !( isset( $_POST['file'] ) and is_scalar( $_POST['file'] ) ) )
        {
          trigger_error( '['.__METHOD__.'][action:block] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }
        $asRecipients = ( isset( $_POST['recipient'] ) and is_array( $_POST['recipient'] ) ) ? $_POST['recipient'] : null;

        // File handle
        $sFileHandle_encrypted = $_POST['file'];
        $amFileHandle = $this->decryptFileHandle( $sFileHandle_encrypted );
        $iFileHandle = $this->validateFileHandle( $amFileHandle, true );

        // Authorize recipients
        if( count( $asRecipients ) )
        {
          // Validate
          $asRecipients = array_map( 'strtolower', $asRecipients );
          foreach( $asRecipients as $sRecipient )
          {
            // Check e-mail address
            if( !preg_match( '/^(\w+[-_.])*\w+@(\w+[-_.])*\w+\.\w+$/AD', $sRecipient ) )
            {
              $bSessionBailOut = false;
              throw new Exception( $this->getLocaleText( 'error:invalid_email' )."\n[".$sRecipient.']' );
            }
          }

          // Block
          try
          {
            // Open database
            $this->databaseOpen();

            // Retrieve file data
            $amFileData = $this->databaseQueryFileData( $iFileHandle );

            // Block each user
            foreach( $asRecipients as $sRecipient )
            {
              // Save in database
              $this->databaseExecuteAccessBlock( $iFileHandle, $sRecipient );

              // Notifification
              $this->notifyEventAccessBlock( $iFileHandle, $amFileData['file_name'], $sAuthenticatedUser, $sRecipient );
            }

            // Close database
            $this->databaseClose();
          }
          catch( Exception $e )
          {
            $this->databaseClose();
            throw $e;
          }

        }

        // View
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].'?view=admin&file='.$sFileHandle_encrypted );
        break;

      case 'cleanup':
        // View
        $sView = 'list';

        // Check superuser
        if( !in_array( $sAuthenticatedUser, $this->amCONFIG['superusers'] ) )
        {
          trigger_error( '['.__METHOD__.'][action:cleanup] Not authorized; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:not_authorized' ) );
        }

        // Cleanup
        $aiFileHandle = isset( $_POST['handle'] ) ? $_POST['handle'] : null;
        if( !is_null( $aiFileHandle ) and !is_array( $aiFileHandle ) ) $aiFileHandle = array( $aiFileHandle );
        if( count( $aiFileHandle ) )
        {
          try
          {
            $this->databaseOpen();
            foreach( $aiFileHandle as $iFileHandle )
            {
              // Delete file
              $iFileHandle = (integer)$iFileHandle;
              $amFileData = $this->databaseQueryFileData( $iFileHandle );
              $sFileHash = $amFileData['file_hash'];
              $sFilePath = $this->getFilePath( $sFileHash );
              $this->fileDelete( $sFilePath );
              $this->databaseExecuteFileDelete( $iFileHandle );
            }
            $this->databaseClose();
          }
          catch( Exception $e )
          {
            $this->databaseClose();
            throw $e;
          }
        }

        // View
        $this->htmlRedirect( $_SERVER['SCRIPT_NAME'].'?view=list'.( isset( $_POST['filter'] ) ? '&filter='.urlencode( trim( $_POST['filter'] ) ) : null ) );
        break;

      default:
        // View
        if( isset( $_POST['do'] ) )
        {
          trigger_error( '['.__METHOD__.'][action:unknown] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }
        break;
        
      }

    }
    catch( Exception $e )
    {
      // Save the error message
      $sError = $e->getMessage();

      // Session clean-up
      if( $bSessionBailOut ) // Critical error
      {
        // Reset session
        $this->resetSession();

        // View
        $sView = 'error';
      }

    }

    try
    {
      // View
      switch( $sView )
      {

      case 'admin':
        // Arguments
        if( !( isset( $_GET['file'] ) and is_scalar( $_GET['file'] ) )
            and !( isset( $_POST['file'] ) and is_scalar( $_POST['file'] ) )
            )
        {
          trigger_error( '['.__METHOD__.'][view:admin] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
          throw new Exception( $this->getLocaleText( 'error:invalid_data' ) );
        }

        // File handle
        $sFileHandle_encrypted = isset( $_POST['file'] ) ? $_POST['file'] : $_GET['file'];
        $amFileHandle = $this->decryptFileHandle( $sFileHandle_encrypted );
        $iFileHandle = $this->validateFileHandle( $amFileHandle, true );

        // File data
        try
        {
          // Query database
          $this->databaseOpen();
          $amFileData = $this->databaseQueryFileData( $iFileHandle, false );
          if( $amFileData === false )
          {
            trigger_error( '['.__METHOD__.'][view:admin] Failed to retrieve file data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
            throw new Exception( $this->getLocaleText( 'error:file_missing' ) );
          }
          $this->databaseClose();
        }
        catch( Exception $e )
        {
          $this->databaseClose();
          throw $e;
        }

        // Save file data
        $amFormData = array_merge( $amFormData, $amFileData );
        $amFormData['file_handle_encrypted'] = $sFileHandle_encrypted;
        break;

      }

    }
    catch( Exception $e )
    {
      // Save the error message
      $sError = $e->getMessage();

      // Reset session
      $this->resetSession();

      // View
      $sView = 'error';
    }

    // Save form data
    $this->amFORMDATA = array_merge( array( 'VIEW' => $sView, 'ERROR' => $sError ), $amFormData );

    // Done
    return $this->amFORMDATA['VIEW'];
  }

  /** Retrieve data (variable value) from the controller
   *
   * @param string $sID Data (variable) ID
   * @return mixed Data (variable) value
   */
  public function getFormData( $sID )
  {
    return isset( $this->amFORMDATA[ $sID ] ) ? $this->amFORMDATA[ $sID ] : null;
  }

  /** Retrieve the form's HTML code from the controller (for the given view)
   *
   * @param string $sID Form ID
   * @param array|mixed $amFormData Additional form data
   * @return string Form's HTML code
   */
  public function htmlForm( $sID, $amFormData = null )
  {
    static $iTabIndex;

    // Build form
    $sHTML = '';
    if( is_null( $iTabIndex ) ) $iTabIndex = 1;
    switch( $sID )
    {

    case 'locale':
      $sLocaleCurrent = $this->getLocaleCurrent();
      $sHTML .= '<FORM METHOD="post" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
      if( isset( $this->amFORMDATA['VIEW'] ) )
        $sHTML .= '<INPUT TYPE="hidden" NAME="view" VALUE="'.$this->amFORMDATA['VIEW'].'" />';
      if( isset( $this->amFORMDATA['file_handle_encrypted'] ) )
        $sHTML .= '<INPUT TYPE="hidden" NAME="file" VALUE="'.$this->amFORMDATA['file_handle_encrypted'].'" />';
      $sHTML .= '<TABLE CLASS="detail" CELLSPACING="0">';
      // Field: locale (select)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:language' ) ).':</TD>';
      $sHTML .= '<TD CLASS="input"><SELECT NAME="locale" ONCHANGE="javascript:submit();" STYLE="WIDTH:50px;">';
      foreach( $this->getLocaleSupported() as $sLocale )
      {
        $sHTML .= '<OPTION VALUE="'.$sLocale.'"'.( $sLocale == $sLocaleCurrent ? ' SELECTED' : null ).'>'.$sLocale.'</OPTION>';
      }
      $sHTML .= '</SELECT></TD>';
      if( !empty( $this->amCONFIG['timezone'] ) )
      {
        $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:timezone' ) ).':</TD>';
        $sHTML .= '<TD CLASS="data">'.htmlentities( $this->amCONFIG['timezone'] ).'</TD>';
      }
      $sHTML .= '</TR>';
      $sHTML .= '</TABLE>';
      $sHTML .= '</FORM>';
      break;

    case 'upload':
      $sHTML .= '<FORM METHOD="post" ENCTYPE="multipart/form-data" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
      $sHTML .= '<TABLE CLASS="detail" CELLSPACING="0">';
      // Field: upload (file; required)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:file' ) ).':</TD>';
      $sHTML .= '<TD CLASS="input"><SPAN CLASS="required"><INPUT TYPE="file" TABINDEX="'.($iTabIndex++).'" NAME="upload" /></SPAN></TD>';
      $sHTML .= '<TD CLASS="note"><SPAN CLASS="required">*</SPAN></TD>';
      $sHTML .= '</TR>';
      // Field: md5 (text)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:file_md5' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:file_md5' ) ).':</TD>';
      $sHTML .= '<TD CLASS="input"><INPUT TYPE="text" TABINDEX="'.($iTabIndex++).'" NAME="md5" MAXLENGTH="32" /></TD>';
      $sHTML .= '<TD CLASS="note">&nbsp;</TD>';
      $sHTML .= '</TR>';
      // Field: size (static)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:file_max_size' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:file_max_size' ) ).':</TD>';
      $sHTML .= '<TD CLASS="data">'.htmlentities( $this->formatBytes($this->amCONFIG['file_max_size'],false).' / '.$this->formatBytes($this->amCONFIG['file_max_size'],true) ).'</TD>';
      $sHTML .= '<TD CLASS="note">&nbsp;</TD>';
      $sHTML .= '</TR>';
      // Field: upload (button)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="button" COLSPAN="3"><BUTTON TYPE="submit" TABINDEX="'.($iTabIndex++).'">'.htmlentities( $this->getLocaleText( 'button:upload' ) ).'</BUTTON></TD>';
      $sHTML .= '</TR>';
      $sHTML .= '</TABLE>';
      $sHTML .= '</FORM>';
      break;

    case 'file':
      // Check view
      $sView = isset( $this->amFORMDATA['VIEW'] ) ? $this->amFORMDATA['VIEW'] : null;
      if( $sView != 'admin' )
      {
        trigger_error( '['.__METHOD__.'][file] Form not allowed in current view; view='.$sView, E_USER_WARNING );
        throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
      }

      // Arguments
      $sAuthenticatedUser = $this->getAuthenticatedUser();
      $sFilePath = $this->getFilePath( $this->amFORMDATA['file_hash'] );
      $bFileExists = $this->fileExists( $sFilePath );

      // Form
      $sHTML .= '<FORM METHOD="post" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
      $sHTML .= '<INPUT TYPE="hidden" NAME="file" VALUE="'.$this->amFORMDATA['file_handle_encrypted'].'" />';
      $sHTML .= '<INPUT TYPE="hidden" NAME="do" VALUE="tobedefined" />';
      $sHTML .= '<INPUT TYPE="hidden" NAME="confirm_delete" VALUE="'.htmlentities( $this->getLocaleText( 'confirm:delete' ) ).'" DISABLED />';
      $sHTML .= '<TABLE CLASS="detail" CELLSPACING="0">';
      // Field: file + upload_user (static)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:file' ) ).':</TD>';
      if( $bFileExists )
      {
        $sHTML .= '<TD CLASS="data"><A HREF="'.$this->amCONFIG['home_url'].'?download='.$this->amFORMDATA['file_handle_encrypted'].'">'.htmlentities( $this->amFORMDATA['file_name'] ).'</A></TD>';
      }
      else
      {
        $sHTML .= '<TD CLASS="data">'.htmlentities( $this->amFORMDATA['file_name'] ).'</TD>';
      }
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:upload_user' ) ).':</TD>';
      $sHTML .= '<TD CLASS="data">'.htmlentities( $this->amFORMDATA['upload_user'] ).'</TD>';
      $sHTML .= '</TR>';
      // Field: md5 + upload_timestamp (static)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:file_md5' ) ).':</TD>';
      $sHTML .= '<TD CLASS="data">'.htmlentities( $this->amFORMDATA['file_md5'] ).'</TD>';
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:upload_timestamp' ) ).':</TD>';
      $sHTML .= '<TD CLASS="data">'.htmlentities( $this->amFORMDATA['upload_timestamp'] ).'</TD>';
      $sHTML .= '</TR>';
      // Field: size + expire_timestamp (static)
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label">'.htmlentities( $this->getLocaleText( 'label:file_size' ) ).':</TD>';
      $sHTML .= '<TD CLASS="data">'.htmlentities( $this->formatBytes($this->amFORMDATA['file_size'],false).' / '.$this->formatBytes($this->amFORMDATA['file_size'],true) ).'</TD>';
      $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:expire_timestamp' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:expire_timestamp' ) ).':</TD>';
      $sHTML .= '<TD CLASS="data"><SPAN STYLE="COLOR:'.( strtotime( $this->amFORMDATA['expire_timestamp'] ) > time() ? '#00A000' : '#A00000' ).';">'.htmlentities( $this->amFORMDATA['expire_timestamp'] ).'</SPAN></TD>';
      $sHTML .= '</TR>';
      // Fields: options
      if( $bFileExists )
      {
        // Field: public (checkbox)
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:option_public' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:option_public' ) ).':</TD>';
        if( $this->amCONFIG['option_public_allow'] )
        {
          $sHTML .= '<TD CLASS="input" COLSPAN="3"><INPUT TYPE="checkbox" TABINDEX="'.($iTabIndex++).'" NAME="option[]" VALUE="public"'.($this->amFORMDATA['option_public']?' CHECKED="1"':null).'/></TD>';
        }
        else
        {
          $sHTML .= '<TD CLASS="data" COLSPAN="3">'.($this->amFORMDATA['option_public']?'x':'-').'</TD>';
        }
        $sHTML .= '</TR>';
        // Field: unique (checkbox)
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:option_unique' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:option_unique' ) ).':</TD>';
        if( $this->amCONFIG['option_unique_allow'] )
        {
          $sHTML .= '<TD CLASS="input" COLSPAN="3"><INPUT TYPE="checkbox" TABINDEX="'.($iTabIndex++).'" NAME="option[]" VALUE="unique"'.($this->amFORMDATA['option_unique']?' CHECKED="1"':null).'/></TD>';
        }
        else
        {
          $sHTML .= '<TD CLASS="data" COLSPAN="3">'.($this->amFORMDATA['option_unique']?'x':'-').'</TD>';
        }
        $sHTML .= '</TR>';
        // Field: multiple (checkbox)
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:option_multiple' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:option_multiple' ) ).':</TD>';
        if( $this->amCONFIG['option_multiple_allow'] )
        {
          $sHTML .= '<TD CLASS="input" COLSPAN="3"><INPUT TYPE="checkbox" TABINDEX="'.($iTabIndex++).'" NAME="option[]" VALUE="multiple"'.($this->amFORMDATA['option_multiple']?' CHECKED="1"':null).'/></TD>';
        }
        else
        {
          $sHTML .= '<TD CLASS="data" COLSPAN="3">'.($this->amFORMDATA['option_multiple']?'x':'-').'</TD>';
        }
        $sHTML .= '</TR>';
      }
      // Field: buttons
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="button" COLSPAN="100">';
      if( $bFileExists and ( $this->amCONFIG['option_public_allow'] or $this->amCONFIG['option_unique_allow'] or $this->amCONFIG['option_multiple_allow'] ) )
      {
        $sHTML .= '<BUTTON TYPE="button" TABINDEX="'.($iTabIndex++).'" ONCLICK="javascript:this.form.do.value=\'update\'; this.form.submit();">'.htmlentities( $this->getLocaleText( 'button:update' ) ).'</BUTTON>';
      }
      $sHTML .= '<BUTTON TYPE="button" TABINDEX="'.($iTabIndex++).'" ONCLICK="javascript:if( window.confirm( this.form.confirm_delete.value ) ) { this.form.do.value=\'delete\'; this.form.submit(); }">'.htmlentities( $this->getLocaleText( 'button:delete' ) ).'</BUTTON>';
      $sHTML .= '</TD>';
      $sHTML .= '</TR>';
      $sHTML .= '</TABLE>';
      $sHTML .= '</FORM>';
      break;

    case 'access':
      // Check view
      $sView = isset( $this->amFORMDATA['VIEW'] ) ? $this->amFORMDATA['VIEW'] : null;
      if( $sView != 'admin' )
      {
        trigger_error( '['.__METHOD__.'][access] Form not allowed in current view; view='.$sView, E_USER_WARNING );
        throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
      }

      // Arguments
      $sAuthenticatedUser = $this->getAuthenticatedUser();
      $sFilePath = $this->getFilePath( $this->amFORMDATA['file_hash'] );
      $bFileExists = $this->fileExists( $sFilePath );

      // Form: new recipient
      if( $bFileExists )
      {
        $sHTML .= '<FORM METHOD="post" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
        $sHTML .= '<INPUT TYPE="hidden" NAME="file" VALUE="'.$this->amFORMDATA['file_handle_encrypted'].'" />';
        $sHTML .= '<INPUT TYPE="hidden" NAME="do" VALUE="authorize" />';
        $sHTML .= '<TABLE CLASS="detail" CELLSPACING="0">';
        // Field: authorize + button (text)
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:download_user' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:download_user' ) ).':</TD>';
        $sHTML .= '<TD CLASS="input"><INPUT TYPE="text" TABINDEX="'.($iTabIndex++).'" NAME="recipient" VALUE="'.htmlentities( $this->amFORMDATA['recipient'] ).'" MAXLENGTH="96" /></TD>';
        $sHTML .= '<TD CLASS="button"><BUTTON TYPE="submit" TABINDEX="'.($iTabIndex+1).'">'.htmlentities( $this->getLocaleText( 'button:add' ) ).'</BUTTON></TD>';
        $sHTML .= '</TR>';
        // Field: authorize + button (text)
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:message' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:message' ) ).':</TD>';
        $sHTML .= '<TD CLASS="input"><TEXTAREA TABINDEX="'.($iTabIndex++).'" NAME="message">'.htmlentities( $this->amFORMDATA['message'] ).'</TEXTAREA></TD>'; $iTabIndex++;
        $sHTML .= '<TD CLASS="button">&nbsp;</BUTTON></TD>';
        $sHTML .= '</TR>';
        $sHTML .= '</TABLE>';
        $sHTML .= '</FORM>';
      }

      // Form: access list
      $iRowCount = 0;
      $sHTML .= '<FORM METHOD="post" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
      $sHTML .= '<INPUT TYPE="hidden" NAME="file" VALUE="'.$this->amFORMDATA['file_handle_encrypted'].'" />';
      $sHTML .= '<INPUT TYPE="hidden" NAME="do" VALUE="tobedefined" />';
      $sHTML .= '<TABLE CLASS="list" CELLSPACING="0">';
      // Row: headers
      $sHTML .= '<TR>';
      $sHTML .= '<TH CLASS="label"'.( $bFileExists ? ' COLSPAN="2"' : null ).'>'.htmlentities( $this->getLocaleText( 'label:download_user' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:download_start' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:download_complete' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:download_count' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:download_block' ) ).'</TH>';
      $sHTML .= '</TR>';
      try
      {
        $this->databaseOpen();
        $oResultset = $this->databaseQueryAccessData( $this->amFORMDATA['file_handle'] );
        while( is_array( $amAccessData = $oResultset->fetch( PDO::FETCH_ASSOC ) ) )
        {
          $iRowCount++;

          // Row: file details (static)
          $sHTML .= '<TR>';
          if( $bFileExists )
          {
            $sHTML .= '<TD CLASS="input"><INPUT TYPE="checkbox" TABINDEX="'.($iTabIndex++).'" NAME="recipient[]" VALUE="'.htmlentities( $amAccessData['download_user'] ).'" /></TD>';
          }
          $sHTML .= '<TD CLASS="data"><A HREF="mailto:'.htmlentities( $amAccessData['download_user'] ).'">'.htmlentities( $amAccessData['download_user'] ).'</A></TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amAccessData['download_start'] ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amAccessData['download_complete'] ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amAccessData['download_count'] ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.( $amAccessData['download_block'] ? 'x' : '-' ).'</TD>';
          $sHTML .= '</TR>';
        }
        $oResultset = null;
        $this->databaseClose();
      }
      catch( Exception $e )
      {
        $this->databaseClose();
      }
      // Row: button
      if( $bFileExists and $iRowCount )
      {
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="button" COLSPAN="100">';
        $sHTML .= '<BUTTON TYPE="button" TABINDEX="'.($iTabIndex++).'" ONCLICK="javascript:this.form.do.value=\'block\'; this.form.submit();">'.htmlentities( $this->getLocaleText( 'button:block' ) ).'</BUTTON>';
        $sHTML .= '<BUTTON TYPE="button" TABINDEX="'.($iTabIndex++).'" ONCLICK="javascript:this.form.do.value=\'unblock\'; this.form.submit();">'.htmlentities( $this->getLocaleText( 'button:unblock' ) ).'</BUTTON>';
        $sHTML .= '</TD>';
        $sHTML .= '</TR>';
      }
      $sHTML .= '</TABLE>';
      $sHTML .= '</FORM>';
      break;

    case 'list':
      // Check view
      $sView = isset( $this->amFORMDATA['VIEW'] ) ? $this->amFORMDATA['VIEW'] : null;
      if( $sView != 'list' )
      {
        trigger_error( '['.__METHOD__.'][list] Form not allowed in current view; view='.$sView, E_USER_WARNING );
        throw new Exception( $this->getLocaleText( 'error:internal_error' )."\n[".date('c').']' );
      }

      // Arguments
      $sAuthenticatedUser = $this->getAuthenticatedUser();
      $bSuperUser = in_array( $sAuthenticatedUser, $this->amCONFIG['superusers'] );
      $sFilter = isset( $_GET['filter'] ) ? trim( $_GET['filter'] ) : null;

      // Form: filter
      $sHTML .= '<FORM METHOD="get" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
      $sHTML .= '<INPUT TYPE="hidden" NAME="view" VALUE="list" />';
      $sHTML .= '<TABLE CLASS="detail" CELLSPACING="0">';
      // Field: filter
      $sHTML .= '<TR>';
      $sHTML .= '<TD CLASS="label" TITLE="'.htmlentities( $this->getLocaleText( 'tooltip:filter' ) ).'" STYLE="CURSOR:help;">'.htmlentities( $this->getLocaleText( 'label:filter' ) ).':</TD>';
      $sHTML .= '<TD CLASS="input"><INPUT TYPE="text" TABINDEX="'.($iTabIndex++).'" NAME="filter" VALUE="'.htmlentities( $sFilter ).'" /></TD>';
      // Field: button
      $sHTML .= '<TD CLASS="button" COLSPAN="3"><BUTTON TYPE="submit" TABINDEX="'.($iTabIndex++).'">'.htmlentities( $this->getLocaleText( 'button:filter' ) ).'</BUTTON></TD>';
      $sHTML .= '</TR>';
      $sHTML .= '</TABLE>';
      $sHTML .= '</FORM>';

      // Form: files list
      $iRowCount = 0;
      $sHTML .= '<FORM METHOD="post" ACTION="'.$_SERVER['SCRIPT_NAME'].'">';
      $sHTML .= '<INPUT TYPE="hidden" NAME="filter" VALUE="'.htmlentities( $sFilter ).'" />';
      if( $bSuperUser )
      {
        $sHTML .= '<INPUT TYPE="hidden" NAME="do" VALUE="cleanup" />';
        $sHTML .= '<INPUT TYPE="hidden" NAME="confirm_delete" VALUE="'.htmlentities( $this->getLocaleText( 'confirm:delete' ) ).'" DISABLED />';
      }
      $sHTML .= '<TABLE CLASS="list" CELLSPACING="0">';
      // Row: headers
      $sHTML .= '<TR>';
      $sHTML .= '<TH CLASS="label"'.( $bSuperUser ? ' COLSPAN="2"' : null ).'>'.htmlentities( $this->getLocaleText( 'label:file' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:file_size' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:upload_user' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:upload_timestamp' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:expire_timestamp' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:option_public' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:option_unique' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:option_multiple' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:download_usercount' ) ).'</TH>';
      $sHTML .= '<TH CLASS="label">'.htmlentities( $this->getLocaleText( 'label:download_count' ) ).'</TH>';
      $sHTML .= '</TR>';
      try
      {
        $this->databaseOpen();
        $oResultset = $this->databaseQueryFileData( $sFilter );
        while( is_array( $amFileData = $oResultset->fetch( PDO::FETCH_ASSOC ) ) )
        {
          $iRowCount++;
          $sFileHandle_encrypted = $this->encryptFileHandle( $amFileData['file_handle'], $sAuthenticatedUser, true );

          // Row: file details (static)
          $sHTML .= '<TR>';
          if( $bSuperUser )
          {
            $sHTML .= '<TD CLASS="input"><INPUT TYPE="checkbox" TABINDEX="'.($iTabIndex++).'" NAME="handle[]" VALUE="'.htmlentities( $amFileData['file_handle'] ).'" /></TD>';
          }
          $sHTML .= '<TD CLASS="data"><A HREF="'.$_SERVER['SCRIPT_NAME'].'?view=admin&file='.$sFileHandle_encrypted.'">'.htmlentities( $amFileData['file_name'] ).'</A></TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $this->formatBytes($amFileData['file_size'],false).' / '.$this->formatBytes($amFileData['file_size'],true) ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amFileData['upload_user'] ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amFileData['upload_timestamp'] ).'</TD>';
          $sHTML .= '<TD CLASS="data"><SPAN STYLE="COLOR:'.( strtotime( $amFileData['expire_timestamp'] ) > time() ? '#00A000' : '#A00000' ).';">'.htmlentities( $amFileData['expire_timestamp'] ).'</SPAN></TD>';
          $sHTML .= '<TD CLASS="data">'.( $amFileData['option_public'] ? 'x' : '-' ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.( $amFileData['option_unique'] ? 'x' : '-' ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.( $amFileData['option_multiple'] ? 'x' : '-' ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amFileData['download_usercount'] ).'</TD>';
          $sHTML .= '<TD CLASS="data">'.htmlentities( $amFileData['download_count'] ).'</TD>';
          $sHTML .= '</TR>';
        }
        $oResultset = null;
        $this->databaseClose();
      }
      catch( Exception $e )
      {
        $this->databaseClose();
      }
      // Row: button
      if( $bSuperUser and $iRowCount )
      {
        $sHTML .= '<TR>';
        $sHTML .= '<TD CLASS="button" COLSPAN="100"><BUTTON TYPE="button" TABINDEX="'.($iTabIndex++).'" ONCLICK="javascript:if( window.confirm( this.form.confirm_delete.value ) ) this.form.submit();">'.htmlentities( $this->getLocaleText( 'button:delete' ) ).'</BUTTON></TD>';
        $sHTML .= '</TR>';
      }
      $sHTML .= '</TABLE>';
      $sHTML .= '</FORM>';
      break;

    }

    // Done
    return $sHTML;
  }

  /** File download controller
   */
  public function httpDownload()
  {
    ini_set( 'display_errors', 0 );
    ini_set( 'output_buffering', 0 );
    ignore_user_abort( true );
    $pFile = null;
    try
    {
      // Check encryption
      if( $this->amCONFIG['force_ssl'] and !isset( $_SERVER['HTTPS'] ) )
      {
        trigger_error( '['.__METHOD__.'] Unsecure channel', E_USER_WARNING );
        throw new Exception( 'HTTP/1.1 403 Forbidden' );
      }

      // Check authentication
      if( !isset( $_SERVER['PHP_AUTH_USER'] ) )
      {
        trigger_error( '['.__METHOD__.'] Unauthenticated channel', E_USER_WARNING );
        throw new Exception( 'HTTP/1.1 401 Unauthorized' );
      }

      // Get user
      try
      {
        $sAuthenticatedUser = $this->getAuthenticatedUser();
      }
      catch( Exception $e )
      {
        trigger_error( '['.__METHOD__.'] Invalid credentials', E_USER_WARNING );
        throw new Exception( 'HTTP/1.1 403 Forbidden' );
      }
      $sRemoteIP = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';

      // Arguments
      if( !isset( $_GET['download'] ) or !is_scalar( $_GET['download'] ) )
      {
        trigger_error( '['.__METHOD__.'] Invalid form data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_WARNING );
        throw new Exception( 'HTTP/1.1 500 Internal Server Error' );
      }

      // File handle
      $sFileHandle_encrypted = $_GET['download'];
      $amFileHandle = $this->decryptFileHandle( $sFileHandle_encrypted );
      $iFileHandle = $this->validateFileHandle( $amFileHandle, false, true ); // Assume the file is public; we'll check again if not

      // Open database
      $this->databaseOpen();

      // Retrieve file data
      $amFileData = $this->databaseQueryFileData( $iFileHandle, false );
      if( $amFileData === false )
      {
        trigger_error( '['.__METHOD__.'] Failed to retrieve file data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
        throw new Exception( 'HTTP/1.1 404 Not Found' );
      }

      // Authorize
      if( !$amFileData['option_public'] )
      {
        try
        {
          $this->validateFileHandle( $amFileHandle, false, false ); // File is not public; check the file handle again
        }
        catch( Exception $e )
        {
          throw new Exception( 'HTTP/1.1 403 Forbidden' );
        }
      }
      $sFilePath = $this->getFilePath( $amFileData['file_hash'] );
      if( !$this->fileExists( $sFilePath ) )
      {
        trigger_error( '['.__METHOD__.'] File does not exist; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
        throw new Exception( 'HTTP/1.1 404 Not Found' );
      }

      // Retrieve access data
      $amAccessData = $this->databaseQueryAccessData( $iFileHandle, $sAuthenticatedUser, false );
      if( $amAccessData === false )
      {
        if( !$amFileHandle['admin'] and !$amFileData['option_public'] )
        {
          trigger_error( '['.__METHOD__.'] Failed to retrieve access data; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
          throw new Exception( 'HTTP/1.1 403 Forbidden' );
        }
        else
        {
          // Add access permission/history for file administrator or public file
          $this->databaseExecuteAccessAuthorize( $iFileHandle, $sAuthenticatedUser, strtotime( $amFileData['expire_timestamp'] ) );
          $amAccessData = $this->databaseQueryAccessData( $iFileHandle, $sAuthenticatedUser );
        }
      }
      $iDownloadStart = strtotime( $amAccessData['download_start'] ); if( $iDownloadStart < 0 ) $iDownloadStart = 0;
      $iDownloadProgress = strtotime( $amAccessData['download_progress'] ); if( $iDownloadProgress < 0 ) $iDownloadProgress = 0;
      $iDownloadComplete = strtotime( $amAccessData['download_complete'] ); if( $iDownloadComplete < 0 ) $iDownloadComplete = 0;
      $iNow = time();

      // Authorize
      if( !$amFileHandle['admin']  // download is not an admin download
          and $amAccessData['download_block']  // download is blocked
          and ( $iNow - $iDownloadComplete > $this->amCONFIG['file_grace_delay']  // download is completed (more than grace delay ago)
                or $sRemoteIP != $amAccessData['download_ip'] )  // download is initiated from a different IP
          )
      {
        trigger_error( '['.__METHOD__.'] Access blocked; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
        throw new Exception( 'HTTP/1.1 403 Forbidden' );
      }

      // Download range
      // REF: http://tools.ietf.org/id/draft-ietf-http-range-retrieval-00.txt
      $iRangeLength = $amFileData['file_size'];
      $iRangeFirst = 0;
      $iRangeLast = $iRangeLength - 1;
      $bRangePartial = false;
      if( isset( $_SERVER['HTTP_RANGE'] ) )
      {
        $bRangePartial = true;
        if( !preg_match( '/^bytes=\d*-\d*(,\d*-\d*)*$/AD', $_SERVER['HTTP_RANGE'] ) )
        {
          trigger_error( '['.__METHOD__.'] Invalid download range; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
          header( 'Content-Range: bytes 0-'.($iRangeLength-1).'/'.$iRangeLength );
          throw new Exception( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
        }
        // NOTE: We handle only a single range, by aggregating multiple ranges.
        //       This complies with the RFC and simplifies encoding and download throttling.
        $iRangeFirst = $iRangeLength;
        $iRangeLast = 0;
        foreach( explode( ',', substr( $_SERVER['HTTP_RANGE'], 6 ) ) as $sRange )
        {
          list( $iRangeFirst_tmp, $iRangeLast_tmp ) = explode( '-', $sRange );
          if( empty( $iRangeFirst_tmp ) ) $iRangeFirst_tmp = 0;
          if( empty( $iRangeLast_tmp ) ) $iRangeLast_tmp = $iRangeLength - 1;
          $iRangeFirst = min( $iRangeFirst, (integer)$iRangeFirst_tmp );
          $iRangeLast = max( $iRangeLast, (integer)$iRangeLast_tmp );
        }
        if( $iRangeFirst > $iRangeLast )
        {
          trigger_error( '['.__METHOD__.'] Invalid download range; '.$sAuthenticatedUser.'@'.$sRemoteIP , E_USER_NOTICE );
          header( 'Content-Range: bytes 0-'.($iRangeLength-1).'/'.$iRangeLength );
          throw new Exception( 'HTTP/1.1 416 Requested Range Not Satisfiable' );
        }
      }
      $iContentLength = $iRangeLast - $iRangeFirst + 1;
      if( $iContentLength == $iRangeLength )
      {
        $bRangePartial = false;
      }
      if( $bRangePartial  // download is a resume request
          and ( $iDownloadStart == 0  // download has not been started
                or ( $iDownloadComplete != 0 and $iNow - $iDownloadComplete > $this->amCONFIG['file_grace_delay'] )  // download is completed (more than grace delay ago)
                )
          )
      {
        trigger_error( '['.__METHOD__.'] Invalid download resume attempt; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
        throw new Exception( 'HTTP/1.1 403 Forbidden' );
      }

      // Prevent simultaneous download
      if( $iDownloadStart != 0  // download has already started
          and $iDownloadComplete == 0  // download is not completed
          and !( $bRangePartial and $sRemoteIP == $amAccessData['download_ip'] )  // download is not a valid resume request
          and $iNow - $iDownloadProgress < $this->amCONFIG['file_grace_delay']  // download grace delay is not yet reached
        )
      {
        trigger_error( '['.__METHOD__.'] Simultaneous download attempt; '.$sAuthenticatedUser.'@'.$sRemoteIP , E_USER_NOTICE );
        throw new Exception( 'HTTP/1.1 409 Conflict' );
      }

      // Open file
      $sFilePath_full = $this->amCONFIG['dir_files'].$sFilePath;
      $pFile = fopen( $sFilePath_full, 'r' );
      if( $pFile === false )
      {
        trigger_error( '['.__METHOD__.'] Failed to open file; '.$sFilePath_full, E_USER_WARNING );
        throw new Exception( 'HTTP/1.1 500 Internal Server Error' );
      }
      if( fseek( $pFile, $iRangeFirst, SEEK_SET ) < 0 )
      {
        trigger_error( '['.__METHOD__.'] Failed to seek file; '.$sFilePath_full.', '.$iRangeFirst, E_USER_WARNING );
        throw new Exception( 'HTTP/1.1 500 Internal Server Error' );
      }

      // Save access start/progress status
      if( $iDownloadStart == 0 or !$bRangePartial or $sRemoteIP != $amAccessData['download_ip'] )
      {
        $iDownloadStart = $iNow;
        $this->databaseExecuteAccessStart( $iFileHandle, $sAuthenticatedUser, $iDownloadStart );
        $this->notifyEventAccessStart( $iFileHandle, $amFileData['file_name'], $amFileData['upload_user'], $sAuthenticatedUser, $iDownloadStart );
      }
      else
      {
        $this->databaseExecuteAccessProgress( $iFileHandle, $sAuthenticatedUser, $iNow, $iProgressBytes );
        $this->notifyEventAccessProgress( $iFileHandle, $amFileData['file_name'], $amFileData['upload_user'], $sAuthenticatedUser, $iDownloadStart, $iNow, $iProgressBytes );
      }

      // Dump file
      header( $bRangePartial ? 'HTTP/1.1 206 Partial Content' : 'HTTP/1.1 200 OK' );
      header( 'Cache-Control: max-age='.$this->amCONFIG['file_grace_delay'] );
      header( 'Accept-Range: bytes' );
      header( 'Content-Type: application/octet-stream' );
      header( 'Content-Disposition: attachment; filename="'.$amFileData['file_name'].'"' );
      header( 'Content-Transfer-Encoding: binary' );
      header( 'Content-Range: bytes '.$iRangeFirst.'-'.$iRangeLast.'/'.$iRangeLength );
      header( 'Content-Length: '.$iContentLength );
      $iProgressBytes = $iRangeFirst;
      $iProgressTime = $iNow;
      $fChunkDelay = $this->amCONFIG['file_chunk_size'] / $this->amCONFIG['file_max_speed'];
      while( $iContentLength > 0 )
      {
        // Save dump start
        $fChunkStart = microtime( true );

        // Dump chunk
        $iReadBytes = min( $iContentLength, $this->amCONFIG['file_chunk_size'] );
        $sBuffer = fread( $pFile, $iReadBytes );
        if( $sBuffer === false )
        {
          trigger_error( '['.__METHOD__.'] Failed to read file; '.$sFilePath_full.', '.$iReadBytes, E_USER_WARNING );
          throw new Exception( 'HTTP/1.1 500 Internal Server Error' );
        }
        echo $sBuffer;
        unset( $sBuffer );
        $iContentLength -= $iReadBytes;
        $iProgressBytes += $iReadBytes;
        if( $iContentLength <= 0 ) break;

        // Throttling
        $fChunkStop = microtime( true );
        $fChunkSleep = $fChunkDelay - $fChunkStop + $fChunkStart;
        if( $fChunkSleep > 0 ) usleep( 1000000 * $fChunkSleep );

        // Check connection
        if( connection_aborted() )
        {
          trigger_error( '['.__METHOD__.'] Connection interrupted; '.$sAuthenticatedUser.'@'.$sRemoteIP, E_USER_NOTICE );
          throw new Exception();
        }

        // Save access progress status
        // NOTE: Let's log progress no more than once per second
        if( $fChunkStop - $iProgressTime > $this->amCONFIG['log_event_progress_delay'] )
        {
          $iNow = time();
          $this->databaseExecuteAccessProgress( $iFileHandle, $sAuthenticatedUser, $iNow, $iProgressBytes );
          $this->notifyEventAccessProgress( $iFileHandle, $amFileData['file_name'], $amFileData['upload_user'], $sAuthenticatedUser, $iDownloadStart, $iNow, $iProgressBytes );
          $iProgressTime = (integer)$fChunkStart;
        }
      }

      // Close file
      if( fclose( $pFile ) === false )
      {
        $pFile = null;
        trigger_error( '['.__METHOD__.'] Failed to close file; '.$sFilePath_full, E_USER_WARNING );
        throw new Exception();
      }
      $pFile = null;

      // Save access completion status
      $this->databaseExecuteAccessComplete( $iFileHandle, $sAuthenticatedUser, $iNow );
      $this->notifyEventAccessComplete( $iFileHandle, $amFileData['file_name'], $amFileData['upload_user'], $sAuthenticatedUser, $iDownloadStart, $iNow, $amFileData['file_size'] );

      // Block access
      if( !$amFileHandle['admin'] and !$amFileData['option_multiple'] )
        $this->databaseExecuteAccessBlock( $iFileHandle, $sAuthenticatedUser );

      // Delete file
      if( !$amFileHandle['admin'] and $amFileData['option_unique'] )
        $this->fileDelete( $sFilePath );
      
      // Close database
      $this->databaseClose();
    }
    catch( Exception $e )
    {
      if( !is_null( $pFile ) ) fclose( $pFile );
      $this->databaseClose();
      $sMessage = $e->getMessage();
      if( !empty( $sMessage ) )
      {
        @header( substr( $sMessage, 0, 5 ) == 'HTTP/' ? $sMessage : 'HTTP/1.1 500 Internal Server Error' );  // Rewrite messages that are not a HTTP status code
        echo $sMessage;
      }
    }
    exit;
  }

  /** Performs automatic clean-up of expired files
   *
   * <P><B>SYNOPSIS: This functions performs the automatic cleanup of expired files. More precisely:<BR/>
   * - files that have expired will be deleted from the filesystem (but kept in the database)<BR/>
   * - files that have expired more than <SAMP>$_CONFIG['file_delete_delay']</SAMP> will be deleted from the database</P>
   */
  public function doAutoCleanup()
  {
    try
    {
      $iNow = time();
      $this->databaseOpen();
      $oResultset = $this->databaseQueryFileExpired();
      while( is_array( $amFileData = $oResultset->fetch( PDO::FETCH_ASSOC ) ) )
      {
        // Delete file
        $iFileHandle = $amFileData['file_handle'];
        $sFileHash = $amFileData['file_hash'];
        $sFilePath = $this->getFilePath( $sFileHash );
        $iExpireTimestamp = strtotime( $amFileData['expire_timestamp'] );
        if( $this->fileExists( $sFilePath ) ) $this->fileDelete( $sFilePath );
        if( $iNow - $iExpireTimestamp > 86400*$this->amCONFIG['file_delete_delay'] ) $this->databaseExecuteFileDelete( $iFileHandle );
      }
      $this->databaseClose();
    }
    catch( Exception $e )
    {
      $this->databaseClose();
      throw $e;
    }
  }

}
