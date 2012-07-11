<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                version="1.0"
		xmlns:doc="http://docbook.org/ns/docbook"
		xmlns:xlink="http://www.w3.org/1999/xlink"
                xmlns:php="http://php.net/xsl"
                xmlns:xi="http://www.w3.org/2001/XInclude">
<xsl:output method="text" indent="no" />
<xsl:strip-space elements="*"/>

<xsl:template match="*[name() != 'programlisting']/text()">
<xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(.))" />
</xsl:template>

<!-- title -->
<xsl:template match="/doc:section|/doc:appendix|/doc:chapter"> 
<xsl:if test="../@xml:id != ''">
.. _<xsl:value-of select="@xml:id" />:
</xsl:if>
<xsl:text>
</xsl:text>
<xsl:value-of select="php:function('ZendBin\RstConvert::maintitle', string(doc:title))" />
<xsl:apply-templates select="doc:section|doc:para|xi:include|doc:programlisting|doc:note" />
</xsl:template>

<!-- sub-titles -->
<xsl:template match="doc:title">
<xsl:if test="../@xml:id != ''">
.. _<xsl:value-of select="../@xml:id" />:
</xsl:if>
<xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.))" />
</xsl:template>

<!-- para -->
<xsl:template match="doc:para">  
<xsl:text>
</xsl:text>
<xsl:if test="name(..) != 'section'">
<xsl:text>    </xsl:text>
</xsl:if>
<xsl:apply-templates/>
<xsl:text>
</xsl:text>
</xsl:template>

<!-- link -->
<xsl:template match="//doc:link">
<xsl:value-of select="php:function('ZendBin\RstConvert::link', .)" />
</xsl:template>

<!-- include -->
<xsl:template match="//xi:include">
.. include:: <xsl:value-of select="php:function('ZendBin\RstConvert::XmlFileNameToRst', string(@href))" />
</xsl:template>

<!-- classname, methodname, type, command, property -->
<xsl:template match="//doc:classname|//doc:methodname|//doc:type|//doc:command|//doc:property"> ``<xsl:value-of select="normalize-space()" />`` </xsl:template>

<!-- acronym, emphasis -->
<xsl:template match="//doc:acronym|/doc:emphasis"> *<xsl:value-of select="normalize-space()" />* </xsl:template>

<!-- programlisting -->
<xsl:template match="//doc:programlisting">
<xsl:value-of select="php:function('ZendBin\RstConvert::programlisting', string(.))" />
</xsl:template>

<!-- itemizedlist -->
<xsl:template match="//doc:itemizedlist">
<xsl:value-of select="php:function('ZendBin\RstConvert::listitem', string(.))" />
</xsl:template>

<!-- note -->
<xsl:template match="//doc:note">
.. note::
    **<xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(doc:title))" />**
<xsl:apply-templates select="*[name(.) != 'title']"/>
</xsl:template>

<!-- table -->
<xsl:template match="//doc:table">
.. _<xsl:value-of select="@xml:id" />:
<xsl:text>
</xsl:text>
<xsl:value-of select="php:function('ZendBin\RstConvert::title', string(doc:title))" />
<xsl:value-of select="php:function('ZendBin\RstConvert::table', doc:tgroup)" />
</xsl:template>

</xsl:stylesheet>
