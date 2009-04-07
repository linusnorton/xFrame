<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output doctype-public="-//W3C//DTD XHTML 1.1//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"/>

<xsl:template match="/">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <title>Error generating page</title>
</head>
<body>
    <h1>I'm sorry there was an error generating this page</h1>
    <xsl:call-template name="display-errors" />
</body>
</html>
</xsl:template>

<xsl:template name="display-errors">
    <xsl:if test="count(/root/errors/error)!=0">
        <h2>The following errors occured:</h2>
        <xsl:for-each select="/root/errors/error">
            <div style="margin:5px;padding: 5px; border: 1px solid black;">
                <strong><xsl:value-of select="./@type" /></strong>: <xsl:value-of select="./message" disable-output-escaping="yes" /><br />
                <strong>Backtrace:</strong>
                <p>
                    <xsl:for-each select="./backtrace/step">
                        [<xsl:value-of select="./@number" />] line <xsl:value-of select="./@line" /> of <xsl:value-of select="./@file" />
                        called <xsl:value-of select="./@class" />
                        <xsl:if test="./@class!=''">-></xsl:if><xsl:value-of select="./@function" />()
                        <br />
                    </xsl:for-each>
                </p>
            </div>
        </xsl:for-each>
    </xsl:if>
    <xsl:if test="count(/root/exceptions/exception)!=0">
        <h2>The following exceptions occured:</h2>
        <xsl:for-each select="/root/exceptions/exception">
            <div style="margin:5px;padding: 5px; border: 1px solid black;">
                <strong><xsl:if test="./@caught='false'">Uncaught </xsl:if>Exception</strong>: <xsl:value-of select="./message" disable-output-escaping="yes" /><br />
                <strong>Backtrace:</strong>
                <p>
                    <xsl:for-each select="./backtrace/step">
                        [<xsl:value-of select="./@number" />] line <xsl:value-of select="./@line" /> of <xsl:value-of select="./@file" />
                        called <xsl:value-of select="./@class" />
                        <xsl:if test="./@class!=''">-></xsl:if><xsl:value-of select="./@function" />()
                        <br />
                    </xsl:for-each>
                </p>
            </div>
        </xsl:for-each>
    </xsl:if>
</xsl:template>
</xsl:stylesheet>

