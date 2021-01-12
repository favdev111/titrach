<?php extract($data);
$agency = Agency::model()->findByPk($bill_agency);

$provider = Provider::model()->findByPk($bill_provider);
if($provider)
	$provider_name = $provider->getFullName();
	
$student = Patient::model()->findByPk($bill_patient);

$frequency = ' - ';
$duration = ' - ';
$split = '';

if($mandate){
	$frequency = $mandate->frequency;
	$duration = $mandate->duration;
	$split = $mandate->split ? ' split' : '';	
}
elseif(isset($student->mandates[$bill_service_type]))
{
	$frequency = $student->mandates[$bill_service_type]->frequency;
	$duration = $student->mandates[$bill_service_type]->duration;
	$split = $student->mandates[$bill_service_type]->split ? ' split' : '';
}



$base_url = Yii::app()->getBaseUrl('true');
PDF::$base_url = $base_url;
PDF::$pageMargin = array(
	'setLeftMargin'=>0.5,
	'setRightMargin'=>0.5,
	'setTopMargin'=>0.5,
	'setFooterMargin'=>0.5,
);
$date = DateTime::createFromFormat('m/d/Y',$bill_date);
$month = '';
$year ='';
if($date)
{
	$month = $date->format('F');
	$year = $date->format('Y');
}
?>
<div style="font-family:'Times New Roman', Times, Baskerville, Georgia, serif; font-size:10pt">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="34%">
			<img src="<?php echo $base_url?>/img/templates/cse_logo.jpg" width="100" height="58"/>
		</td>
		<td width="75%" style="text-align: left; font-style: italic; color: #0000ff">
			Division of Financial Operations <br>
			Non Public School Payables<br>
			65 Court Street, room 1503<br>
			Brooklyn, NY 11201<br>
		</td>
	</tr>
</table>
<h3 style="text-align: center; margin: 20px 0;">Independent Providers of Related Service Billing Form: RSA -7A</h3>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="75%" align="right" style="text-align: right;"><b>Month:</b></td>
		<td width="10%" style="text-align: center;" align="center"><?php echo $month;?></td>
		<td width="10%" style="text-align: right;"><b>Year:</b></td>
		<td width="5%" style="text-align: center;" align="center"><?php echo $year;?></td>
	</tr>
</table>
<table width="100%" border="1" cellpadding="2" cellspacing="0">
	<tr>
		<td style="background-color: #99ccff; font-size:13pt;padding-left: 10px;">
			Section 1: Students Information
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="2">
				<tr>
					<td width="20%" style="text-align: left;"><b>Students Name:</b></td>
					<td width="40%"><?php echo $student->getFullName(); ?> </td>
					<td width="15%" style="text-align: right;"><b>Date of Birth:</b></td>
					<td width="15%"><?php echo $student->dob;  ?></td>
				</tr>
			</table>
			<table width="100%" border="0" cellpadding="2">
				<tr>
					<td width="15%" style="text-align: left;"><b>NYC ID #:</b></td>
					<td width="15%"><?php echo $student->student_id; ?> </td>
					<td width="15%" style="text-align: right;"><b>Service District:</b></td>
					<td width="5%"><?php echo $student->service_district; ?></td>
					<td width="15%"><b>Related Service:</b></td>
					<td width="30%"><?php echo implode(', ',$services)?></td>
				</tr>
			</table>
			<p style="font-size:13pt; padding-left:5px;">Recommendation on IEP:</p>
			<table  width="100%" border="0" cellpadding="2">
				<tr>
					<td width="15%"><b>Hourly Rate:</b></td>
					<td width="5%"></td>
					<td width="10%"><b>Frequency:</b></td>
					<td width="7%"><?php echo $frequency,$split; ?></td>
					<td width="15%"><b>Duration:</b></td>
					<td width="5%"><?php echo $duration; ?></td>
					<td width="10%"><b>Group Size:</b></td>
					<td width="13%"><?php echo implode(', ',$size)?></td>
					<td width="10%"><b>Language:</b></td>
					<td width="10%"><?php echo implode(', ',$lngs)?></td>					
				</tr>
			</table>
			<table  width="100%" border="0" cellpadding="2">
				<tr>
					<td width="30%">Location of Services Provided<br>
					<span style="font-size:8pt;">Home, School or Place of Buisness</span>	
					</td>
					<td width="70%"><?php echo implode(', ',$locations)?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p>&nbsp;</p>
<table width="100%" border="1" cellpadding="2" cellspacing="0">
	<tr>
		<td style="background-color: #99ccff; font-size:13pt;padding-left: 10px;">
			Section 2: Provider Information
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="2">
				<tr>
					<td width="20%" style="text-align: left;"><b>Providers Name:</b></td>
					<td width="30%"><?php echo $provider->getFullName(); ?> </td>
					<td width="20%" style="text-align: right;"><b>Social Security #:</b></td>
					<td width="25%"><?php echo $provider->ss_id;  ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3"><?php echo $provider->getFullAddress();?></td>
				</tr>
				<tr>
					<td width="20%" style="text-align: left;"><b>Telephone # :</b></td>
					<td width="30%"><?php echo $provider->phone;?> </td>
					<td width="20%" style="text-align: right;"><b>E- Mail Address:</b></td>
					<td width="25%"><?php echo $provider->email;  ?></td>
				</tr>				
			</table>
		</td>
	</tr>
</table>
<p>&nbsp;</p>
<table width="100%" border="1" cellpadding="2" cellspacing="0">
	<tr>
		<td style="background-color: #99ccff; font-size:13pt;padding-left: 10px;">
			Section 3: Agency Information
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="2">
				<tr>
					<td width="20%" style="text-align: left;"><b>Agency Name:</b></td>
					<td width="30%"><?php echo $agency->name; ?> </td>
					<td width="20%" style="text-align: right;"><b>Federal Tax ID #:</b></td>
					<td width="25%"><?php echo $agency->tax_id;  ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3"><?php echo $agency->getFullAddress();?></td>
				</tr>
				<tr>
					<td width="20%" style="text-align: left;"><b>Telephone # :</b></td>
					<td width="30%"><?php echo $agency->phone;?> </td>
					<td width="20%" style="text-align: right;"><b>E- Mail Address:</b></td>
					<td width="25%"><?php echo $agency->email;  ?></td>
				</tr>				
			</table>
		</td>
	</tr>
</table>
<p>&nbsp;</p>
<table width="100%" border="1" cellpadding="2" cellspacing="0">
	<tr>
		<td colspan="5" style="background-color: #99ccff; font-size:13pt;padding-left: 10px;">
			Section 4: Service Provision
		</td>
	</tr>
	<tr>
		<td aling="center" style="text-align: center;"><b>Date</b></td>
		<td aling="center" style="text-align: center;"><b>Frequency</b></td>
		<td aling="center" style="text-align: center;"><b>Start Time</b></td>
		<td aling="center" style="text-align: center;"><b>End Time</b></td>
		<td aling="center" style="text-align: center;"><b>Group Size</b></td>
	</tr>
	
<?php

	$i = 0;
	foreach($billRows as $key=>$row):?>
<tr>
	<td aling="center" style="text-align: center;"><?php echo date('m/d/Y',$key);?></td>
	<td aling="center" style="text-align: center;"><?php echo $frequency,'x',$duration; ?></td>	
	<td aling="center" style="text-align: center;"><?php echo $row['speech_session_start_time']?></td>
	<td aling="center" style="text-align: center;"><?php echo $row['speech_session_end_time']?></td>
	<td aling="center" style="text-align: center;"><?php echo $row['speech_session_group_size']?></td>
</tr>
	
<?php
		$i++;
	endforeach;
	for($i;$i<9;$i++):
?>	
	<tr>
		<td aling="center" style="text-align: center;">&nbsp;</td>
		<td aling="center" style="text-align: center;">&nbsp;</td>
		<td aling="center" style="text-align: center;">&nbsp;</td>
		<td aling="center" style="text-align: center;">&nbsp;</td>
		<td aling="center" style="text-align: center;">&nbsp;</td>		
	</tr>
<?php endfor; ?>	
	<tr>
		<td colspan="5">
			<table width="100%">
				<tr>
					<td>Total # of  Sessions:</td>
					<td><?php echo count($billRows);?></td>
					<td>Rate</td>					
					<td></td>
					<td>Total Amount Due:</td>
					<td></td>					
				</tr>
			</table>
		</td>
	</tr>	
</table>
<p>&nbsp;</p>
<table width="100%" border="1" cellpadding="2" cellspacing="0">
	<tr>
		<td style="background-color: #99ccff; font-size:13pt;padding-left: 10px;">
			Section 5: Certification
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellpadding="2">
				<tr>
					<td width="48%" valing="top" style="text-align: left; vertical-align: top;">I hereby certify that I have provided related services on the dates for the
duration indicated herein. I understand that when completed and filed, this
form becomes a record of the Board of Education and that any material
misrepresentation may subject me to criminal, civil and/or administrative
action.</td>
					<td width="4%"></td>
					<td width="48%" valing="top" style="text-align: left; vertical-align: top;">By my signature I acknowledge that I have reviewed this Related
Service billing form and that, to the best of my knowledge, these
sessions were provided as indicated.</td>
				</tr>
				<tr>
					<td>
						<p>&nbsp;</p>
						<table width="100%">
							<tr>
								<td width="70%" style="border-top:1px solid #333; text-align: center">
									Signature of Provider
								</td>
								<td width="30%" style="border-top:1px solid #333; text-align: center">
									Date
								</td>
							</tr>
						</table>
					</td>
					<td>
						&nbsp;
					</td>
					<td>
<p>&nbsp;</p>
						<table width="100%">
							<tr>
								<td width="70%" style="border-top:1px solid #333; text-align: center">
									Signature of Parent/Guardian/Principal
								</td>
								<td width="30%" style="border-top:1px solid #333; text-align: center">
									Date
								</td>
							</tr>
						</table>						
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<p>
<small>Revised 5/20/2009<br>
K.D.Q</small>
</p>