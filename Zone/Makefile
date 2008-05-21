# ============================================
# $Id: Makefile,v 1.2 2008/05/21 09:41:29 eric Exp $
#
# ============================================



pages_csv = $(patsubst %.csv,$(pubdir)/$(appname)/Layout/%.csv,$(wildcard *csv))


$(pubdir)/$(appname)/Layout:
	mkdir $@

$(pubdir)/$(appname)/Layout/%.csv: %.csv $(pubdir)/$(appname)/Layout
	cp $< $@

publish: $(pubdir)/$(appname)/Layout $(pages_csv) 