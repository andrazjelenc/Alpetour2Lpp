<?php

function readData($pathAlpetour, $pathLpp, $voznjaLpp)
{
	global $alpetour;
	global $lpp;
	
	$content = file_get_contents($pathAlpetour);
	$content = explode("\r\n",$content);
	unset($content[0]);

	foreach($content as $line)
	{
		$data = preg_split('/\s+/', $line);
		$alpetour[] = array($data[0],$data[1]);
	}

	$content = file_get_contents($pathLpp);
	$content = explode("\r\n",$content);

	foreach($content as $index => $minute)
	{
		if(empty($minute))
		{
			continue;
		}
		$ura = $index + 1;
		$minute = explode(' ',$minute);
		foreach($minute as $element)
		{
			if(!empty($element))
			{
				$start = $ura.':'.$element;
				if(strlen($start) == 4)
				{
					$start = '0'.$start;
				}
				
				//izracunamo stop
				$stopMinute = $element + $voznjaLpp;
				$stopUra = $ura + floor($stopMinute / 60);
				$stopMinute = $stopMinute % 60;
				
				if(strlen($stopMinute) == 1)
				{
					$stopMinute = '0'.$stopMinute;
				}
				
				$stop = $stopUra.':'.$stopMinute;
				
				if(strlen($stop) == 4)
				{
					$stop = '0'.$stop;
				}
				$lpp[] = array($start, $stop);
			}
		}
	}
}

function calculate($varnostneMinuteNaPrestopu, $deadLine )
{
	global $alpetour;
	global $lpp;
	
	$result = '';
	foreach($alpetour as $prevoz1)
	{
		//s katerimi alpetour busi sploh ujamemo deadline
		if(strtotime($prevoz1[1].':00') >= strtotime($deadLine.':00'))
		{
			continue;
		}
		
		
		foreach($lpp as $prevoz2)
		{
			//s katerimi lppji sploh ujamemo deadline
			if(strtotime($prevoz2[1].':00') >= strtotime($deadLine.':00'))
			{
				continue;
			}
			
			//ali je dovolj velika razlika med alpteourjem in lppjem
			if(strtotime($prevoz2[0].':00') - strtotime($prevoz1[1].':00') < $varnostneMinuteNaPrestopu*60)
			{
				continue;
			}
			
			$result =  $prevoz1[0].'->'.$prevoz1[1].'   '.$prevoz2[0].'->'.$prevoz2[1]."\r\n";			

		
		}
		
	}
	echo $result;
	
}

//{04:35, 05:05}, {05:10, 05:40}...
//global
$alpetour = array();
$lpp = array();


$pathAlpetour = 'alpetour.txt';
$pathLpp = 'lpp2.txt';


$voznjaLpp = 12; //lpp time to get from lpp.txt to destination
$varnostneMinuteNaPrestopu = 4; // difference between alpetour arival time and lpp take off :)
$deadLine = '11:09'; //when you need to be at lpp destination

readData($pathAlpetour, $pathLpp, $voznjaLpp);
calculate($varnostneMinuteNaPrestopu, $deadLine);
?>
