<?php
set_time_limit(20);
//require_once 'config.php';
require_once 'i-tatarajah.php';
require_once 'imgupload.class.php';

if(!isset($_GET['id'])):
	$img = new ImageUpload;
	$result['images'] = $img->listImage();
	if(!empty($result))
	{
		//echo '<pre>'; print_r($result); echo '</pre>';
		echo 'Your images can be viewed here:<br><br>';
		foreach ($result as $myTable => $row)
		{
			if ( count($row)==0 ) echo '';
			else
			{
				showTable($myTable,$row);
			}# if ( count($row)==0 )
		}# endforeach
	}
	else
	{
		echo 'Tiada data';
	}
else:
	$img = new ImageUpload;
	$img->deleteImage($_GET['id']);
endif;

#--------------------------------------------------------------------------------------------------
function showTable($tajukjadual,$row)
{
	echo "\n" . '<table border="1" class="excel" id="example">';
	echo "\n" . '<h3>'. $tajukjadual . '</h3>';
	$printed_headers = false; # mula bina jadual
	#-----------------------------------------------------------------
	for ($kira=0; $kira < count($row); $kira++)
	{
		if ( !$printed_headers ) # papar tajuk medan sekali sahaja:
		{
			echo "\n" . '<thead><tr><th>#</th>';
			foreach ( array_keys($row[$kira]) as $tajuk )
			{
				echo "\n" . '<th>' . $tajuk . '</th>';
			}
			echo "\n" . '</tr></thead>';
			$printed_headers = true;
		}
	# papar data $row ------------------------------------------------
	echo "\n" . '<tr><td align="center">'. ($kira+1) . '</td>';
		foreach ( $row[$kira] as $key=>$data )
		{
			gaya_url_1($key,$data);
		}
		echo '</tr>' . "\n";
	}#-----------------------------------------------------------------
	echo "\n" . '</table>';
	#
}
#--------------------------------------------------------------------------------------------------
function gaya_url_1($key,$data)
{
	$k0 = URL . 'image.php?id=' . $data;
	$k1 = URL . 'download.php?id=' . $data;
	$k2 = URL . 'i-papar.php?id=' . $data;

	if ($data == null):
		echo "\n<td>&nbsp;</td>";
	elseif($key == 'id'):
		?><td><?php
		pautanTD('_blank',$k0,null,$data,null);
		pautanTD('_blank',$k1,null,'Download',null);
		pautanTD('_blank',$k2,null,'Delete',null);
		?></td><?php
	else: 
		echo "\n<td>$data</td>";
	endif;
}
#--------------------------------------------------------------------------------------------------
function pautanTD($target, $href, $class, $data, $iconFA)
{
	$t = ($target == null) ? '':' target="' . $target . '"';
	$data = ($data == '0' or $data == null) ? '&nbsp;':$data;
	$iconFA = ($iconFA == null) ? '':$iconFA;
	?><a<?php echo $t ?> href="<?php echo $href ?>" class="<?php
	echo $class ?>"><?php echo $data ?></a>|<?php
}
#------------------------------------------------------------------------------------------
