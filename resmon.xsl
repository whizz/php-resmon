<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="ResmonResults">
<html>
<head>
    <title>Resmon Results</title>
    <link rel="stylesheet" type="text/css" href="resmon.css" />
</head>
<body>
    <p>
    Total checks:
    <xsl:value-of select="count(ResmonResult)" />

    </p>
    <xsl:for-each select="ResmonResult">
        <xsl:sort select="@module" />
        <xsl:sort select="@service" />
        <div class="item">
            <div class="info">
                Last check: <xsl:value-of select="last_runtime_seconds" />
                /
                Last updated: <xsl:value-of select="last_update" />

            </div>
            <h1>
                <a>
                    <xsl:attribute name="href">
                        /<xsl:value-of select="@module" />
                    </xsl:attribute>
                    <xsl:value-of select="@module" />
                </a>`<a>

                    <xsl:attribute name="href">
                        /<xsl:value-of select="@module"
                            />/<xsl:value-of select="@service" />
                    </xsl:attribute>
                    <xsl:value-of select="@service" />
                </a>
            </h1>
            <ul>
                <xsl:for-each select="metric">

                    <xsl:sort select="@name" />
                    <li><xsl:value-of select="@name" /> = 
                    <xsl:value-of select="." /></li>
                </xsl:for-each>
            </ul>
        </div>
    </xsl:for-each>
</body>
</html>
</xsl:template>

</xsl:stylesheet>