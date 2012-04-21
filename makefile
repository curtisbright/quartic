SHELL = /bin/bash
.SUFFIXES: .t3
quartic:
	pdftex quartic.tex
cubic:
	pdftex cubic.tex
quadratic:
	pdftex quadratic.tex
linear:
	pdftex linear.tex
formulae:
	pdftex formulae.tex
all: quartic cubic quadratic linear formulae
quartic-type3: cmbx36.t3 cmex10.t3 cmmi10.t3 cmmi17.t3 cmr5.t3 cmr7.t3 cmr10.t3 cmr1190.t3 cmr17.t3 cmsy7.t3 cmsy10.t3 cmsy12.t3 cmsy17.t3 cmti12.t3
	tex quartic.tex
	dvips -D 10000 -T 1189mm,420.5mm -O -1in,-1in -u cm-t3.map quartic.dvi
	ps2pdf quartic.ps quartic-type3.pdf
cubic-type3: cmbx36.t3 cmex10.t3 cmmi7.t3 cmmi10.t3 cmmi12.t3 cmmi17.t3 cmr5.t3 cmr7.t3 cmr10.t3 cmr1190.t3 cmr12.t3 cmr17.t3 cmsy7.t3 cmsy10.t3 cmsy12.t3 cmsy17.t3 cmti12.t3
	tex cubic.tex
	dvips -D 10000 -T 297mm,210mm -O -1in,-1in -u cm-t3.map cubic.dvi
	ps2pdf cubic.ps cubic-type3.pdf
quadratic-type3: cmbx36.t3 cmmi10.t3 cmmi17.t3 cmr7.t3 cmr10.t3 cmr1190.t3 cmr17.t3 cmsy10.t3 cmsy17.t3
	tex quadratic.tex
	dvips -D 10000 -T 297mm,210mm -O -1in,-1in -u cm-t3.map quadratic.dvi
	ps2pdf quadratic.ps quadratic-type3.pdf
linear-type3: cmbx36.t3 cmmi10.t3 cmmi17.t3 cmr7.t3 cmr10.t3 cmr17.t3 cmsy10.t3 cmsy17.t3
	tex linear.tex
	dvips -D 10000 -T 297mm,210mm -O -1in,-1in -u cm-t3.map linear.dvi
	ps2pdf linear.ps linear-type3.pdf
formulae-type3: cmbx17.t3 cmbx36.t3 cmex10.t3 cmmi7.t3 cmmi10.t3 cmmi12.t3 cmr5.t3 cmr7.t3 cmr0840.t3 cmr10.t3 cmr12.t3 cmsy7.t3 cmsy10.t3 cmsy12.t3 cmti10.t3
	tex formulae.tex
	dvips -D 10000 -T 1189mm,841mm -O -1in,-1in -u cm-t3.map formulae.dvi
	ps2pdf formulae.ps formulae-type3.pdf
all-type3: quartic-type3 cubic-type3 quadratic-type3 linear-type3 formulae-type3
%.t3:
	php cmtot3.php $*
clean:
	rm -f *.log *.dvi *.ps *.pdf *.tfm *.t3 cm-t3.map
