<!-- INDENTING (emacs/vi): -*- mode:html; tab-width:2; c-basic-offset:2; intent-tabs-mode:nil; -*- ex: set tabstop=2 expandtab: -->
<?php
/** PHP File Exchange Platform (PHP-FEXP)
 *
 * @package    FEXP
 * @subpackage Resources_FR
 */
$sView = $oFEXP->getFormData( 'VIEW' );
?>
<H1><?php echo htmlentities( $oFEXP->getLocaleText( 'title:file_exchange_platform' ) ); ?></H1>

<?php $sError = $oFEXP->getFormData( 'ERROR' ); if( strlen( $sError ) > 0 ) { ?>
<DIV CLASS="error">
<H2>Erreur</H2>
<P STYLE="font-weight:bold;"><?php echo nl2br( htmlentities( $sError ) ); ?></P>
</DIV>
<?php } ?>

<?php if( $sView == 'default' ) { ?>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'locale' ); ?>
</DIV>
<H2>Partager un fichier</H2>
<P>Afin de partager un fichier, <B>t&eacute;l&eacute;chargez-le (<I>upload</I>) s'il-vous-pla&icirc;t en utilisant le formulaire ci-dessous</B>. Une fois le t&eacute;l&eacute;chargement (<I>upload</I>) termin&eacute;, vous pourrez en changez les options de partage et g&eacute;rer les droits de t&acute;l&eacute;chargement (<I>download</I>).</P>
<DIV CLASS="form">
<P>( les champs munis d'un <SPAN CLASS="required">*</SPAN> sont obligatoires )</P>
</DIV>
<H3>T&eacute;l&eacute;chargement (<I>upload</I>)</H3>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'upload' ); ?>
</DIV>
<H3><A HREF="<?php echo $_SERVER['SCRIPT_NAME'].'?view=list'; ?>">Liste des fichiers partag&eacute;s</A></H3>

<?php } elseif( $sView == 'admin' ) { ?>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'locale' ); ?>
</DIV>
<H2>Gestion du partage</H2>
<DIV CLASS="form">
</DIV>
<H3>D&eacute;tails du fichier et options de partage</H3>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'file' ); ?>
</DIV>
<H3>Destinataires et historique de t&eacute;l&eacute;chargement (download)</H3>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'access' ); ?>
</DIV>
<H3><A HREF="<?php echo $_SERVER['SCRIPT_NAME'].'?view=list'; ?>">Liste des fichiers partag&eacute;s</A> - <A HREF="<?php echo $_SERVER['SCRIPT_NAME']; ?>">Partager un autre fichier</A></H3>

<?php } elseif( $sView == 'list' ) { ?>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'locale' ); ?>
</DIV>
<H2>Gestion des fichiers t&eacute;l&eacute;charg&eacute;s</H2>
<DIV CLASS="form">
<?php echo $oFEXP->htmlForm( 'list' ); ?>
</DIV>
<H3><A HREF="<?php echo $_SERVER['SCRIPT_NAME']; ?>">Partager un fichier</A></H3>

<?php } ?>
