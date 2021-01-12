<?php
extract($data);
$base_url = Yii::app()->getBaseUrl('true');
PDF::$base_url = $base_url;
?>
<center><h3>Student Information</h3></center>
<table width="600px;" style="width: 800px !important; margin:  0 auto;" class="table table-bordered">
	<tr>
		<td width="50%" align="right" class="text-right"><b>Name</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_firstname); ?> <?php echo ucfirst($new_student_lastname); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Gender</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_gender); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>DOB</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_dob); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Student ID</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_student_id); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Parent / Guardian</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_parent_guardian); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Contact Person</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_contact_person); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Contact Phone</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_contact_phone); ?></td>
	</tr>
	<tr>
		<td width="50%" align="right" class="text-right"><b>Address</b></td>
		<td width="50%" align="left" class="text-left"><?php echo ucfirst($new_student_address); ?></td>
	</tr>	
</table>