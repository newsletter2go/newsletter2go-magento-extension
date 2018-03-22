version = $(subst .,_,${shell grep -oP '(?<=<version>).*?(?=</version>)' package.php})

outfile = Magento_nl2go_$(version).tgz

$(outfile):
	php package.php
	tar -P -cvzf  build.tgz app/* package.xml
	rm package.xml
	mv build.tgz $(outfile)

clean:
	rm -rf $(outfile)
