<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!--
	$Id: xmlStyle.xsl,v 1.3 2004/02/20 14:50:12 entropie Exp $
//-->
  <xsl:template match="/">
    <html>
      <head>
        <title>Jamp - XMLDump</title>
        <link rel="stylesheet" media="screen" href="inc/xmlStyle.css" />
      </head>
      <body>
        <table cellpadding="0" cellspacing="0">
          <xsl:apply-templates />
        </table>
      </body>
    </html>
  </xsl:template>


  <xsl:template match="JampCAT">
	  <tr>
	    <th><a><xsl:attribute name="href">index.php?pathid=<xsl:value-of select="@PID" /></xsl:attribute><xsl:value-of select="@path" /></a></th>
	    <th style="text-align:center"><xsl:value-of select="@songcount" /> Files</th>
	    <th style="text-align:right"><xsl:value-of select="@fullsize" /></th>
	  </tr>
		<xsl:apply-templates />
  </xsl:template>

  <xsl:template match="song">
		<tr>
		  <td colspan="2" ><a><xsl:attribute name="href">play.php?trackid=<xsl:value-of select="@ID" /></xsl:attribute><xsl:value-of select="@name" /></a></td>
	  	<td align="right"><xsl:value-of select="@size" /></td>
		</tr>
  </xsl:template>

</xsl:stylesheet>