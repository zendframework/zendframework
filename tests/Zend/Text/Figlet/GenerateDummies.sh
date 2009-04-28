#!/bin/bash
# This file will generate all dummies which are required for the unit testing.
# The figlet program needs to be installed.
#
# Author: Ben 'DASPRiD' Scholzen
# Date:   2008/6/27 13:05

# Font used for unit testing, should be the default font
UTFLF="../../../../library/Zend/Text/Figlet/zend-framework.flf" 

# GZIP font for .gz test
rm -f GzippedFont.gz
cp $UTFLF GzippedFont
gzip GzippedFont

# Create an invalid font file
rm -f InvalidFont.flf
touch InvalidFont.flf

# Create dummies
figlet -f $UTFLF Dummy > StandardAlignLeft.figlet
figlet -f $UTFLF -c Dummy > StandardAlignCenter.figlet
figlet -f $UTFLF -r Dummy > StandardAlignRight.figlet
figlet -f $UTFLF -R Dummy > StandardRightToLeftAlignRight.figlet
figlet -f $UTFLF -Rc Dummy > StandardRightToLeftAlignCenter.figlet
figlet -f $UTFLF -Rl Dummy > StandardRightToLeftAlignLeft.figlet
figlet -f $UTFLF -w50 -r Dummy > OutputWidth50AlignRight.figlet
figlet -f $UTFLF -m-1 Dummy > NoSmush.figlet
figlet -f $UTFLF -m-1 -R Dummy > NoSmushRightToLeft.figlet
figlet -f $UTFLF -m0 Dummy > SmushDefault.figlet
figlet -f $UTFLF -m5 Dummy > SmushForced.figlet
echo 'Ömläüt' | iconv -f UTF-8 -t ISO-8859-15 | figlet -f $UTFLF > CorrectEncoding.figlet
figlet -f $UTFLF Dummy Dummy Dummy > WordWrapLeftToRight.figlet
figlet -f $UTFLF -R Dummy Dummy Dummy > WordWrapRightToLeft.figlet
figlet -f $UTFLF DummyDumDummy > CharWrapLeftToRight.figlet
figlet -f $UTFLF -R DummyDumDummy > CharWrapRightToLeft.figlet
echo -e "Dum\nDum\n\nDum" | figlet -f $UTFLF -p > ParagraphOn.figlet
echo -e "Dum\nDum\n\nDum" | figlet -f $UTFLF > ParagraphOff.figlet
