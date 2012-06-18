<!-- INDENTING (emacs/vi): -*- mode:html; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab: -->
<?php
/** PHP File Exchange Platform (PHP-FEXP)
 *
 * @package    FEXP
 * @subpackage Resources_EN
 */
$sView = $oFEXP->getFormData( 'VIEW' );
?>
<H1><?php echo htmlentities( $oFEXP->getLocaleText( 'title:file_exchange_platform' ) ); ?></H1>

<?php $sError = $oFEXP->getFormData( 'ERROR' ); if( strlen( $sError ) > 0 ) { ?>
<DIV CLASS="error">
<H2>Error</H2>
<P STYLE="font-weight:bold;"><?php echo nl2br( htmlentities( $sError ) ); ?></P>
</DIV>
<?php } ?>

<?php if( $sView == 'default' ) { ?>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'locale' ); ?>
</DIV>
<H2>Share A File</H2>
<P>In order to share a file, please <B>upload the required file using the form below</B>. Once the upload completed, you will be able to change sharing options and manage download permissions.</P>
<DIV CLASS="form">
<P>( fields with an <SPAN CLASS="required">*</SPAN> are required )</P>
</DIV>
<H3>File Upload</H3>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'upload' ); ?>
</DIV>
<H3><A HREF="<?php echo $_SERVER['SCRIPT_NAME'].'?view=list'; ?>">Shared Files List</A></H3>

<?php } elseif( $sView == 'admin' ) { ?>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'locale' ); ?>
</DIV>
<H2>Share Management</H2>
<DIV CLASS="form">
</DIV>
<H3>File Details and Sharing Options</H3>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'file' ); ?>
</DIV>
<H3>Recipients and Download History</H3>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'access' ); ?>
</DIV>
<H3><A HREF="<?php echo $_SERVER['SCRIPT_NAME'].'?view=list'; ?>">Shared Files List</A> - <A HREF="<?php echo $_SERVER['SCRIPT_NAME']; ?>">Share Another File</A></H3>

<?php } elseif( $sView == 'list' ) { ?>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'locale' ); ?>
</DIV>
<H2>Uploaded Files Management</H2>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'list' ); ?>
</DIV>
<H3><A HREF="<?php echo $_SERVER['SCRIPT_NAME']; ?>">Share A File</A></H3>

<?php } ?>
