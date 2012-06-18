#{SUBJECT}
[FEXP] Notification de partage de fichier (@{event})

#{TEXT}
L'événement suivant vient d'avoir lieu sur @{title}:

  Événement:          @{event}
  Heure:              @{timestamp}
  Fichier:            @{file_name}
  Téléchargé par:     @{upload_user}
  Destinataire:       @{download_user}
  Taille/Avancement:  @{bytes}
  Adresse IP:         @{ip}

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
<P><B>L'&eacute;v&eacute;nement suivant vient d'avoir lieu sur <I>@{title}</I>:</B></P>
<BLOCKQUOTE>
<TABLE CLASS="info" CELLSPACING="0">
<TR><TD CLASS="l">&Eacute;v&eacute;nement:</TD><TD CLASS="v">@{event}</TD></TR>
<TR><TD CLASS="l">Heure:</TD><TD CLASS="v">@{timestamp}</TD></TR>
<TR><TD CLASS="l">Fichier:</TD><TD CLASS="v">@{file_name}</TD></TR>
<TR><TD CLASS="l">T&eacute;l&eacute;charg&eacute; par:</TD><TD CLASS="v">@{upload_user}</TD></TR>
<TR><TD CLASS="l">Destinataire:</TD><TD CLASS="v">@{download_user}</TD></TR>
<TR><TD CLASS="l">Taille/Avancement:</TD><TD CLASS="v">@{bytes}</TD></TR>
<TR><TD CLASS="l">Adresse IP:</TD><TD CLASS="v">@{ip}</TD></TR>
</TABLE>
</BLOCKQUOTE>
</BODY>
</HTML>
