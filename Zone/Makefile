# ============================================
# $Id: Makefile,v 1.4 2008/05/26 13:06:03 eric Exp $
#
# ============================================


include $(utildir)/PubRule
DISTFILES += $(wildcard *csv)

pages_csv = $(patsubst %.csv,$(pubdir)/$(appname)/Layout/%.csv,$(wildcard *csv))

$(pubdir)/$(appname)/Layout:
	mkdir $@

$(pubdir)/$(appname)/Layout/%.csv: %.csv $(pubdir)/$(appname)/Layout
	cp $< $@

publish: $(pubdir)/$(appname)/Layout $(pages_csv) 