<?php
$monthNames = Array("January", "February", "March", "April", "May", "June", "July",
"August", "September", "October", "November", "December");

?>
<style>
	td{width:14.286% !important}
</style>
<table width="100%" border="1" cellpadding="2" cellspacing="2">
<tr align="left">
<th colspan="7"><strong><?php echo $monthNames[$cMonth-1].' '.$cYear; ?></strong></th>
</tr>
<tr>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Sun</strong></td>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Mon</strong></td>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Tue</strong></td>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Wed</strong></td>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Thu</strong></td>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Fri</strong></td>
<td align="center" bgcolor="#999999" style="color:#FFFFFF"><strong>Sat</strong></td>
</tr>
<?php

$timestamp = mktime(0,0,0,$cMonth,1,$cYear);
$maxday = date("t",$timestamp);
$thismonth = getdate ($timestamp);
$startday = $thismonth['wday'];
$cols = 0;
for ($i=0; $i<($maxday+$startday); $i++) {
    if(($i % 7) == 0 ) echo "<tr>\n";
    if($i < $startday) echo "<td>&nbsp;</td>\n";
    else{
		$title = '';
		if(isset($events[$i - $startday + 1]) && is_array($events[$i - $startday + 1]))
		{
			foreach($events[$i - $startday + 1] as $event)
			{
				if(date('n',$event->start_date_timestamp)!== $cMonth)
					continue;
				$title .='<b>'.date('h:i A',$event->start_date_timestamp).' to '.date('h:i A',$event->end_date_timestamp).'</b>  PT '.
							($event->end_date_timestamp - $event->start_date_timestamp)/60 .'m: '.$event->patient->getFullName().'<br />';
			}
		}
		echo '<td align="left" valign="middle"><b>'. ($i - $startday + 1) . '</b><br />'.$title.'</td>'."\n";
	}
	$cols++;
    if(($i % 7) == 6 )
	{
		echo "</tr>\n";
		$cols=0;
	}
}
if($cols<7 && $cols!==0)
{
	echo str_repeat("<td>&nbsp;</td>\n",7-$cols),'</tr>';
	
}
?>
</table>
