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

$cnt = count($billRows);
?>
<style>
	.dotted td{
		border:1px dotted #333333;
	}
</style>
<div style="font-family: serif; font-size:10pt">
<table width="700px" boder="0">
	<tr>
		<td width="100px"><img border="0" width="80px" height="78px" src="<?php echo $base_url?>/img/templates/cpse_logo_small.jpg"/></td>
		<td width="250px">
			<b>New York City Department of Education</b><br>
			<b style="text-decoration:underline;">JOEL I. KLEIN, Chancellor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b><br>
			Division of Financial Operations- Bureau of Non Public School Payables<br>
			Billing Form for Preschool Related Service Providers
		</td>
		<td width="150px">
			<b>Vendor Invoice #</b><br><br> 
			<p style="text-align:right;"><b>Month</b>  <?php echo $month;?>   &nbsp;&nbsp;&nbsp;<b>Year</b>  <?php echo $year;?>    &nbsp;&nbsp;</p>
		</td>
	</tr>
</table>
<table width="100%" border="0">
	<tr>
		<td width="49%">
			<table width="100%">
				<tr>
					<td><span style="font-size:13pt">Section 1: Student Information</span></td>
				</tr>
				<tr>
					<td  width="100%">Student's Name: <span style="font-size:12pt; padding-left:10px;"><?php echo $student->lastname;?> <?php echo $student->firstname;?></span> </td> 
				</tr>
				<tr>
					<td>NYC ID #:  <span style="font-size:12pt; padding-left:10px;"><?php echo $student->student_id;?></span> </td>
				</tr>
				<tr>
					<td>Date of Birth: <span style="font-size:12pt; padding-left:2pt; margin-left:1pt;"><?php echo $student->dob; ?></span> Home District:<span style="font-size:12pt; padding-left:2pt; margin-left:1pt;"><?php echo $student->service_district; ?></span> </td>
				</tr>
				<tr>
					<td>Related Service: &nbsp;&nbsp;&nbsp;&nbsp;<?php echo implode(', ',$services);?></td>
				</tr>
				<tr>
					<td>Recommendation on IEP:</td>
				</tr>
				<tr>
					<td>
						Frequency:<span><?php echo $frequency,$split; ?></span> Duration:<span><?php echo $duration; ?></span> Group Size: <span>___</span> Lang.:<span><?php echo implode(', ', $lngs)?></span>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;<br>
						<table width="100%" >
							<tr>
								<td width="10%">
									<span style="font-size:14pt;">(&nbsp;&nbsp;)</span>
								</td>
								<td width="90%">
									Check here if student was assigned to you/agency by CPSE after being
									selected from the NYC Municipality List of Approved Preschool Related
									Service Providers
								</td>
							</tr>
							<tr>
								<td width="10%"><b>OR</b></td>
								<td width="90%"></td>
							</tr>
							<tr>
								<td width="10%"><span style="font-size:14pt;">(&nbsp;&nbsp;)</span></td>
								<td width="90%">Check here if student was assigned to your agency as a result of being awarded
									the related service contract through the RFP process.
								</td>
							</tr>
						</table>
						&nbsp;<br>
					</td>
				</tr>
				<tr>
					<td>Contract #:</td>
				</tr>
				<tr>
					<td>Location Where Services are Provided: <?php echo implode(', ',$locations);?></td>
				</tr>
				<tr>
					<td>Comments:</td>
				</tr>
			</table>
		</td>
		<td width="2%">&nbsp;</td>
		<td width="49%">
			<table>
				<tr>
					<td><span style="font-size:13pt">Section 2: Provider Information</span></td>
				</tr>
				<tr>
					<td>
						<table width="100%">
							<tr><td widht="20%">Provider's<br>Name</td><td width="80%"><span style="font-size:12pt;"><?php echo $provider->getFullName(); ?></span></td></tr>
							<tr><td width="20%">Address:</td><td width="80%"><span style="font-size:12pt;"><?php echo $provider->getFullAddress(); ?></span><br></td></tr>
					</table></td>
				</tr>
				<tr><td>
					S.S.#(required):<span style="font-size:12pt;"><?php echo $provider->ss_id; ?></span>
				</td></tr>
				<tr><td>Telephone:</td></tr>
				<tr>
					<td><span style="font-size:13pt">Section 3: AGENCY INFORMATION</span></td>
				</tr>
				<tr>
					<td>Name: <span style="font-size:12pt;"><?php echo $agency->name; ?></span></td>
				</tr>
				<tr>
					<td>Address:<span style="font-size:12pt;"><?php echo $agency->getFullAddress(); ?></span><br></td>
				</tr>
				<tr>
					<td>
						<table width="100%">
							<tr>
								<td width="18%">Telephone</td>
								<td width="27%"><span style="font-size:12pt;"><?php echo $agency->phone; ?></span></td>
								<td width="10%">email</td>
								<td width="45%"><span style="font-size:12pt;"><?php echo $agency->email; ?></span></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						Agency Rep(print name)
					</td>
				</tr>
				<tr>
					<td>
						<br>
						Fed.Tax ID: <span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $agency->tax_id; ?></span>
					</td>
				</tr>
			</table>			
		</td>
	</tr>
</table>
<br>
<p style="font-size:13pt">Section 4: Service Provision</p>
<table border="0" cellpadding="2">
	<tr>
		<td width="8%" valign="bottom" style="vertical-align:bottom;">DATE</td>
		<td width="8%" valign="bottom">RCV<br>Group<br>Size</td>
		<td width="8%" valign="bottom">Start<br>Time</td>
		<td width="8%" valign="bottom">End<br>Time</td>
		<td width="18%" valign="bottom">Signature of parent/Principal or designee verifying that service has actually been provided at the times indicated</td>
		<td width="8%" valign="bottom">DATE</td>
		<td width="8%" valign="bottom">RCV<br>Group<br>Size</td>
		<td width="8%" valign="bottom">Start<br>Time</td>
		<td width="8%" valign="bottom">End<br>Time</td>
		<td width="18%" valign="bottom">Signature of parent/Principal or designee verifying that service has actually been provided at the times indicated</td>		
	</tr>
</table>
<table border="1" width="100%">
	<tr>
		<td width="50%">
			<table width="100%" class="dotted">
<?php
			for($i=1;$i<=16;$i++):
				if($row = findDayFromRows($billRows,$i))
				{?>
				<tr>
					<td width="16%" style="text-align:center" align="center"><b><?php echo $i;?></b></td>
					<td width="16%"><?php echo $row['speech_session_group_size'];?></td>
					<td width="16%"><?php echo $row['speech_session_start_time']?></td>
					<td width="16%"><?php echo $row['speech_session_end_time']?></td>
					<td width="36%">&nbsp;</td>
				</tr>
<?php				
				}else{
				?>
				<tr>
					<td width="16%" style="text-align:center" align="center"><b><?php echo $i;?></b></td>
					<td width="16%">&nbsp;</td>
					<td width="16%">&nbsp;</td>
					<td width="16%">&nbsp;</td>
					<td width="36%">&nbsp;</td>
				</tr>
<?php
				}
			endfor;			
?>
			</table>
		</td>
		<td width="50%">
			<table width="100%" class="dotted">
<?php
			for($i=17;$i<=31;$i++):
				if($row = findDayFromRows($billRows,$i))
				{?>
				<tr>
					<td width="16%" style="text-align:center" align="center"><b><?php echo $i;?></b></td>
					<td width="16%"><?php echo $row['speech_session_group_size'];?></td>
					<td width="16%"><?php echo $row['speech_session_start_time']?></td>
					<td width="16%"><?php echo $row['speech_session_end_time']?></td>
					<td width="36%">&nbsp;</td>
				</tr>
<?php				
				}else{
				?>
				<tr>
					<td width="16%" style="text-align:center" align="center"><b><?php echo $i;?></b></td>
					<td width="16%">&nbsp;</td>
					<td width="16%">&nbsp;</td>
					<td width="16%">&nbsp;</td>
					<td width="36%">&nbsp;</td>
				</tr>
<?php
				}
			endfor;			
?>
				<tr>
					<td width="16%" style="text-align:center" align="center"></td>
					<td width="16%">&nbsp;</td>
					<td width="16%">&nbsp;</td>					
					<td width="16%">&nbsp;</td>
					<td width="36%">&nbsp;</td>
				</tr>
			</table>			
		</td>
	</tr>
</table>
<p style="font-size:13pt"><b>Section 5: Certification for the Provision of Services:</b></p>
<table>
	<tr>
		<td width="45%">
			I hereby certify that I have served in the Related Service Program 
on the dates and for the duration indicated herein. I understand that
any material misrepresentation of fact provided by me on this form 
may result in criminal action.
		<p> &nbsp;</p>
		<p> &nbsp;</p>
				<p> &nbsp;</p>
		</td>
		<td width="10%"></td>
		<td width="45%">
			<table width="100%">
				<tr>
					<td>Total # of Sessions:</td>
					<td><?php echo $cnt;?></td>
					<td>Rate:</td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr>
					<td>Total Amount Due:</td>
					<td colspan="2">&nbsp;</td>
				</tr>
			</table>
				
		</td>
	</tr>
	<tr>
		<td width="45%">
			<table width="100%">
				<tr>
					<td width="70%" style="border-top:1px solid #333; text-align: center">
						Signature of Provider<small>(original)</small>
					</td>
					<td width="30%" style="border-top:1px solid #333; text-align: center">
						Date
					</td>
				</tr>
			</table>			
		</td>
		<td widht="10%"></td>
		<td width="45%">
			<table width="100%">
				<tr>
					<td width="80%" style="border-top:1px solid #333; text-align: center">
						Signature of Agency/School Representative<small>(original)</small>
					</td>
					<td width="20%" style="border-top:1px solid #333; text-align: center">
						Date
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<p>&nbsp;</p>
<b>*The DOE will only accept Billing Forms that have instructions for completion on the reverse side</b>
</div>


<?php
function findDayFromRows($rows,$day)
{
	foreach($rows as $key=>$row)
	{
		if(date('j',$key)==$day)
			return $row;
	}
	return false;
}

?>








