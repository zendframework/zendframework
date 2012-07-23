<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:doc="http://docbook.org/ns/docbook"
                xmlns:php="http://php.net/xsl"
                xmlns:xi="http://www.w3.org/2001/XInclude"
                version="1.0">
<xsl:output method="text" indent="no" encoding="UTF-8" />
<xsl:strip-space elements="*"/>

    <xsl:variable name="default_indentation">3</xsl:variable>

    <xsl:variable name="lcletters">abcdefghijklmnopqrstuvwxyz</xsl:variable>
    <xsl:variable name="ucletters">ABCDEFGHIJKLMNOPQRSTUVWXYZ</xsl:variable>

    <xsl:template match="//text()" name="text">
        <xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(.), preceding-sibling::*[1], following-sibling::*[1])"/>
    </xsl:template>

    <xsl:template name="formatText">
        <xsl:value-of select="php:function('ZendBin\RstConvert::formatText', string(.))"/>
    </xsl:template>

    <!-- Id -->
    <xsl:template match="*[@xml:id]" name="id">
        <xsl:text>.. _</xsl:text><xsl:value-of select="@xml:id"/><xsl:text>:&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
        <xsl:apply-templates/>
    </xsl:template>

    <!--
    ##############
    ### TITLES ###
    ##############
    -->
    <!-- article title -->
    <xsl:template match="/doc:article/doc:title">
        <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '#', true())"/>
    </xsl:template>

    <!-- chapter title -->
    <xsl:template match="//doc:chapter/doc:title|//doc:appendix/doc:title">
        <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '*', true())"/>
    </xsl:template>

    <!-- section title -->
    <xsl:template match="/doc:section/doc:title">
        <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '=')"/>
    </xsl:template>

    <!-- subsection title -->
    <xsl:template match="/*//doc:section/doc:title">
        <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '-')"/>
    </xsl:template>

    <!-- subsection title -->
    <xsl:template match="/*/*//doc:section/doc:title">
        <xsl:value-of select="php:function('ZendBin\RstConvert::title', string(.), '^')"/>
    </xsl:template>

    <!-- rest of titles out of the structure of the document -->
    <xsl:template match="//doc:title" priority="-1" name="default_title">
        <xsl:text>.. rubric:: </xsl:text><xsl:apply-templates/><xsl:text>&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!--
    ##################
    ### PARAGRAPHS ###
    ##################
    -->
    <!-- para, simpara -->
    <xsl:template match="doc:para|doc:simpara">
        <xsl:variable name="body">
            <xsl:apply-templates mode="para"/>
        </xsl:variable>
        <xsl:value-of select="php:function('ZendBin\RstConvert::wrap', $body)"/><xsl:text>&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!-- DocBook allow this elements as inline elements but in rST this will be block elements -->
    <xsl:template match="*|text()" mode="para">
        <xsl:apply-templates select="."/>
    </xsl:template>
    <xsl:template match="doc:variablelist|doc:example|doc:table|doc:programlisting|doc:note|doc:itemizedlist|doc:orderedlist" mode="para">
        <xsl:text>&#xA;</xsl:text><xsl:text>&#xA;</xsl:text>
        <xsl:apply-templates select="." mode="indent"/>
    </xsl:template>
    <xsl:template match="doc:para//*[@xml:id]" priority="1">
        <xsl:text>&#xA;</xsl:text><xsl:text>&#xA;</xsl:text>
        <xsl:text>   .. _</xsl:text><xsl:value-of select="@xml:id"/><xsl:text>:&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
        <xsl:apply-templates mode="indent"/>
    </xsl:template>

    <!-- indent -->
    <xsl:template match="*" mode="indent">
        <xsl:param name="indent" select="$default_indentation" />
        <xsl:value-of select="php:function('ZendBin\RstConvert::addIndent', $indent)"/>
        <xsl:variable name="body">
            <xsl:apply-templates select="." />
        </xsl:variable>
        <xsl:choose>
            <xsl:when test="$indent = 2">
                <xsl:value-of select="php:function('ZendBin\RstConvert::indent2', $body)"/>
            </xsl:when>
            <xsl:when test="$indent = 7">
                <xsl:value-of select="php:function('ZendBin\RstConvert::indent7', $body)"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="php:function('ZendBin\RstConvert::indent', $body)"/>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:value-of select="php:function('ZendBin\RstConvert::removeIndent', $indent)"/>
    </xsl:template>

    <!-- link, uri, xref -->
    <xsl:template match="//doc:link|//doc:uri|//doc:xref" name="link">
        <xsl:value-of select="php:function('ZendBin\RstConvert::link', .)"/>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- footnote -->
    <xsl:template match="//doc:footnote" name="footnote">
        <xsl:variable name="body">
            <xsl:apply-templates mode="indent">
                <xsl:with-param name="indent" select="7"/>
            </xsl:apply-templates>
        </xsl:variable>
        <xsl:value-of select="php:function('ZendBin\RstConvert::footnote', $body)"/>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- include -->
    <xsl:template match="//xi:include">
        <xsl:text>.. include:: </xsl:text><xsl:value-of select="php:function('ZendBin\RstConvert::xmlFileNameToRst', string(@href))"/>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!--
    ##############
    ### INLINE ###
    ##############
    -->
    <!-- literal, classname, interfacename, exceptionname, methodname, function, type, command, property, constant, filename, varname -->
    <xsl:template match="//doc:literal|//doc:classname|//doc:interfacename|//doc:exceptionname|//doc:type|//doc:methodname|//doc:function|//doc:command|//doc:property|//doc:constant|//doc:filename|//doc:varname" name="literal">
        <xsl:text>``</xsl:text><xsl:value-of select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(.), '`')"/><xsl:text>``</xsl:text>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- acronym, code  -->
    <xsl:template match="//doc:acronym|//doc:code" name="acronym">
        <xsl:text>*</xsl:text><xsl:value-of select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(.), '*')"/><xsl:text>*</xsl:text>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- emphasis, firstterm  -->
    <xsl:template match="//doc:emphasis|//doc:firstterm" name="emphasis">
        <xsl:text>**</xsl:text><xsl:value-of select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(.), '*')"/><xsl:text>**</xsl:text>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- trademark -->
    <xsl:template match="//doc:trademark" name="trademark"><xsl:call-template name="formatText"/><xsl:text>(tm)</xsl:text>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- copyright -->
    <xsl:template match="//doc:copyright" name="copyright"><xsl:text>(c)</xsl:text>
        <xsl:call-template name="formatText"/>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- superscript -->
    <xsl:template match="//doc:superscript" name="superscript">:sup:`<xsl:call-template name="formatText"/>` <xsl:text/>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- subscript -->
    <xsl:template match="//doc:subscript" name="subcript">:sub:`<xsl:call-template name="formatText"/>` <xsl:text/>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- citetitle -->
    <xsl:template match="//doc:citetitle" name="citetitle">:t:`<xsl:call-template name="formatText"/>` <xsl:text/>
        <xsl:if test="name(following-sibling::node()[1]) != ''">
            <xsl:text> </xsl:text>
        </xsl:if>
    </xsl:template>

    <!--
    ##############
    ### BLOCKS ###
    ##############
    -->
    <!-- blockquote -->
    <xsl:template match="//doc:blockquote">
        <xsl:text>&#xA;</xsl:text>
        <xsl:text>| </xsl:text>
        <xsl:apply-templates/>
    </xsl:template>

    <!-- literallayout -->
    <xsl:template match="//doc:literallayout">
        <xsl:text>&#xA;</xsl:text>
        <xsl:text>::&#xA;</xsl:text>
        <xsl:value-of select="php:function('ZendBin\RstConvert::addIndent', $default_indentation)"/>
        <xsl:value-of select="php:function('ZendBin\RstConvert::indent', string(.))"/>
        <xsl:value-of select="php:function('ZendBin\RstConvert::removeIndent', $default_indentation)"/>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!-- programlisting -->
    <xsl:template match="//doc:programlisting" name="programlisting">
        <xsl:variable name="language">
            <xsl:call-template name="program_language"/>
        </xsl:variable>
        <xsl:text>.. code-block:: </xsl:text><xsl:value-of select="$language"/>
        <xsl:text>&#xA;</xsl:text>
        <xsl:text>   :linenos:</xsl:text>
        <xsl:text>&#xA;</xsl:text>
        <xsl:value-of select="php:function('ZendBin\RstConvert::addIndent', $default_indentation)"/>
        <xsl:value-of select="php:function('ZendBin\RstConvert::indent', string(.))"/>
        <xsl:value-of select="php:function('ZendBin\RstConvert::removeIndent', $default_indentation)"/>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!-- varlistentry/term -->
    <xsl:template match="//doc:varlistentry/doc:term">
        <xsl:if test="normalize-space(.) != ''">
            <xsl:text/>**<xsl:value-of select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(.), '*')"/>**<xsl:text>&#xA;</xsl:text>
        </xsl:if>
    </xsl:template>

    <!-- varlistentry/listitem -->
    <xsl:template match="//doc:varlistentry/doc:listitem" priority="1">
        <xsl:apply-templates mode="indent"/>
    </xsl:template>
    <!-- varlistentry/listitem/methodsynopsys -->
    <xsl:template match="//doc:varlistentry/doc:listitem/doc:methodsynopsis" priority="1">
        <xsl:call-template name="methodsynopsis"/>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!--
    #################
    ### REFERENCE ###
    #################
    -->
    <!-- refentry -->
    <xsl:template match="//doc:refentry">
        <xsl:text>.. _</xsl:text><xsl:value-of select="@xml:id"/><xsl:text>:&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
        <xsl:apply-templates select="doc:refnamediv/doc:refname"/>
        <xsl:text>&#xA;</xsl:text>
        <xsl:apply-templates select="doc:refnamediv/doc:refpurpose" mode="indent"/>
        <xsl:text>&#xA;</xsl:text>
        <xsl:apply-templates select="doc:refsynopsisdiv/doc:methodsynopsis" mode="indent"/>

        <xsl:apply-templates select="doc:refsection" mode="indent"/>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!-- methodsynopsis -->
    <xsl:template match="//doc:methodsynopsis" name="methodsynopsis">
        <xsl:if test="doc:type != ''">
            <xsl:value-of select="doc:type"/>:<xsl:text/>
        </xsl:if>
        <xsl:text>``</xsl:text><xsl:value-of select="doc:methodname"/>
        <xsl:text>(</xsl:text>
        <xsl:value-of select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(string(*//doc:funcparams)), '*')"/>
        <xsl:text>)``</xsl:text>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!-- refsection -->
    <xsl:template match="//doc:refsection/doc:title" name="refsection_title">
        <xsl:text>&#xA;</xsl:text>
        <xsl:text>**</xsl:text><xsl:value-of select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(.), '*')"/><xsl:text>**</xsl:text>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!--
   #############
   ### LISTS ###
   #############
    -->
    <!-- orderedlist|itemizedlist -->
    <xsl:template match="//doc:orderedlist|//doc:itemizedlist">
        <xsl:call-template name="newline"/>
        <xsl:apply-templates/>
    </xsl:template>

    <!-- listitem -->
    <xsl:template match="//doc:listitem">
        <xsl:variable name="body">
            <xsl:apply-templates mode="indent">
                <xsl:with-param name="indent" select="2"/>
            </xsl:apply-templates>
        </xsl:variable>
        <xsl:text>-</xsl:text><xsl:value-of select="substring($body, 2)"/>
    </xsl:template>

    <!-- ordered listitem -->
    <xsl:template match="//doc:orderedlist/doc:listitem">
        <xsl:variable name="body">
            <xsl:apply-templates mode="indent">
                <xsl:with-param name="indent" select="2"/>
            </xsl:apply-templates>
        </xsl:variable>
        <xsl:text>.</xsl:text><xsl:value-of select="substring($body, 2)"/>
    </xsl:template>

    <!--
   ###################
   ### ADMONITIONS ###
   ###################
    -->
    <xsl:template match="//doc:caution|//doc:important|//doc:note|//doc:tip|//doc:warning" name="admonition">
        <xsl:text>.. </xsl:text><xsl:value-of select="name()"/><xsl:text>::&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
        <xsl:if test="doc:title != '' or doc:info/doc:title != ''">
            <xsl:text>   **</xsl:text>
            <xsl:value-of
                    select="php:function('ZendBin\RstConvert::escapeChar', normalize-space(doc:title|doc:info/doc:title), '*')"/>
            <xsl:text>**</xsl:text>
            <xsl:text>&#xA;</xsl:text>
            <xsl:text>&#xA;</xsl:text>
        </xsl:if>
        <xsl:apply-templates select="*[(name(.) != 'title') and (name(.) != 'info')]" mode="indent">
            <xsl:with-param name="indent" select="3"/>
        </xsl:apply-templates>
    </xsl:template>

    <!--
    #############
    ### IMAGE ###
    #############
    -->
    <xsl:template match="//doc:imagedata" name="imagedata">
        <xsl:text>.. image:: </xsl:text>
        <xsl:value-of select="php:function('ZendBin\RstConvert::imageFileName', string(@fileref))"/>
        <xsl:text>&#xA;</xsl:text>
        <xsl:if test="@width != ''">
            <xsl:text>   :width: </xsl:text><xsl:value-of select="@width"/>
            <xsl:text>&#xA;</xsl:text>
        </xsl:if>
        <xsl:if test="@align != ''">
            <xsl:text>   :align: </xsl:text><xsl:value-of select="@align"/>
        </xsl:if>
    </xsl:template>

    <!--
   #############
   ### TABLE ###
   #############
    -->
    <!-- Title -->
    <xsl:template match="//doc:table/doc:title" name="table_title">
        <xsl:text>.. table:: </xsl:text>
        <xsl:call-template name="formatText"/>
        <xsl:text>&#xA;</xsl:text>
        <xsl:text>&#xA;</xsl:text>
    </xsl:template>

    <!-- Content -->
    <xsl:template match="//doc:table/doc:tgroup">
        <xsl:value-of select="php:function('ZendBin\RstConvert::addIndent', $default_indentation)"/>
        <xsl:value-of select="php:function('ZendBin\RstConvert::indent', php:function('ZendBin\RstConvert::table', .))"/>
        <xsl:value-of select="php:function('ZendBin\RstConvert::removeIndent', $default_indentation)"/>
    </xsl:template>

    <!-- Transforms a program language in a valid name for Pygments lexer (http://pygments.org) -->
    <xsl:template name="program_language">
        <xsl:choose>
            <xsl:when test="@language = 'txt'">text</xsl:when>
            <xsl:when test="@language = 'plain'">text</xsl:when>
            <xsl:when test="@language = 'querystring'">text</xsl:when>
            <xsl:when test="@language = 'dosinig'">ini</xsl:when>
            <xsl:when test="@language = 'shell'">console</xsl:when>
            <xsl:when test="@language = 'output'">console</xsl:when>
            <xsl:when test="@language = 'httpd.conf'">apache</xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="translate(@language, $ucletters, $lcletters)"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Newline, introduces a blankline at the beggining if needed. -->
    <xsl:template name="newline">
        <xsl:if test="substring-after(preceding-sibling::node()[1], '&#xA;&#xA;') != ''"><xsl:text>&#xA;</xsl:text></xsl:if>
        <xsl:if test="substring-after(preceding-sibling::node()[1], '&#xA;&#xA;') != ''"><xsl:text>&#xA;</xsl:text></xsl:if>
    </xsl:template>

</xsl:stylesheet>