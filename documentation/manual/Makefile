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
# @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
# @license    http://framework.zend.com/license/new-bsd     New BSD License
#

# LANG is the list of target languages, separated by spaces or * for all
LANG=*

# Docbook 5 DTD
DOCBOOK_DTD?=http://www.docbook.org/xml/5.0/dtd/docbook.dtd

# You should grab the docbook-xsl-ns HTML chunk.xsl stylesheet and add it
# via an environment variable for best results.
DOCBOOK_XSL?=http://framework.zend.com/docbook-xsl/htmlhelp/htmlhelp.xsl

# For rendering PDF
DOCBOOK_FO_XSL?=http://framework.zend.com/docbook-xsl/fo/docbook.xsl

all: html

html:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    echo "LANG $$lang start." ; \
	    ( cd $$lang && autoconf && ./configure && \
	    make html "DOCBOOK_DTD=$(DOCBOOK_DTD)" "DOCBOOK_XSL=$(DOCBOOK_XSL)" 2>&1 | tee err.txt ) ; \
	    echo "LANG $$lang done." ; \
	done

pdf:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    echo "LANG $$lang start." ; \
	    ( cd $$lang && autoconf && ./configure && \
	    make pdf "DOCBOOK_DTD=$(DOCBOOK_DTD)" "DOCBOOK_XSL=$(DOCBOOK_XSL)" "DOCBOOK_FO_XSL=$(DOCBOOK_FO_XSL)" 2>&1 | tee err.txt ) ; \
	    echo "LANG $$lang done." ; \
	done

pdf-print:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    echo "LANG $$lang start." ; \
	    ( cd $$lang && autoconf && ./configure && \
	    make pdf-print "DOCBOOK_DTD=$(DOCBOOK_DTD)" "DOCBOOK_XSL=$(DOCBOOK_XSL)" "DOCBOOK_FO_XSL=$(DOCBOOK_FO_XSL)" 2>&1 | tee err.txt ) ; \
	    echo "LANG $$lang done." ; \
	done

check:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] || continue ; \
	    echo "LANG $$lang start." ; \
	    ( cd $$lang && autoconf && ./configure && \
	    make check "DOCBOOK_DTD=$(DOCBOOK_DTD)" "DOCBOOK_XSL=$(DOCBOOK_XSL)" 2>&1 | tee err.txt ) ; \
	    echo "LANG $$lang done." ; \
	done

clean:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] && [ -f $$lang/Makefile ] || continue ; \
	    echo "LANG $$lang start." ; \
	    ( cd $$lang && make clean) ; \
	    echo "LANG=$$lang done." ; \
	done

cleanall:
	@for lang in $(LANG) ; \
	do \
	    [ -d $$lang ] && [ -f $$lang/Makefile ] || continue ; \
	    echo "LANG $$lang start." ; \
	    ( cd $$lang && make cleanall) ; \
	    echo "LANG=$$lang done." ; \
	done
