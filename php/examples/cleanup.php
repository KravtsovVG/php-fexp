<?php // INDENTING (emacs/vi): -*- mode:php; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
/** PHP File Exchange Platform (PHP-FEXP)
 *
 * @package    FEXP
 * @subpackage Examples
 */

// Check configuration path
if( getenv( 'PHP_FEXP_CONFIG' ) === false )
{
  trigger_error( 'Missing configuration path. Please set the PHP_FEXP_CONFIG environment variable.', E_USER_ERROR );
}

/** Load and instantiate FEXP resources
 */
require_once 'FEXP.php';
$oFEXP = new FEXP( getenv( 'PHP_FEXP_CONFIG' ) );

// Perform auto cleanup
$oFEXP->doAutoCleanup();
