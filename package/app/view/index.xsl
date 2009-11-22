<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="../../framework/view/error.xsl"/>

<xsl:output doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"/>
<xsl:template match="/">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Welcome to xFrame</title>
    </head>
    <body>
        <h1>You have successfully installed xFrame</h1>
        <xsl:call-template name="display-errors" />
    </body>
    </html>
</xsl:template>

</xsl:stylesheet>

