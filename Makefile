# Makefile
#
# This is just a generic driver makefile for the Makefile.language. The poin
# is that one should be able to have this one alongside the master.
#
# This file is used here in accordance with the GNU GPL which is copied in the 
# COPYING file accompanying this file.
#
# Copyright 2006, Creative Commons.
# Copyright 2006, Jon Phillips.
#

all:
	$(MAKE) -f Makefile.language all

clean:
	$(MAKE) -f Makefile.language clean 

create-pot: 
	$(MAKE) -f Makefile.language create-pot

update-po-files: 
	$(MAKE) -f Makefile.language update-po-files

check: 
	$(MAKE) -f Makefile.language check
