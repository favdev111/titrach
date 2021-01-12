<?php extract($data);
$agency = Agency::model()->findByPk($bill_agency);

$provider = Provider::model()->findByPk($bill_provider);
if($provider)
	$provider_name = $provider->getFullName();
	
$student = Patient::model()->findByPk($bill_patient);

$base_url = Yii::app()->getBaseUrl('true');
PDF::$base_url = $base_url;

//Rates
$rates = array(
	''=>0,
	'0'=>0,
	'1'=>41.98,
	'2'=>62.97,
	'3'=>83.96,
	'4'=>94.45,
	'5'=>104.95
);

?>
<div style="font-size:11pt">
<table border="0" width="100%">
	<tr>
		<td>
			<img src="<?php echo $base_url?>/img/templates/setss_logo.jpg" width="300"/>
		</td>
		<td align="right" style="text-align: right;">
			<span style="color: #777; font-size:10pt;">Version 9/4/12</span>
		</td>
	</tr>
</table>
<h3 style="text-align: center;">SETSS INVOICE</h3>

<table width="100%" border="0" cellpadding="0" cellspasing="0">
	<tr>
		<td width="25%"><b>Invoice #</b></td>
		<td width="25%">&nbsp;</td>
		<td width="25%"><b>Invoice Date</b></td>
		<td width="25%"><?php echo $bill_date?></td>
	</tr>
	<tr>
		<td><b>Agency/Provider Name</b></td>
		<td colspan="3" align="left" class="text-left"><?php echo $agency->name ?> / <?php echo $provider_name; ?></td>
	</tr>
	<tr>
		<td><b>Agency EIN /Provider SSN</b></td>
		<td colspan="3" align="left" class="text-left"><?php echo $agency->tax_id ?> / <?php echo $provider->ss_id; ?></td>
	</tr>
	<tr>
		<td><b>Agency /Provider Address</b></td>
		<td colspan="3" align="left" class="text-left"><?php echo $agency->getFullAddress(); ?> / <?php echo $provider->getFullAddress(); ?></td>
	</tr>
	<tr>
		<td><b>Agency/Provider Phone Number</b></td>
		<td><?php echo $agency->phone,' / ',$provider->phone;?></td>
		<td><b>SETSS Direct Service Provider</b></td>
		<td><?php echo $provider_name; ?></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td><b>Student Name</b></td>
		<td colspan="3" align="left" class="text-left"><?php echo $student->getFullName(); ?></td>
	</tr>
	<tr>
		<td><b>Student OSIS #</b></td>
		<td colspan="3" align="left" class="text-left"><?php echo $student->student_id; ?> </td>
	</tr>
	<tr>
		<td><b>Student Address</b></td>
		<td colspan="3" align="left" class="text-left"><?php echo $student->getFullAddress(); ?></td>
	</tr>
	<tr>
		<td><b>Site (where services were rendered)</b></td>
		<td colspan="3"><?php echo implode(', ',$locations);?></td>
	</tr>
</table>
<p>&nbsp;</p>
<div style="font-size:9pt;">
<table width="100%" border="1">
	<tr>
		<td colspan="6" align="center" style="text-align: center; background-color: #CCC;font-size:14pt;"><b>Service</b></td>
		<td colspan="6" align="center" style="text-align: center; background-color: #CCC;font-size:14pt;"><b>Service</b></td>
	</tr>
	<tr>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="10%" valign="bottom" align="center"><b>Date</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time In</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time Out</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Length <em>(in minutes)</em></b></td>		
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Group Size</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Rate</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="10%" valign="bottom" align="center"><b>Date</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time In</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Time Out</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Length <em>(in minutes)</em></b></td>		
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Group Size</b></td>
		<td style="background-color: #CCC; text-align: center; vertical-align: bottom" width="8%" valign="bottom" align="center"><b>Session Rate</b></td>		
	</tr>
<?php
$cnt = count($billRows);
$perCol = $cnt > 20 ? round($cnt/2) : 10;
$i = 0;
$mins = 0;
$total = 0;
?>

	<tr>
		<td colspan="6" width="50%">
			<table width="100%" border="1">
<?php 		foreach($billRows as $key=>$row):
				if($i==$perCol)
					break;
?>
				<tr>
					<td width="20%" align="center" style="text-align: center;"><?php echo date('m/d/Y',$key);?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_start_time']?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_end_time']?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $m = getTimeDiff($row['speech_session_start_time'],$row['speech_session_end_time']); $mins +=$m;?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_group_size']?></td>
					<td width="16%" align="center" style="text-align: center; background-color: #CCC;">$<?php  $t = round(($rates[$row['speech_session_group_size']]/$row['speech_session_group_size'])/60 * $m,2); $total +=$t; echo $t;  ?></td>					
				</tr>
<?php
				$i++;
				unset($billRows[$key]);
			endforeach;
			for($i;$i<$perCol;$i++):?>
				<tr>
					<td width="20%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center; background-color: #CCC;">&nbsp;</td>					
				</tr>			
<?php
			endfor;
			$i = 0;
?>

			</table>
		</td>
		<td colspan="6" width="50%">
			<table width="100%" border="1">
<?php		foreach($billRows as $key=>$row):
				if($i==$perCol)
					break;
?>
				<tr>
					<td width="20%" align="center" style="text-align: center;"><?php echo date('m/d/Y',$key);?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_start_time']?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_end_time']?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $m = getTimeDiff($row['speech_session_start_time'],$row['speech_session_end_time']); $mins +=$m;?></td>
					<td width="16%" align="center" style="text-align: center;"><?php echo $row['speech_session_group_size']?></td>
					<td width="16%" align="center" style="text-align: center; background-color: #CCC;">$<?php  $t = round(($rates[$row['speech_session_group_size']]/$row['speech_session_group_size'])/60 * $m,2); $total +=$t; echo $t;  ?></td>					
				</tr>
<?php
				$i++;
				unset($billRows[$key]);
			endforeach;
			for($i;$i<$perCol;$i++):?>
				<tr>
					<td width="20%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center;">&nbsp;</td>
					<td width="16%" align="center" style="text-align: center; background-color: #CCC;">&nbsp;</td>					
				</tr>			
<?php
			endfor;
?>
			</table>
		</td>
	</tr>	

</table>
</div>
<p>&nbsp;</p>
<table border="0" width="100%;">
	<tr>
		<td width="35%"><b>Total Session Length (in hours) :</b></td>
		<td width="15%"><?php echo convertMinToHrs($mins);?></td>
		<td width="30%" align="right" style="text-align: right;"><b>Total Amount Due:</b></td>
		<td width="20%"> <?php echo $total;?> </td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4">
			<b>SETSS Direct Service Provider:</b> I certify that I have provided SETSS services on the dates, times, group size and duration indicated herein. I understand that when completed and filed, this form becomes a record of the NYC Department of Education (DOE) and is relied upon by the DOE to make payment and any material misrepresentation may subject me to criminal, civil and/or administrative action.			
		</td>
	</tr>

	<tr>
		<td width="50%"  colspan="2" align="center" style="text-align: center; color: #ccc;"><br />Signature required</td>
		<td width="30%" align="left" style="text-align: left;"><br /><b>Date</b></td>
		<td width="20%"><br /> - </td>
	</tr>
</table>
<p>&nbsp;</p>
<p style="font-size:12px;">
<b>Parent and/or Principal:</b> By my signature, I acknowledge that I have reviewed this billing form and that, to the best of my knowledge, the sessions were provided as indicated	
</p>
<p>&nbsp;</p>
<table border="0" width="100%">
	<tr>
		<td width="55%"><b>Parent (for services other than at school)</b></td>
		<td width="30%"></td>
		<td width="5%"><b>Date</b></td>
		<td width="10%"></td>
	</tr>
	<tr><td colspan="4" style="font-size:5pt;">&nbsp;</td></tr>
	<tr>
		<td width="55%"><b>Principal/Principal's Designee (for services at school)</b></td>
		<td width="30%"></td>
		<td width="5%"><b>Date</b></td>
		<td width="10%"></td>
	</tr>	
</table>
<p>
	&nbsp;
</p>
<table border="0" width="100%" cellpadding="10">
	<tr>
		<td valign="middle" width="70%" align="right" style="text-align: right;">Submit <b>this invoice with original signatures and a copy of the approval letter</b> to:<br>
		the Related Services Supervisor at</td>
	<td width="30%;">
		<b>N.Y.C. Department of Education<br>
Bureau of NonPublic School Payables<br>
65 Court Street – Room 1001<br>
Brooklyn, NY 11201</b>
	</td>
	</tr>
</table>
</div>