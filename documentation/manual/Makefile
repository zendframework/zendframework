#
# Zend Framework
#
# LICENSE
#
# This source file is subject to the new BSD license that is bundled
# with this package in the file LICENSE.txt.
# It is also available through the world-wide-web at this URL:
# http://framework.zend.com/license/new-bsd
# If you did not receive a copy of the license and are unable to
# obtain it through the world-wide-web, please send an email
# to license@zend.com so we can send you a copy immediately.
#
# @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License
#

LANG=*
DOCBOOK_DTD=http://framework.zend.com/docbook/xml/4.5/docbookx.dtd
DOCBOOK_XSL=http://framework.zend.com/docbook-xsl/htmlhelp/htmlhelp.xsl

all: html

html: 
	@for lang in ./$(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    ( cd $$lang && autoconf && ./configure && \
		make "DOCBOOK_DTD=$(DOCBOOK_DTD)" "DOCBOOK_XSL=$(DOCBOOK_XSL)" 2>&1 | tee err.txt ) ; \
	    echo "LANG $$lang done." ; \
	done

clean:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    ( cd $$lang && rm -rf html/*.html html/HTML.manifest html/figures _temp_*.xml) ; \
	    echo "LANG=$$lang done." ; \
	done

cleanall:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    ( cd $$lang && rm -rf html/*.html html/HTML.manifest html/figures _temp_*.xml \
		config.* configure autom4te.cache Makefile html.xsl manual.xml err.txt ) ; \
	    echo "LANG $$lang done." ; \
	done
