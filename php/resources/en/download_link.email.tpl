#{SUBJECT}
[FEXP] Download Link (@{file_name})

#{TEXT}
You have been granted download access to the following file on @{title}:

  File:         @{file_name}
  MD5:          @{file_md5}
  Size:         @{file_size}
  Uploaded By:  mailto:@{upload_user}
  Uploaded On:  @{upload_timestamp}
  Expiration:   @{expire_timestamp}
  URL:          @{url}

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
<P><B>You have been granted download access to the following file on <I>@{title}</I>:</B></P>
<BLOCKQUOTE>
<TABLE CLASS="info" CELLSPACING="0">
<TR><TD CLASS="l">File:</TD><TD CLASS="v">@{file_name}</TD></TR>
<TR><TD CLASS="l">MD5:</TD><TD CLASS="v">@{file_md5}</TD></TR>
<TR><TD CLASS="l">Size:</TD><TD CLASS="v">@{file_size}</TD></TR>
<TR><TD CLASS="l">Uploaded By:</TD><TD CLASS="v"><A HREF="mailto:@{upload_user}">@{upload_user}</A></TD></TR>
<TR><TD CLASS="l">Uploaded On:</TD><TD CLASS="v">@{upload_timestamp}</TD></TR>
<TR><TD CLASS="l">Expiration:</TD><TD CLASS="v">@{expire_timestamp}</TD></TR>
<TR><TD CLASS="l">URL:</TD><TD CLASS="v"><A HREF="@{url}">download link</A></TD></TR>
</TABLE>
</BLOCKQUOTE>
<P><B>Message:</B></P>
<P>@{message}</P>
</BODY>
</HTML>
