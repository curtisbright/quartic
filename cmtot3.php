<?php

// Computer Modern Metafont to PostScript Type 3 Converter
// Curtis Bright, May 19, 2011
//
// Usage: php cmtot3.php [-t] [external font names]
// Use -t to write the characters on a test page
//
// Example: php cmtot3.php cmr10 cmbx10 cmsl10

if($argc==1)
	exit("Computer Modern Metafont to PostScript Type 3 Converter\nUsage: php cmtot3.php [-t] [external font names]\nUse -t to write the characters on a test page\n");

unset($argv[0]);
$testpage = 0;
if($argv[1]=="-t")
{	$testpage = 1;
	unset($argv[1]);
}
foreach($argv as $name)
{	if(substr($name, -3)==".mf")
		$name = substr($name, 0, -3);

	exec("mpost --interaction=batchmode -mem=mfplain '\mode:=lowres; input $name;'");
	if(file_exists("$name.tfm"))
	{	exec("tftopl -charcode-format=octal $name.tfm", $pldata);
		foreach($pldata as $line)
			if(strpos($line, "DESIGNSIZE")!==false)
			{	$designsize = substr(end(explode(" ", $line)), 0, -1);
				break;
			}

		exec("mpost --interaction=batchmode -mem=mfplain '\mode:=lowres; mag:=1000/$designsize; input $name;'");
		exec("rm $name.log");
	}
	else
	{	$family = $name;
		$opticalsize = "";
		while(is_numeric(substr($family, -1)))
		{	$opticalsize = substr($family, -1) . $opticalsize;
			$family = substr($family, 0, -1);
		}

		if($opticalsize==11)
			$designsize = 10.95;
		else if($opticalsize==14)
			$designsize = 14.4;
		else if($opticalsize==17)
			$designsize = 17.28;
		else if($opticalsize==20)
			$designsize = 20.74;
		else if($opticalsize==25)
			$designsize = 24.88;
		else if($opticalsize==30)
			$designsize = 29.86;
		else if($opticalsize==36)
			$designsize = 35.83;
		else if(strlen($opticalsize)>=4)
			$designsize = $opticalsize/100;
		else
			$designsize = $opticalsize;

		exec("mpost --interaction=batchmode -mem=mfplain '\mode:=lowres; design_size:=$designsize; mag:=1000/design_size; input b-$family;'");
		exec("tftopl -charcode-format=octal b-$family.tfm", $pldata);
		exec("mv b-$family.tfm $name.tfm");
		exec("rm b-$family.log");
		exec("rm $name.log");

		for($i=0; $i<128; $i++)
			if(file_exists("b-$family.$i"))
				exec("mv b-$family.$i $name.$i");
	}

	if(file_exists("$name.tfm"))
	{	$fp = fopen("$name.t3", "w");

		if($testpage)
			fwrite($fp, "%!PS\n");
		fwrite($fp, "8 dict dup begin\n");
		fwrite($fp, "/FontType 3 def\n");
		fwrite($fp, "/FontMatrix [ 0.001 0 0 0.001 0 0 ] def\n");
		fwrite($fp, "/FontBBox [ 0 0 0 0 ] def\n");
		fwrite($fp, "/Encoding 256 array def\n");
		fwrite($fp, "0 1 255 {Encoding exch /.notdef put} for\n");

		$count = 1;
		for($i=0; $i<128; $i++)
			if(file_exists("$name.$i"))
			{	fwrite($fp, "Encoding $i /ch$i put\n");
				$count++;
			}

		fwrite($fp, "/CharProcs $count dict def\n");
		fwrite($fp, "CharProcs /.notdef { } put\n");

		for($i=0; $i<128; $i++)
			if(file_exists("$name.$i"))
			{	fwrite($fp, "CharProcs /ch$i {\n");

				$data = file("$name.$i");
				exec("rm $name.$i");

				$w = 0;
				for($j=0; $j<count($pldata); $j++)
					if(strpos($pldata[$j], "CHARACTER O ".decoct($i))!==false)
					{	$w = 1000*substr(end(explode(" ", $pldata[$j+1])), 0, -1);
						break;
					}
				fwrite($fp, "$w 0 setcharwidth\n");

				for($j=9; $j<count($data)-2; $j++)
					fwrite($fp, $data[$j]);

				fwrite($fp, "} put\n");
			}

		fwrite($fp, "/BuildGlyph {exch begin CharProcs exch get exec end} bind def\n");
		fwrite($fp, "/BuildChar {1 index /Encoding get exch get 1 index /BuildGlyph get exec} bind def\n");
		fwrite($fp, "end\n");
		fwrite($fp, "/$name exch definefont pop\n");
		if($testpage)
		{	fwrite($fp, "/$name findfont $designsize scalefont setfont\n");
			for($i=0; $i<128; $i++)
			{	if($i%16==0)
					fwrite($fp, "80 " . (700-$designsize*$i/16) . " moveto (");
				fwrite($fp, "\\".decoct($i));
				if($i%16==15)
					fwrite($fp, ") show\n");
			}
		}
		fclose($fp);

		exec("echo '$name $name <$name.t3' >> cm-t3.map");
	}
	unset($pldata);
}

?>
