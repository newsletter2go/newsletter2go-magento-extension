version = 0_0_00

version_dots= $(subst _,.,$(version))
outfile = NL2go_Sync-$(version_dots).tgz

$(outfile):
	tar  -P -cvf  build.tgz app/* package.xml
	mv build.tgz $(outfile)

clean:
	rm -rf $(outfile)
