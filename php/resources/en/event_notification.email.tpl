#{SUBJECT}
[FEXP] File Exchange Notification (@{event})

#{TEXT}
The following event just happened on @{title}:

  Event:          @{event}
  Time:           @{timestamp}
  File:           @{file_name}
  Uploaded By:    @{upload_user}
  Recipient:      @{download_user}
  Size/Progress:  @{bytes}
  IP Address:     @{ip}

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
<P><B>The following event just happened on <I>@{title}</I>:</B></P>
<BLOCKQUOTE>
<TABLE CLASS="info" CELLSPACING="0">
<TR><TD CLASS="l">Event:</TD><TD CLASS="v">@{event}</TD></TR>
<TR><TD CLASS="l">Time:</TD><TD CLASS="v">@{timestamp}</TD></TR>
<TR><TD CLASS="l">File:</TD><TD CLASS="v">@{file_name}</TD></TR>
<TR><TD CLASS="l">Uploaded By:</TD><TD CLASS="v">@{upload_user}</TD></TR>
<TR><TD CLASS="l">Recipient:</TD><TD CLASS="v">@{download_user}</TD></TR>
<TR><TD CLASS="l">Size/Progress:</TD><TD CLASS="v">@{bytes}</TD></TR>
<TR><TD CLASS="l">IP Address:</TD><TD CLASS="v">@{ip}</TD></TR>
</TABLE>
</BLOCKQUOTE>
</BODY>
</HTML>
