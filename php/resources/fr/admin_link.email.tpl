#{SUBJECT}
[FEXP] Lien d'administration (@{file_name})

#{TEXT}
Le fichier suivant est disponible sur @{title}:

  Fichier:         @{file_name}
  MD5:             @{file_md5}
  Taille:          @{file_size}
  Téléchargé par:  @{upload_user}
  Téléchargé le:   @{upload_timestamp}
  Expiration:      @{expire_timestamp}
  URL:             @{url}

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
<P><B>Le fichier suivant est disponible sur <I>@{title}</I>:</B></P>
<BLOCKQUOTE>
<TABLE CLASS="info" CELLSPACING="0">
<TR><TD CLASS="l">Fichier:</TD><TD CLASS="v">@{file_name}</TD></TR>
<TR><TD CLASS="l">MD5:</TD><TD CLASS="v">@{file_md5}</TD></TR>
<TR><TD CLASS="l">Taille:</TD><TD CLASS="v">@{file_size}</TD></TR>
<TR><TD CLASS="l">T&eacute;l&eacute;charg&eacute; par:</TD><TD CLASS="v">@{upload_user}</TD></TR>
<TR><TD CLASS="l">T&eacute;l&eacute;charg&eacute; le:</TD><TD CLASS="v">@{upload_timestamp}</TD></TR>
<TR><TD CLASS="l">Expiration:</TD><TD CLASS="v">@{expire_timestamp}</TD></TR>
<TR><TD CLASS="l">URL:</TD><TD CLASS="v"><A HREF="@{url}">lien d'administration</A></TD></TR>
</TABLE>
</BLOCKQUOTE>
</BODY>
</HTML>
