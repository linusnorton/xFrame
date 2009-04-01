<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:include href="error.xsl"/>

    <xsl:output method="html" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.1//EN" />
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

