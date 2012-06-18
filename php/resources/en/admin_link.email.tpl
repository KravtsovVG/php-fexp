#{SUBJECT}
[FEXP] Administrive Link (@{file_name})

#{TEXT}
The following file is now available on @{title}:

  File:         @{file_name}
  MD5:          @{file_md5}
  Size:         @{file_size}
  Uploaded By:  @{upload_user}
  Uploaded On:  @{upload_timestamp}
  Expiration:   @{expire_timestamp}
  URL:          @{url}

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
<P><B>The following file is now available on <I>@{title}</I>:</B></P>
<BLOCKQUOTE>
<TABLE CLASS="info" CELLSPACING="0">
<TR><TD CLASS="l">File:</TD><TD CLASS="v">@{file_name}</TD></TR>
<TR><TD CLASS="l">MD5:</TD><TD CLASS="v">@{file_md5}</TD></TR>
<TR><TD CLASS="l">Size:</TD><TD CLASS="v">@{file_size}</TD></TR>
<TR><TD CLASS="l">Uploaded By:</TD><TD CLASS="v">@{upload_user}</TD></TR>
<TR><TD CLASS="l">Uploaded On:</TD><TD CLASS="v">@{upload_timestamp}</TD></TR>
<TR><TD CLASS="l">Expiration:</TD><TD CLASS="v">@{expire_timestamp}</TD></TR>
<TR><TD CLASS="l">URL:</TD><TD CLASS="v"><A HREF="@{url}">administrative link</A></TD></TR>
</TABLE>
</BLOCKQUOTE>
</BODY>
</HTML>
