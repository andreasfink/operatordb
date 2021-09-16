<?php

/* connect to the DB */

include_once("db.php");

function execute_sql($handler,$sql,$xdebug=0)
{
	if($xdebug==1)
	{
		printf("SQL: %s\n",$sql);
	}
	$result = $handler->query($sql);
	if($result==false)
	{
		echo "SQL Error #1: ".$handler->error."\n";
		exit;
	}
	if($xdebug==1)
	{
		printf("\n");
	}
	return $result;
}

function get_rows_sql($handler,$sql,$xdebug=0)
{
	$arr = array();
	$r = execute_sql($handler,$sql,$xdebug);
	for($i=0;$i<$r->num_rows;$i++)
	{
		$arr[$i] = $r->fetch_array(MYSQLI_ASSOC);
	}
	return $arr;
}

function add_node(&$root,$prefix,$digit,$cc2,$cc3,$country,$operator_code,$mcc,$mnc,$name)
{
	if(strlen($digit)> 0)
	{
		$current_digit = substr($digit,0,1);
		$remaining_digits = substr($digit,1);

		if(!isset($root[$current_digit]))
		{
			$prefix = $prefix . $current_digit;
			$root[$current_digit] = array('prefix'=>$prefix);
		}
		$root = &$root[$current_digit];
		add_node($root,$prefix,$remaining_digits,$cc2,$cc3,$country,$operator_code,$mcc,$mnc,$name);
	}
	else
	{
		$root['operator_code']=$operator_code;
		$root['cc2']=$cc2;
		$root['cc3']=$cc3;
		$root['country']=$country;
		$root['mcc']=$mcc;
		$root['mnc']=$mnc;
		$n = str_replace("\"","'",$name);
		$n = str_replace("“","'",$n);
		$n = str_replace("”","'",$n);
		$root['name']=str_replace("\"","'",$n);
	}
}

function print_node($root,$ident,$tab,$index,$format)
{
	$has_sub = 0;
	
	if($format=="c")
	{
		$varprefix = "x_";
	}
	else if($format=="php")
	{
		$varprefix = "$";
	}
	
	if(isset($root['operator_code']))
	{
		echo $ident.$varprefix."operator_code = \"".$root['operator_code']."\";\n";
	}
	if(isset($root['cc2']))
	{
		echo $ident.$varprefix."cc2 = \"".$root['cc2']."\";\n";
	}
	if(isset($root['cc3']))
	{
		echo $ident.$varprefix."cc3 = \"".$root['cc3']."\";\n";
	}
	if(isset($root['country']))
	{
		echo $ident.$varprefix."country = \"".$root['country']."\";\n";
	}
	if(isset($root['mcc']))
	{
		echo $ident.$varprefix."mcc = \"".$root['mcc']."\";\n";
	}
	if(isset($root['mnc']))
	{
		echo $ident.$varprefix."mnc = \"".$root['mnc']."\";\n";
	}

	if(isset($root['name']))
	{
		echo $ident.$varprefix."mnc = \"".$root['name']."\";\n";
	}

	for($i=0;$i<10;$i++)
	{
		if(isset($root[$i]))
		{
			$has_sub = 1;
			break;
		}
	}
	if($has_sub==1)
	{
		if($format=="c")
		{
			echo $ident."switch(imsi[".$index."])\n";
		}
		else if($format=="php")
		{
			echo $ident."switch(\$imsi[".$index."])\n";
		}
		echo $ident."{\n";
		
		for($i=0;$i<10;$i++)
		{
			if(isset($root[$i]))
			{
				echo $ident.$tab."case '".$i."':\n";
				echo $ident.$tab."{\n";
				print_node($root[$i],$ident.$tab.$tab,$tab,$index+1,$format);
				echo $ident.$tab.$tab."break;\n";
				echo $ident.$tab."}\n";
			}
		}
		echo $ident."}\n";
	}
}

$root = array('prefix'=>'');

$mcc_recs = get_rows_sql($mysqli1,"select * from country_mcc");
$n = sizeof($mcc_recs);
for($i=0;$i<$n;$i++)
{
	$r = $mcc_recs{$i};
	$country = $r['country'];
	$organisation = $country;
	$network = $country;
	$abbreviated_name = $country;
	$mcc = $r['mcc'];
	$mnc = "XX";
	$sim = "";
	$last_update = "";
	$prefix = $mcc;
	add_node($root,"",$prefix,$operator_code,$cc2,$cc3,$country,$mcc,$mnc,$name);
}

$op_recs = get_rows_sql($mysqli1,"select * from opdb");

$n = sizeof($op_recs);
for($i=0;$i<$n;$i++)
{
	$r = $op_recs{$i};

	$operator_code = $r['operator_code'];
	$cc2 = $r['cc2'];
	$cc3 = $r['cc3'];
	$country = $r['country'];
	$mcc = $r['mcc'];
	$mnc = $r['mnc'];
	$name = $r['name'];

	$prefix = $mcc . $mnc;
	add_node($root,"",$prefix,$operator_code,$cc2,$cc3,$country,$mcc,$mnc,$name);
}

$output_format = "c";
if($argc>1)
{
	$output_format = $argv[1];
}

if($output_format=="c")
{
	echo "//\n";
	echo "//  operatordb.c\n";
	echo "//  operatordb\n";
	echo "//\n";
	echo "//  Created by ".get_current_user()." on ".gmdate('Y-m-d H:i:s e').".\n";
	echo "//\n";
	echo "\n";
	echo "#include <string.h>\n";
	echo "\n";
	echo "void get_operator_from_imsi2(const char *imsi,\n";
	echo "                            const char **operator_code,\n";
	echo "                            const char **cc2,\n"; 
	echo "                            const char **cc3,\n"; 
	echo "                            const char **country,\n";
	echo "                            const char **mcc,\n";
	echo "                            const char **mnc,\n";
	echo "                            const char **name)\n";
	echo "{\n";
	echo "    const char *x_operator_code = \"\";\n";
	echo "    const char *x_cc2 = \"\";\n";
	echo "    const char *x_cc3 = \"\";\n";
	echo "    const char *x_country = \"\";\n";
	echo "    const char *x_mcc = \"\";\n";
	echo "    const char *x_mnc = \"\";\n";
	echo "    const char *x_name = \"\";\n";
	echo "    if (imsi == 0)\n";
	echo "    {\n";
	echo "        return;\n";
	echo "    }\n";
	echo "    if (strlen(imsi) == 0)\n";
	echo "    {\n";
	echo "        return;\n";
	echo "    }\n";
	echo "\n";

	$tab = "    ";
	$ident = $tab;
	print_node($root,$ident,$tab,0,"c");
	echo "    if(*operator_code)\n";
	echo "    {\n";
	echo "        *operator_code = x_operator_code;\n";
	echo "    }\n";
	echo "    if(*cc2)\n";
	echo "    {\n";
	echo "        *cc2 = x_cc2;\n";
	echo "    }\n";
	echo "    if(*operator_code)\n";
	echo "    {\n";
	echo "        *cc3 = x_cc3;\n";
	echo "    }\n";
	echo "    if(*country)\n";
	echo "    {\n";
	echo "        *country = x_country;\n";
	echo "    }\n";
	echo "    if(*mcc)\n";
	echo "    {\n";
	echo "        *mcc = x_mcc;\n";
	echo "    }\n";
	echo "    if(*mnc)\n";
	echo "    {\n";
	echo "        *mnc = x_mnc;\n";
	echo "    }\n";
	echo "    if(*name)\n";
	echo "    {\n";
	echo "        *name = x_name;\n";
	echo "    }\n";
	echo "\n";
	echo "}\n";
}

else if ($output_format=="php")
{
	echo "<?php\n";
	echo "//  operatordb.php\n";
	echo "//  operatordb\n";
	echo "//\n";
	echo "//  Created by ".get_current_user()." on ".gmdate('Y-m-d H:i:s e').".\n";
	echo "//\n";
	echo "\n";
	echo "\n";
	echo "function get_operator_from_imsi2(\$imsi)\n";
	echo "{\n";
	echo "    \$operator_code = \"\";\n";
	echo "    \$cc2 = \"\";\n";
	echo "    \$cc3 = \"\";\n";
	echo "    \$country = \"\";\n";
	echo "    \$mcc = \"\";\n";
	echo "    \$mnc = \"\";\n";
	echo "    \$name = \"\";\n";
	
	echo "    \$a = array();\n";
	echo "    \$a['operator_code']= \$operator_code;\n";
	echo "    \$a['cc2']= \$cc2;\n";
	echo "    \$a['cc3']= \$cc3;\n";
	echo "    \$a['country']= \$name;\n";
	echo "    \$a['mcc']= \$mcc;\n";
	echo "    \$a['mnc']= \$mnc;\n";
	echo "    \$a['name']= \$name;\n";
	echo "    if ($imsi == NULL) || (strlen($imsi==0))\n";
	echo "    {\n";
	echo "        return $a;\n";
	echo "    }\n";
	echo "\n";

	$tab = "    ";
	$ident = $tab;
	print_node($root,$ident,$tab,0,"php");
	echo "    \$a['operator_code']= \$operator_code;\n";
	echo "    \$a['cc2']= \$cc2;\n";
	echo "    \$a['cc3']= \$cc3;\n";
	echo "    \$a['country']= \$name;\n";
	echo "    \$a['mcc']= \$mcc;\n";
	echo "    \$a['mnc']= \$mnc;\n";
	echo "    \$a['name']= \$name;\n";
	echo "    return \$a;\n";
	echo "}\n";
}
else
{
	fprintf(STDERR,"Unknown output format '$output_format'. Choose 'c' or 'php'\n");
}
