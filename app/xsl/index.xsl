<?xml version="1.0"?>
<xsl:stylesheet
  version="1.0"
  xmlns="http://www.w3.org/1999/xhtml"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
exclude-result-prefixes="xsl">

<xsl:output method="xml" version="1.0" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.1//EN" indent="yes" />

<xsl:include href="error.xsl"/>

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

