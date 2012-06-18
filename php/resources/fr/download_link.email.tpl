#{SUBJECT}
[FEXP] Lien de téléchargement (@{file_name})

#{TEXT}
Vous avez reçu le droit d'accès au fichier suivant sur @{title}:

  Fichier:         @{file_name}
  MD5:             @{file_md5}
  Taille:          @{file_size}
  Téléchargé par:  mailto:@{upload_user}
  Téléchargé le:   @{upload_timestamp}
  Expiration:      @{expire_timestamp}
  URL:             @{url}

Message:
@{message}

#{HTML}
<HTML>
<HEAD>
<STYLE TYPE="text/css">
BODY { FONT:normal 12px sans-serif; BACKGROUND:#FFFFFF; COLOR:#000000; }
TABLE.info { FONT:normal 12px sans-serif; }
TABLE.info TD.l { VERTICAL-ALIGN:top; PADDING-RIGHT:5px; FONT-WEIGHT:bold; WHITE-SPACE:nowrap; }
</STYLE>
</HEAD>
<BODY>
<P><B>Vous avez re&ccedil;u le droit d'acc&egrave;s au fichier suivant sur <I>@{title}</I>:</B></P>
<BLOCKQUOTE>
<TABLE CLASS="info" CELLSPACING="0">
<TR><TD CLASS="l">Fichier:</TD><TD CLASS="v">@{file_name}</TD></TR>
<TR><TD CLASS="l">MD5:</TD><TD CLASS="v">@{file_md5}</TD></TR>
<TR><TD CLASS="l">Taille:</TD><TD CLASS="v">@{file_size}</TD></TR>
<TR><TD CLASS="l">T&eacute;l&eacute;charg&eacute; par:</TD><TD CLASS="v"><A HREF="mailto:@{upload_user}">@{upload_user}</A></TD></TR>
<TR><TD CLASS="l">T&eacute;l&eacute;charg&eacute; le:</TD><TD CLASS="v">@{upload_timestamp}</TD></TR>
<TR><TD CLASS="l">Expiration:</TD><TD CLASS="v">@{expire_timestamp}</TD></TR>
<TR><TD CLASS="l">URL:</TD><TD CLASS="v"><A HREF="@{url}">lien de t&eacute;l&eacute;chargement</A></TD></TR>
</TABLE>
</BLOCKQUOTE>
<P><B>Message:</B></P>
<P>@{message}</P>
</BODY>
</HTML>
