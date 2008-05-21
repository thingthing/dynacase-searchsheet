# ============================================
# $Id: Makefile,v 1.3 2008/05/21 09:51:11 eric Exp $
#
# ============================================


include $(utildir)/PubRule

pages_csv = $(patsubst %.csv,$(pubdir)/$(appname)/Layout/%.csv,$(wildcard *csv))


$(pubdir)/$(appname)/Layout:
	mkdir $@

$(pubdir)/$(appname)/Layout/%.csv: %.csv $(pubdir)/$(appname)/Layout
	cp $< $@

publish: $(pubdir)/$(appname)/Layout $(pages_csv) 