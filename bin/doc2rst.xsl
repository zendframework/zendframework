<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:doc="http://docbook.org/ns/docbook"
                xmlns:php="http://php.net/xsl"
                xmlns:xi="http://www.w3.org/2001/XInclude"
                version="1.0">
<xsl:output method="text" indent="no" />
<xsl:strip-space elements="*"/>

<xsl:template match="//text()" name="text">
    <xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(.), preceding-sibling::*[1], following-sibling::*[1])" />
</xsl:template>
<xsl:template match="//text()" mode="indent">
    <xsl:call-template name="text" />
</xsl:template>

<xsl:template name="formatText">
    <xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(.))" />
</xsl:template>

<!-- root -->
<xsl:template match="/">
    <xsl:apply-templates />
</xsl:template>

<!-- Id -->
<xsl:template match="*[@xml:id]">
.. _<xsl:value-of select="@xml:id" />:
    <xsl:apply-templates />
</xsl:template>

<!-- title -->
<xsl:template match="//doc:title">
    <xsl:choose>
        <xsl:when test=".. = /">
            <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '=')" />
        </xsl:when>
        <xsl:when test="../.. = /">
            <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '-')" />
        </xsl:when>
        <xsl:when test="../../.. = /">
            <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '^')" />
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '^')" />
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!-- para, simpara -->
<xsl:template match="doc:para|doc:simpara">
    <xsl:apply-templates/>
    <xsl:text>
        
</xsl:text>
</xsl:template>

<xsl:template match="doc:para|doc:simpara" mode="indent">
<xsl:text>   </xsl:text><xsl:apply-templates mode="indent"/>
<xsl:text>

</xsl:text>
</xsl:template>

<!-- link, uri, xref -->
<xsl:template match="//doc:link|//doc:uri|//doc:xref" name="link">
    <xsl:value-of select="php:function('ZendBin\RstConvert::link', .)" /><xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if>
</xsl:template>
<xsl:template match="//doc:link|//doc:uri|//doc:xref" mode="indent">
    <xsl:call-template name="link"/>
</xsl:template>

<!-- footnote -->
<xsl:template match="//doc:footnote" name="footnote">
    <xsl:value-of select="php:function('ZendBin\RstConvert::footnote', string(.))" />
    <xsl:if test="name(following-sibling::node()[1]) != ''">
        <xsl:text> </xsl:text>
    </xsl:if>
</xsl:template>
<xsl:template match="//doc:link|//doc:uri" mode="indent"><xsl:call-template name="link"/></xsl:template>

<!-- include -->
<xsl:template match="//xi:include">
.. include:: <xsl:value-of select="php:function('ZendBin\RstConvert::xmlFileNameToRst', string(@href))" />
</xsl:template>

<!--
##############
### INLINE ###
##############
-->
<!-- literal, classname, interfacename, exceptionname, methodname, function, type, command, property, constant, filename, varname -->
<xsl:template match="//doc:literal|//doc:classname|//doc:interfacename|//doc:exceptionname|//doc:type|//doc:methodname|//doc:function|//doc:command|//doc:property|//doc:constant|//doc:filename|//doc:varname" name="literal">``<xsl:call-template name="formatText" />``<xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:literal|//doc:classname|//doc:interfacename|//doc:exceptionname|//doc:type|//doc:methodname|//doc:function|//doc:command|//doc:property|//doc:constant|//doc:filename|//doc:varname" mode="indent"><xsl:call-template name="literal" /></xsl:template>

<!-- acronym, code  -->
<xsl:template match="//doc:acronym|//doc:code" name="acronym">*<xsl:call-template name="formatText" />*<xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:acronym|//doc:code" mode="indent"><xsl:call-template name="acronym" /></xsl:template>

<!-- emphasis, firstterm  -->
<xsl:template match="//doc:emphasis|//doc:firstterm" name="emphasis">**<xsl:call-template name="formatText" />**<xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:emphasis|//doc:firstterm" mode="indent"><xsl:call-template name="emphasis" /></xsl:template>

<!-- trademark -->
<xsl:template match="//doc:trademark" name="trademark"><xsl:call-template name="formatText" />(tm)<xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:trademark" mode="indent"><xsl:call-template name="trademark" /></xsl:template>

<!-- copyright -->
<xsl:template match="//doc:copyright" name="copyright">(c) <xsl:call-template name="formatText" /><xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:copyright" mode="indent"><xsl:call-template name="copyright" /></xsl:template>

<!-- superscript -->
<xsl:template match="//doc:superscript" name="superscript">:sup:`<xsl:call-template name="formatText" />`<xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:superscript" mode="indent"><xsl:call-template name="superscript" /></xsl:template>

<!-- superscript -->
<xsl:template match="//doc:citetitle" name="citetitle">:t:`<xsl:call-template name="formatText" />`<xsl:if test="name(following-sibling::node()[1]) != ''"><xsl:text> </xsl:text></xsl:if></xsl:template>
<xsl:template match="//doc:citetitle" mode="indent"><xsl:call-template name="citetitle" /></xsl:template>

<!--
##############
### BLOCKS ###
##############
-->
<!-- blockquote -->
<xsl:template match="//doc:blockquote" mode="indent">
   | <xsl:apply-templates />
</xsl:template>

<!-- literallayout -->
<xsl:template match="//doc:literallayout">
    <xsl:text>
</xsl:text>
::
    <xsl:value-of select="php:function('ZendBin\RstConvert::indent', string(.))" />
    <xsl:text>
</xsl:text>
</xsl:template>

<!-- programlisting -->
<xsl:template match="//doc:programlisting">
.. code-block:: <xsl:value-of select="string(@language)" />
   :linenos:
    <xsl:value-of select="php:function('ZendBin\RstConvert::indent', string(.))" />
</xsl:template>

<!-- programlisting -->
<xsl:template match="//doc:programlisting" mode="indent">
<xsl:text>
</xsl:text>
   .. code-block:: <xsl:value-of select="string(@language)" />
      :linenos:
    <xsl:value-of select="php:function('ZendBin\RstConvert::indent', php:function('ZendBin\RstConvert::indent', string(.)))" />
</xsl:template>

<!-- varlistentry/term -->
<xsl:template match="//doc:varlistentry/doc:term">
**<xsl:call-template name="formatText" />**
    <xsl:text>
</xsl:text>
</xsl:template>

<!-- varlistentry/term -->
<xsl:template match="//doc:varlistentry/doc:term" mode="indent">
   **<xsl:call-template name="formatText" />**
    <xsl:text>
</xsl:text>
</xsl:template>

<!-- refentry -->
<xsl:template match="//doc:refentry">
.. _<xsl:value-of select="@xml:id" />:
    <xsl:text>
</xsl:text>
    <xsl:apply-templates select="doc:refnamediv/doc:refname" />
    <xsl:text>
</xsl:text>
    <xsl:apply-templates select="doc:refnamediv/doc:refpurpose" mode="indent" />
    <xsl:text>
</xsl:text>
    <xsl:apply-templates select="doc:refsynopsisdiv/doc:methodsynopsis" mode="indent" />

    <xsl:apply-templates select="doc:refsection" mode="indent" />
    <xsl:text>
</xsl:text>
</xsl:template>

<!-- refpurpose -->
<xsl:template match="//doc:refpurpose" mode="indent">
    <xsl:text>   </xsl:text><xsl:apply-templates mode="indent"/>
</xsl:template>

<!-- methodsynopsis -->
<xsl:template match="//doc:methodsynopsis" name="methodsynopsis">.. c:function:: <xsl:if test="doc:type != ''"><xsl:value-of select="doc:type" /></xsl:if> <xsl:value-of select="doc:methodname" />(<xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(*//doc:funcparams))" />)
    <xsl:text>
</xsl:text>
</xsl:template>
<xsl:template match="//doc:methodsynopsis" mode="indent"><xsl:text>   </xsl:text><xsl:call-template name="methodsynopsis" /></xsl:template>

<!-- refsection -->
<xsl:template match="//doc:refsection/doc:title" name="refsection_title">
   **<xsl:call-template name="formatText" />**
    <xsl:text>
</xsl:text>
</xsl:template>

<xsl:template match="//doc:refsection/doc:title" mode="indent">
    <xsl:text>   </xsl:text><xsl:call-template name="refsection_title" />
</xsl:template>

<!-- orderedlist|itemizedlist -->
<xsl:template match="//doc:orderedlist|//doc:itemizedlist">
    <xsl:text>
</xsl:text>
    <xsl:apply-templates />
</xsl:template>

<!-- orderedlist|itemizedlist -->
<xsl:template match="//doc:orderedlist|//doc:itemizedlist" mode="indent">
    <xsl:text>
</xsl:text>
    <xsl:apply-templates mode="indent" />
</xsl:template>

<!-- listitem -->
<xsl:template match="//doc:listitem">- <xsl:apply-templates select="*[1]" />
    <xsl:if test="*[position()>1] != ''">
        <xsl:apply-templates select="*[position()>1]" mode="indent"/>
    </xsl:if>
</xsl:template>

<!-- listitem -->
<xsl:template match="//doc:listitem" mode="indent">   - <xsl:apply-templates select="*[1]" />
<!-- Indent with 2 spaces -->
    <xsl:if test="*[position()>1] != ''">
        <xsl:text>  </xsl:text><xsl:apply-templates select="*[position()>1]" mode="indent"/>
    </xsl:if>
</xsl:template>

<!-- ordered listitem -->
<xsl:template match="//doc:orderedlist/doc:listitem"><xsl:value-of select="position()" />. <xsl:apply-templates select="*[1]" />
    <xsl:apply-templates select="*[position()>1]" mode="indent"/>
</xsl:template>

<!-- ordered listitem -->
<xsl:template match="//doc:orderedlist/doc:listitem" mode="indent">   <xsl:value-of select="position()" />. <xsl:apply-templates select="*[1]" />
    <xsl:text>   </xsl:text><xsl:apply-templates select="*[position()>1]" mode="indent"/>
</xsl:template>

<!--
###################
### ADMONITIONS ###
###################
 -->
<!-- normal -->
<xsl:template match="//doc:caution|//doc:important|//doc:note|//doc:tip|//doc:warning">
    <xsl:text>
</xsl:text>
.. <xsl:value-of select="name()" />::
    <xsl:if test="doc:title != '' or doc:info/doc:title != ''">   **<xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(doc:info/doc:title|doc:title))" />**
    <xsl:text>
</xsl:text>
    </xsl:if>
    <xsl:apply-templates select="*[(name(.) != 'title') and (name(.) != 'info')]" mode="indent"/>
</xsl:template>

<!-- indent -->
<xsl:template match="//doc:caution|//doc:important|//doc:note|//doc:tip|//doc:warning" mode="indent">
    <xsl:text>
</xsl:text>
   .. <xsl:value-of select="name()" />::
    <xsl:if test="doc:title != '' or doc:info/doc:title != ''">      **<xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(doc:info/doc:title|doc:title))" />**
    <xsl:text>
</xsl:text>
    </xsl:if>
    <xsl:text>   </xsl:text><xsl:apply-templates select="*[(name(.) != 'title') and (name(.) != 'info')]" mode="indent"/>
</xsl:template>

<!--
#############
### IMAGE ###
#############
-->
<xsl:template match="//doc:imagedata" name="imagedata">
.. image:: <xsl:value-of select="php:function('ZendBin\RstConvert::imageFileName', string(@fileref))" />
<xsl:if test="@width != ''">
   :width: <xsl:value-of select="@width" />
</xsl:if>
<xsl:if test="@align != ''">
   :align: <xsl:value-of select="@align" />
</xsl:if>
</xsl:template>
<xsl:template match="//doc:imagedata" mode="indent">
   .. image:: <xsl:value-of select="php:function('ZendBin\RstConvert::imageFileName', string(@fileref))" />
<xsl:if test="@width != ''">
      :width: <xsl:value-of select="@width" />
</xsl:if>
<xsl:if test="@align != ''">
      :align: <xsl:value-of select="@align" />
</xsl:if>
</xsl:template>

<!--
#############
### TABLE ###
#############
 -->
<!-- Title -->
<xsl:template match="//doc:table/doc:title" name="table_title">
.. table:: <xsl:call-template name="formatText" />
<xsl:text>

</xsl:text>
</xsl:template>
<xsl:template match="//doc:table/doc:title" mode="indent">
<xsl:text>   </xsl:text><xsl:call-template name="table_title" />
</xsl:template>

<!-- Content -->
<xsl:template match="//doc:table/doc:tgroup">
<xsl:value-of select="php:function('ZendBin\RstConvert::indent', php:function('ZendBin\RstConvert::table', .))" />
</xsl:template>
<xsl:template match="//doc:table/doc:tgroup" mode="indent">
<xsl:value-of select="php:function('ZendBin\RstConvert::indent', php:function('ZendBin\RstConvert::indent', php:function('ZendBin\RstConvert::table', .)))" />
</xsl:template>

</xsl:stylesheet>
