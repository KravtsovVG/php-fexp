<?php // INDENTING (emacs/vi): -*- mode:html; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab:
/** PHP File Exchange Platform (PHP-FEXP)
 *
 * @package    FEXP
 * @subpackage Examples
 */

// Check configuration path
if( !isset( $_SERVER['PHP_FEXP_CONFIG'] ) )
{
  trigger_error( 'Missing configuration path. Please set the PHP_FEXP_CONFIG environment variable.', E_USER_ERROR );
}

// Disable error display (to prevent session data or download corruption)
// WARNING: Allowing errors to be displayed is a security risk!
//          Do not display errors on a production site!
ini_set( 'display_errors', 0 );

/** Load and instantiate FEXP resources
 */
require_once 'FEXP.php';
$oFEXP = new FEXP( $_SERVER['PHP_FEXP_CONFIG'] );

// Download (?)
if( isset( $_GET['download'] ) )
{
  $oFEXP->httpDownload();
  exit;
}

// Start session (required)
session_start();

// Controller / View
$oFEXP->htmlControlPage(); // We MUST do this before anything is sent to the browser (cf. HTTP headers)
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<META HTTP-EQUIV="content-type" CONTENT="text/html; charset=ISO-8859-1" />
<TITLE><?php echo htmlentities( $oFEXP->getLocaleText( 'title:file_exchange_platform' ) ); ?></TITLE>
<STYLE TYPE="text/css">
DIV.FEXP { MIN-WIDTH:600px; MAX-WIDTH:1200px; MARGIN:10px auto; FONT:12px sans-serif; BACKGROUND:#FFFFFF; }
DIV.FEXP H1 { MARGIN:15px 0px 5px; FONT:bold 20px sans-serif; TEXT-ALIGN:center; }
DIV.FEXP H2 { MARGIN:15px 0px 5px; FONT:bold 16px sans-serif; }
DIV.FEXP H3 { MARGIN:15px 0px 5px; FONT:bold 14px sans-serif; }
DIV.FEXP A { TEXT-DECORATION:none; COLOR:#0086FF; }
DIV.FEXP A:hover { TEXT-DECORATION:underline; }
DIV.FEXP DIV.error { WIDTH:500px; MARGIN:auto; PADDING:5px 10px; BORDER:solid 2px #A00000; BACKGROUND:#FFE0E0; COLOR:#800000; }
DIV.FEXP DIV.error H2 { MARGIN:5px 0px; BACKGROUND:transparent; COLOR:#800000; TEXT-ALIGN:center; }
DIV.FEXP DIV.error P { MARGIN:0px; BACKGROUND:transparent; COLOR:#800000; TEXT-ALIGN:center; }
DIV.FEXP DIV.form { MARGIN:5px 20px; }
DIV.FEXP DIV.form TABLE.detail { FONT:12px sans-serif; }
DIV.FEXP DIV.form TABLE.list { FONT:12px sans-serif; }
DIV.FEXP DIV.form TH { PADDING:2px 4px; FONT-WEIGHT:bold; TEXT-ALIGN:left; WHITE-SPACE:nowrap; }
DIV.FEXP DIV.form TD { PADDING:2px 4px; WHITE-SPACE:nowrap; }
DIV.FEXP DIV.form TD.label { FONT-WEIGHT:bold; }
DIV.FEXP DIV.form TD.input { PADDING-RIGHT:10px; TEXT-ALIGN:left; }
DIV.FEXP DIV.form TD.data { PADDING-RIGHT:10px; TEXT-ALIGN:left; }
DIV.FEXP DIV.form TD.note { WIDTH:20px; TEXT-ALIGN:center; }
DIV.FEXP DIV.form TD.button { TEXT-ALIGN:left; }
DIV.FEXP DIV.form TABLE.list TH { BORDER-BOTTOM:solid 1px #000000; }
DIV.FEXP DIV.form TABLE.list TD { BORDER-TOP:solid 1px #808080; }
DIV.FEXP DIV.form TABLE.detail TD { VERTICAL-ALIGN:top; }
DIV.FEXP DIV.form TABLE.detail TD.label { PADDING-TOP:6px;PADDING-RIGHT:5px; }
DIV.FEXP DIV.form TABLE.detail TD.data { PADDING-TOP:6px; }
DIV.FEXP DIV.form INPUT { BACKGROUND:#FCFCFC; BORDER:solid 1px #A0A0A0; }
DIV.FEXP DIV.form TEXTAREA { BACKGROUND:#FCFCFC; BORDER:solid 1px #A0A0A0; }
DIV.FEXP DIV.form TABLE.detail INPUT { WIDTH:360px; }
DIV.FEXP DIV.form TABLE.detail TEXTAREA { WIDTH:360px; }
DIV.FEXP DIV.form SPAN.readonly { COLOR:#404040; }
DIV.FEXP DIV.form SPAN.readonly INPUT { BACKGROUND:#DCDCDC; BORDER:solid 1px #A0A0A0; }
DIV.FEXP DIV.form SPAN.required { COLOR:#C00000; }
DIV.FEXP DIV.form SPAN.required INPUT { BACKGROUND:#FFFFF0; BORDER:solid 1px #A08000; }
DIV.FEXP DIV.form SELECT { FONT:bold 12px sans-serif; }
DIV.FEXP DIV.form BUTTON { FONT:bold 12px sans-serif; }
</STYLE>
</HEAD>
<BODY>
<DIV CLASS="FEXP">
<?php
/** Include localized HTML body
 */
require_once $oFEXP->getDirResources().'/'.$oFEXP->getLocaleCurrent().'/html.php';
?>
</DIV>
</BODY>
</HTML>
<?php
// Close session
session_write_close();
