<?php
require_once('../conf/auth.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Nutcracker: RGB Effects Builder</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="last-modified" content=" 24 Feb 2012 09:57:45 GMT"/>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE8"/>
<meta name="robots" content="index,follow"/>
<meta name="googlebot" content="noarchive"/>
<link rel="shortcut icon" href="barberpole.ico" type="image/x-icon"/> 
<meta name="description" content="RGB Sequence builder for Vixen, Light-O-Rama and Light Show Pro"/>
<meta name="keywords" content="DIY Light animation, Christmas lights, RGB Sequence builder, Vixen, Light-O-Rama or Light Show Pro"/>
<link href="../css/loginmodule.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h1>Welcome <?php echo $_SESSION['SESS_FIRST_NAME'];?></h1>
<?php $menu="effect-form"; require "../conf/menu.php"; ?>
<?php
//
//
require("read_file.php");
$username=$_SESSION['SESS_LOGIN'];
$member_id=$_SESSION['SESS_MEMBER_ID'];
echo "<h2>Nutcracker: RGB Effects Builder for user $username<br/>
On this page you build an animation of the spiral class and create an animated GIF</h2>"; 
echo "<pre>\n";
ini_get('max_execution_time'); 
set_time_limit(250);
ini_get('max_execution_time'); 
//show_array($_POST,"_POST");
//show_array($_SERVER,"_SERVER");
//show_array($_SESSION,"_SESSION");
echo "</pre>\n";
///*
/*
SESSION
Array
(
[SESS_MEMBER_ID] => 2
[SESS_FIRST_NAME] => sean
[SESS_LAST_NAME] => MEIGHAN
[SESS_LOGIN] => f
)
	*/ 
$array_to_save=$_POST;
$array_to_save['OBJECT_NAME']='spirals';
extract ($array_to_save);
$effect_name = strtoupper($effect_name);
$effect_name = rtrim($effect_name);
$username=str_replace("%20"," ",$username);
$effect_name=str_replace("%20"," ",$effect_name);
$array_to_save['effect_name']=$effect_name;
$array_to_save['username']=$username;
if(!isset($show_frame)) $show_frame='N';
$array_to_save['show_frame']=$show_frame;
$f_delay = $_POST['frame_delay'];
$f_delay = intval((5+$f_delay)/10)*10; // frame frame delay to nearest 10ms number_format
$array_to_save['frame_delay']=$f_delay;
extract ($array_to_save);
save_user_effect($array_to_save);
show_array($array_to_save,"Effect Settings");
$path="../targets/". $member_id;
list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;
$t_dat = $user_target . ".dat";
$arr=read_file($t_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
echo "<pre>arr=read_file($t_dat,$path);</pre>\n";
$member_id=get_member_id($username);
$path ="workspaces/" . $member_id;
$directory=$path;
if (file_exists($directory))
{
	} else {
	echo "The directory $directory does not exist, creating it";
	mkdir($directory, 0777);
}
$x_dat = $user_target . "+" . $effect_name . ".dat";
$base = $user_target . "+" . $effect_name;
spiral($arr,$path,$t_dat,$number_spirals,$number_rotations,$spiral_thickness,$base,
$color1,$color2,$color3,$color4,$color5,$color6,$rainbow_hue,$fade_3d,$speed,
$direction,$f_delay,$sparkles,$window_degrees,$script_start,$use_background,$background_color,$handiness,$username,$seq_duration,$show_frame,$effect_type); 
$target_info=get_info_target($username,$t_dat);
//show_array($target_info,'MODEL: ' . $t_dat);
$description ="Total Elapsed time for this effect:";
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
//if($description = 'Total Elapsed time for this effect:')
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
$filename_buff=make_buff($username,$member_id,$base,$f_delay,$seq_duration,$fade_in,$fade_out); 

function spiral($arr,$path,$t_dat,$numberSpirals,$number_rotations,$spiralThickness,$base,
$color1,$color2,$color3,$color4,$color5,$color6,$rainbow_hue,$fade_3d,$speed,
$direction,$f_delay,$sparkles,$window_degrees,$script_start,$use_background,$background_color,$handiness,$username,$seq_duration,$show_frame,$effect_type)
{
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	$strand_pixel=$arr[9];
	if($color3 == null or !isset($color3)) $color3="#FFFFFF";
	if($color4 == null or !isset($color4)) $color4="#FFFFFF";
	if($color5 == null or !isset($color5)) $color5="#FFFFFF";
	if($color6 == null or !isset($color6)) $color6="#FFFFFF";
	if($fade_3d == null or !isset($fade_3d)) $fade_3d="N";
	if($rainbow_hue == null or !isset($rainbow_hue)) $rainbow_hue="Y";
	if($speed == null or !isset($speed)) $speed=0.5;
	//
	$direction=strtolower($direction);
	$fade_3d=strtoupper($fade_3d);
	$rainbow_hue=strtoupper($rainbow_hue);
	
	show_elapsed_time($script_start,"Creating  Effect, spirals class:");
	if($maxStrand<1)$maxStrand=1;
	$pixelPerStrand=$maxPixel/$maxStrand;
	//if( $numberStrands<1)  $numberStrands=1;
	$deltaStrands= $maxStrand/ $numberSpirals;
	$line= 0;
	$rgb=255;
	$x_dat_base = $base . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
	$dat_file_array=array();
	$r=115;
	$g =115;
	$b = 120;
	$maxLoop = $maxStrand*$number_rotations;
	$deltaPixel = $maxPixel/$maxLoop;
	$S=$V=1;
	$deltaH = (RED - ORANGE)/$maxLoop;
	$H=RED;
	$lowRange1 = $minStrand;
	$lowRange2 = $maxStrand/4;
	$highRange1=$maxStrand -  $maxStrand/4;
	$highRange2=$maxStrand;
	$seq_number=0;
	$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
	//show_elapsed_time($script_start,"Start     delete_effects:");
	//delete_effects($username,$t_dat);
	//show_elapsed_time($script_start,"Fini      delete_effects:");
	//flush();
	//
	$f=1;
	$amperage=array();
	
	//
	//
	
	for ($f=1;$f<=$maxStrand;$f++)
	{
		$x_dat = $base . "_d_". $f . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
		$dat_file[$f] = $path . "/" .  $x_dat;
		$dat_file_array[]=$dat_file[$f];
		$fh_dat [$f]= fopen($dat_file[$f], 'w') or die("can't open file");
		fwrite($fh_dat[$f],"#    " . $dat_file[$f] . "\n");
		for( $ns= $minStrand; $ns<= $numberSpirals; $ns++)
		{
			$line++;
			$p_to_add=1;
			if($effect_type=='v' or $effect_type=='V') $p_to_add=0;
			if(strtoupper($handiness)=="R")
			{
				$strand_base=intval( ($ns-1)*$deltaStrands-$p_to_add);
			}
			else
			{
				$strand_base=intval( ($ns-1)*$deltaStrands+$p_to_add);
			}
			//	echo "<pre>,loop=$l, ns=$ns, strand_base=$strand_base</pre>\n";
			for($thick=1;$thick<=$spiralThickness;$thick++)
			{
				if(strtoupper($handiness)=="R")
				{
					$strand = ($strand_base%$maxStrand)-$thick;
				}
				else
				{
					$strand = ($strand_base%$maxStrand)+$thick;
				}
				if ($strand < $minStrand) $strand += $maxStrand;
				if ($strand > $maxStrand) $strand -= $maxStrand;
				if($strand<1) $strand=1;
				//
				//
				//
				//
				for($p=1;$p<=$maxPixel;$p++)
				{
					if($rainbow_hue<>'N')
					{
						$color_HSV=color_picker($p,$maxPixel,$numberSpirals,$color1,$color2);
						$H=$color_HSV['H'];
						$S=$color_HSV['S'];
						$V=$color_HSV['V'];
						//		echo "<pre>$strand,$p start,end=$start_color,$end_color  HSV=$H,$S,$V</pre>\n";
					}
					else
					{
						$mod = $ns%6;
						// we want the last color to be next in line from color palete if we are dealing with
						// number of spirals <= 6
						//if($ns==$numberSpirals and $mod==0 and $numberSpirals<6) $mod=$numberSpirals;
						if($mod==0) $mod=6;
						switch ($mod)
						{
							case 1:
							$rgb_val=hexdec($color1);
							break;
							case 2:
							$rgb_val=hexdec($color2);
							break;
							case 3:
							$rgb_val=hexdec($color3);
							break;
							case 4:
							$rgb_val=hexdec($color4);
							break;
							case 5:
							$rgb_val=hexdec($color5);
							break;
							case 0:
							$rgb_val=hexdec($color6);
							break;
						}
						$HSL= RGBVAL_TO_HSV($rgb_val);
						//RGBVAL_TO_HSV($rgb_val)
							$H=$HSL['H']; 
						$S=$HSL['S']; 
						$V=$HSL['V'];
						$hex=dechex($rgb_val);
						//	echo "<pre> ns=$ns, mod=$mod, rgbval=$rgb_val($hex), HSV=$H,$S,$V.  $mod = ($ns%$numberSpirals)%6;</pre>\n";
					}
					if($fade_3d=='Y')
					{
						if($direction=='ccw')
						{
							$mod_ratio=$thick/$spiralThickness;
						}
						else
						{
							$mod_ratio=($spiralThickness-($thick-1))/$spiralThickness;
						}
						$V=$V*$mod_ratio;
					}
					$rgb_val=HSV_TO_RGB ($H, $S, $V);
					$f1_rgb_val=$rgb_val;
					$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					$p_offset = intval($p*$number_rotations);
					if($direction=="ccw")
					{
						$new_s = $strand+intval(($f-1)*$speed)+$p_offset; // CCW
					}
					else
					{
						$new_s = $strand-intval(($f-1)*$speed)-$p_offset; // CW
					}
					//	echo "<pre> f,s,p=$f,$strand,$p  ns,thick=$ns,$thick.  new_s=$new_s</pre>\n";
					if($new_s>$maxStrand) $new_s = $new_s%$maxStrand;
					if($new_s<$minStrand) $new_s = $new_s%$maxStrand;
					if($new_s==0) $new_s=$maxStrand;
					if($new_s<0) $new_s+=$maxStrand;
					$xyz=$tree_xyz[$strand][$p]; // get x,y,z location from the model.
					//echo "<pre> f,s,p=$f,$strand,$p  ns,thick=$ns,$thick</pre>\n";
					$tree_rgb[$strand][$p]=$rgb_val;
					$seq_number++;
					if($rgb_val==0 and $use_background=='Y')
					{
						$rgb_val=hexdec($background_color);
						echo "<pre>$rgb_val=hexdec($background_color);</pre>\n";
					}
					$xyz=$tree_xyz[$new_s][$p];
					$seq_number++;
					$rgb_val=sparkles($sparkles,$f1_rgb_val); // if sparkles>0, then rgb_val will be changed.
					$tree_rgb[$strand][$p]=$rgb_val;
					if(in_array($new_s,$window_array)) // Is this strand in our window?, If yes, then we output lines to the dat file
					{
						if($rgb_val==0 and $use_background=='Y')
						{
							$rgb_val=hexdec($background_color);
						}
						//$amperage[$f][$new_s] += $V*0.060; // assume 29ma for pixels tobe full on
						$string=$user_pixel=0;
						fwrite($fh_dat[$f],sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d %d %d %d %d\n",$strand,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$string, $user_pixel,$strand_pixel[$new_s][$p][0],$strand_pixel[$new_s][$p][1],$f,$seq_number));
					}
				}
			}
		}
	}
	for ($f=1;$f<=$maxStrand;$f++)
	{
		fclose($fh_dat[$f]);
	}
	show_elapsed_time($script_start,"Finished  Effect, spirals class:");
	make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$script_start,$amperage,$seq_duration,$show_frame);
	echo "<pre>make_gp($arr,$path,$x_dat_base,$t_dat,$dat_file_array,$min_max,$username,$f_delay,$script_start,$amperage,$seq_duration,$show_frame)</pre>\n";
	?>
	<br/>
	<br/>
	<ul>
	<li> <a href="../index.html">Home</a> 
	<li><a href="../login/member-index.php">Target Generator</a> 
	<li> <a href="effect-form.php">Effects Generator</a> 
	<li> <a href="../login/logout.php">Logout</a>
	</ul>
	<br/>
	</body>
	</html>
	<?php 
}

function delete_effects($username,$model_name)
{
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$model_base_name = basename($model_name,".dat");
	$query = "delete from effects where username='$username' and object_name='$model_base_name'";
	$result=mysql_query($query) or die ("Error on $query");
	mysql_close();
}

function insert_effects($username,$model_name,$strand,$pixel,$x,$y,$z,$rgb_val,$f,$seq_number)
{
	//echo "<pre> insert_effects($username,$model_name,$strand,$pixel,$x,$y,$z,$rgb_val)\n";
	//Include database connection details	
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link)
	{
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db)
	{
		die("Unable to select database");
	}
	$model_base_name = basename($model_name,".dat");
	$x=$x+0.0;
	$y=$y+0.0;
	$z=$z+0.0;
	$query="insert into effects (seq_number,username,object_name,strand,pixel,x,y,z,rgb_val,frame) values
	($seq_number,'$username','$model_base_name',$strand,$pixel,$x,$y,$z,$rgb_val,$f)";
	//echo "<pre>insert_effects: query=$query</pre>\n";
	mysql_query($query) or die ("Error on $query");
	mysql_close();
}
