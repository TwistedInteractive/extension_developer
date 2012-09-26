<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<!--
		XSL Template for the publish page of {{FIELD_NAME}}
	-->

	<xsl:output omit-xml-declaration="yes" />

	<xsl:template match="/">
		<input type="text" name="{data/field/@name}" value="{data/field/@value}" />
	</xsl:template>

</xsl:stylesheet>