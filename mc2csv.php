<?php
echo "\r\n".'MC (message compiler) files to CSV (comma-separated values) converter.'."\r\n".'This utility is used to convert events message compiler files to CSV format, this is used to present some programs event as a table, e.g. for documentation.'."\r\n\r\n";
if ($argc!=3) die('No filename given'."\r\n".'Usage: '."\r\n".'"php '.$argv[0].' input.mc output.csv"'."\r\n");
$output=array();
if (file_exists($argv[1])){
	echo 'Input file: '.$argv[1]."\r\n";
	echo 'Output file: '.$argv[2]."\r\n\r\n";
	$input=file_get_contents($argv[1]);
	preg_match_all('#MessageId\s*=.*\.\r?\n\r?\n#Uis',$input,$m);
	if (!isset($m[1])){
		preg_match_all('#MessageId\s*=.*\.\r?\n\r?\n#Uis',iconv('UCS-2LE', 'UTF-8', $input),$m);
	}
	foreach ($m[0] as $one){
		$oneout=array();
		preg_match('#MessageId\s*=\s*(.*)\r?\n#Uis',$one,$m);
		if (!isset($m[1])) continue;
		$oneout['id']=$m[1];
		preg_match('#Severity\s*=\s*(.*)\r?\n#Uis',$one,$m);
		if (isset($m[1])) $oneout['severity']=$m[1];
		else $oneout['severity']='';
		preg_match('#Facility\s*=\s*(.*)\r?\n#Uis',$one,$m);
		if (isset($m[1])) $oneout['facility']=$m[1];
		else $oneout['facility']='';
		preg_match_all('#Language\s*=\s*(.*)\r?\n(.*)\.\r?\n#Uis',$one,$m);
		if (!isset($m[1])) continue;
		foreach ($m[0] as $i=>$v){
			$oneout['text'][$m[1][$i]]=$m[2][$i];		
		}
		$output[]=$oneout;
	}
	$out='';
	foreach ($output as $one){
		$out.='"'.$one['id'].'";"'.$one['severity'].'";"'.$one['facility'].'"';
		foreach ($one['text'] as $txt){
			$out.=';"'.trim(@iconv('utf-8','cp1251',$txt)).'"';
		}
		$out.="\r\n";
	}
	file_put_contents($argv[2],$out);
	echo 'Job done. '.count($output).' events converted.'."\r\n";
}else{
	die('Input file not found.');
}
